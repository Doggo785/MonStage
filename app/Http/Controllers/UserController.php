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
        $users = User::with('role')->get(); // Récupère les utilisateurs avec leurs rôles
        $roles = Role::all(); // Récupère tous les rôles

        return view('dashboard.users.index', compact('users', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|exists:Role,ID_Role',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::findOrFail($id);

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
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:Utilisateur,email',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|exists:Role,ID_Role',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        User::create([
            'Nom' => strtoupper($request->input('name')),
            'Prenom' => ucfirst(strtolower($request->input('prenom'))),
            'Email' => $request->input('email'),
            'Telephone' => $request->input('telephone'),
            'ID_Role' => $request->input('role'),
            'pfp_path' => $profilePicturePath,
            'Password' => Hash::make('DefaultPassword!'), // Mot de passe par défaut
        ]);

// Redirection avec un message de succès
        return redirect()->route('users.index')->with('success', 'Utilisateur ajouté avec succès.');
    }

    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);

        return view('dashboard.users.show', compact('user'));
    }
}