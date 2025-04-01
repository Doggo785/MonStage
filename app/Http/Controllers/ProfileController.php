<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function updatePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Supprimer l'ancienne photo de profil si elle existe
        if ($user->pfp_path) {
            Storage::delete('public/' . $user->pfp_path);
        }

        // Générer un nom unique pour le fichier
        $filename = 'profile_' . $user->ID_User . '_' . time() . '.' . $request->file('profile_picture')->getClientOriginalExtension();

        // Stocker la nouvelle photo de profil
        $path = $request->file('profile_picture')->storeAs('profile_pictures', $filename, 'public');

        // Mettre à jour le chemin de la photo de profil dans la base de données
        $user->pfp_path = $path;
        $user->save();

        return redirect()->back()->with('success', 'Photo de profil mise à jour avec succès.');
    }
}