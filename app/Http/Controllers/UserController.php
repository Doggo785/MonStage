<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = auth()->user(); // Récupère l'utilisateur connecté

        if ($currentUser->role->Libelle === 'Pilote') {
            // Si l'utilisateur est un pilote, afficher uniquement les étudiants
            $users = User::with('role')->whereHas('role', function ($query) {
                $query->where('Libelle', 'Étudiant');
            })->get();
        } else {
            // Sinon, afficher tous les utilisateurs
            $users = User::with('role')->get();
        }

        $roles = Role::all(); // Récupère tous les rôles

        return view('dashboard.users.index', compact('users', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $currentUser = auth()->user();

        // Vérifie si l'utilisateur connecté est un administrateur ou un pilote
        if (!in_array($currentUser->role->Libelle, ['Administrateur', 'Pilote'])) {
            return redirect()->route('users.index')->with('error', 'Vous n\'avez pas la permission de modifier cet utilisateur.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|exists:Role,ID_Role',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::findOrFail($id);

        // Si l'utilisateur connecté est un pilote, empêcher la modification du rôle
        if ($currentUser->role->Libelle === 'Pilote') {
            $validated['role'] = $user->ID_Role; // Forcer le rôle actuel
        }

        // Mise à jour des données utilisateur
        $user->Nom = $validated['name'];
        $user->Prenom = $validated['prenom'];
        $user->Email = $validated['email'];
        $user->Telephone = $validated['telephone'];
        $user->ID_Role = $validated['role'];

        // Gestion de la photo de profil
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->pfp_path = $path;
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Utilisateur modifié avec succès.');
    }

    public function destroy($id)
    {
        $currentUser = auth()->user();

        // Vérifie si l'utilisateur connecté est un administrateur ou un pilote
        if (!in_array($currentUser->role->Libelle, ['Administrateur', 'Pilote'])) {
            return redirect()->route('users.index')->with('error', 'Vous n\'avez pas la permission de supprimer cet utilisateur.');
        }

        $user = User::findOrFail($id);

        // Supprimer les enregistrements associés dans la table Wishlist
        $user->wishlists()->delete();

        // Supprimer les enregistrements associés dans la table Avis
        $user->avis()->delete();

        // Supprimer l'utilisateur
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search'); // Récupère la valeur de recherche

        // Recherche dans les colonnes 'Nom', 'Prenom', 'Email', et 'role.Libelle'
        $users = User::where('Nom', 'LIKE', "%{$search}%")
            ->orWhere('Prenom', 'LIKE', "%{$search}%")
            ->orWhere('Email', 'LIKE', "%{$search}%")
            ->orWhereHas('role', function ($query) use ($search) {
                $query->where('Libelle', 'LIKE', "%{$search}%");
            })
            ->with('role') // Charge les relations pour éviter les requêtes supplémentaires
            ->get();

        $roles = Role::all(); // Récupère tous les rôles pour les formulaires

        return view('dashboard.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();

        // Vérifie si l'utilisateur connecté est un pilote
        if ($currentUser->role->Libelle === 'Pilote') {
            // Forcer le rôle à "Étudiant" pour les pilotes
            $request->merge(['role' => Role::where('Libelle', 'Étudiant')->first()->ID_Role]);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:Utilisateur,Email',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|exists:Role,ID_Role',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = new User();
        $user->Nom = $validatedData['name'];
        $user->Prenom = $validatedData['prenom'];
        $user->Email = $validatedData['email'];
        $user->Telephone = $validatedData['telephone'];
        $user->ID_Role = $validatedData['role'];
        $user->password = Hash::make('DefaultPassword!'); // Mot de passe par défaut

        // Gestion de la photo de profil
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->pfp_path = $path;
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);

        return view('dashboard.users.show', compact('user'));
    }

    public function showResetPasswordForm()
    {
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('home')->with('success', 'Votre mot de passe a été mis à jour avec succès.');
    }
}