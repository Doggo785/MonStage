<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Entreprise;

class EntrepriseController extends Controller
{

    public function index()
    {
        $entreprises = Entreprise::with('ville')->paginate(6);
        return view('entreprises.index', compact('entreprises'));
    }


    public function updatePicture(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $entreprise = Entreprise::findOrFail($id);

        // Supprimer l'ancienne photo de l'entreprise si elle existe
        if ($entreprise->pfp_path) {
            \Log::info('Chemin de l\'image de l\'entreprise : ' . $entreprise->pfp_path);
            if (Storage::disk('public')->exists($entreprise->pfp_path)) {
                Storage::disk('public')->delete($entreprise->pfp_path);
                \Log::info('Ancienne image supprimée : ' . $entreprise->pfp_path);
            } else {
                \Log::warning('Fichier introuvable pour suppression : ' . $entreprise->pfp_path);
            }
        }

        // Générer un nom unique pour le fichier
        $filename = 'entreprise_' . $entreprise->ID_Entreprise . '_' . time() . '.' . $request->file('image')->getClientOriginalExtension();

        // Stocker la nouvelle image
        $path = $request->file('image')->storeAs('entreprise_images', $filename, 'public');

        // Mettre à jour le chemin de l'image dans la base de données
        $entreprise->pfp_path  = 'entreprise_images/' . $filename; // Stockez uniquement le chemin relatif
        $entreprise->save();

        return redirect()->back()->with('success', 'Photo de l\'entreprise mise à jour avec succès.');
    }
}