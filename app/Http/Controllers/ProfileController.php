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

        // Delete the old profile picture if it exists
        if ($user->pfp_path) {
            Storage::delete('public/' . $user->pfp_path);
        }

        // Store the new profile picture
        $path = $request->file('profile_picture')->store('profile_pictures', 'public');

        // Update the user's profile picture path
        $user->pfp_path = $path;
        $user->save();

        return redirect()->back()->with('success', 'Photo de profil mise à jour avec succès.');
    }
}