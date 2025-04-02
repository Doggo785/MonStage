<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get(); // Récupère tous les utilisateurs avec leurs rôles
        return view('dashboard.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with('role')->findOrFail($id); // Récupère un utilisateur spécifique
        return view('dashboard.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id); // Récupère un utilisateur spécifique
        return view('dashboard.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nom' => 'required|string|max:255',
            'Prenom' => 'required|string|max:255',
            'Email' => 'required|email|max:255',
            'Telephone' => 'nullable|string|max:20',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only(['Nom', 'Prenom', 'Email', 'Telephone']));

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}