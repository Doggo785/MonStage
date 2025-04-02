<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Entreprise;
use App\Models\Ville;

class EntrepriseController extends Controller
{

    public function index()
    {
        $entreprises = Entreprise::with('ville')->get();
        $villes = Ville::all(); // Récupère toutes les villes pour le formulaire
        return view('entreprises.index', compact('entreprises', 'villes'));
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        // Rechercher les entreprises par nom, ville ou titre
        $entreprises = Entreprise::where('Nom', 'like', "%$search%")
            ->orWhereHas('ville', function ($query) use ($search) {
                $query->where('Nom', 'like', "%$search%");
            })
            ->orWhere('Description', 'like', "%$search%")
            ->get();

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

    public function store(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'Nom' => 'required|string|max:255',
            'Ville' => 'required|exists:Ville,ID_Ville', // Vérifie que l'ID de la ville existe
            'Telephone' => 'required|string|max:20',
            'Email' => 'required|email|max:255',
            'Site' => 'nullable|url|max:255',
            'Description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gestion de l'image
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Stocke l'image dans le dossier 'entreprises' du disque 'public'
            $imagePath = $request->file('image')->store('entreprises', 'public');
        }

        // Création de l'entreprise
        Entreprise::create([
            'Nom' => $validatedData['Nom'],
            'ID_Ville' =>$validatedData['Ville'], 
            'Email' => $validatedData['Email'],
            'Site' => $validatedData['Site'],
            'Description' => $validatedData['Description'],
            'pfp_path' => $imagePath, // Stocke uniquement le chemin relatif
        ]);

        // Redirection avec un message de succès
        return redirect()->route('entreprises.index')->with('success', 'Entreprise ajoutée avec succès.');
    }

    public function update(Request $request, $id)
    {
        $entreprise = Entreprise::findOrFail($id);

        // Validation des données
        $validatedData = $request->validate([
            'Nom' => 'required|string|max:255',
            'Ville' => 'required|exists:Ville,ID_Ville',
            'Telephone' => 'required|string|max:20',
            'Email' => 'required|email|max:255',
            'Site' => 'nullable|url|max:255',
            'Description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gestion de l'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($entreprise->pfp_path && Storage::disk('public')->exists($entreprise->pfp_path)) {
                Storage::disk('public')->delete($entreprise->pfp_path);
            }

            // Stocker la nouvelle image
            $imagePath = $request->file('image')->store('entreprises', 'public');
            $entreprise->pfp_path = $imagePath;
        }

        // Mise à jour des données (correction ici : utiliser "ID_Ville" au lieu de "Ville_id")
        $entreprise->update([
            'Nom' => $validatedData['Nom'],
            'ID_Ville' => $validatedData['Ville'],
            'Telephone' => $validatedData['Telephone'],
            'Email' => $validatedData['Email'],
            'Site' => $validatedData['Site'],
            'Description' => $validatedData['Description'],
        ]);

        return redirect()->route('entreprises.index')->with('success', 'Entreprise mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $entreprise = Entreprise::findOrFail($id);

        // Supprimer l'image associée si elle existe
        if ($entreprise->pfp_path && Storage::disk('public')->exists($entreprise->pfp_path)) {
            Storage::disk('public')->delete($entreprise->pfp_path);
        }

        // Supprimer l'entreprise
        $entreprise->delete();

        return redirect()->route('entreprises.index')->with('success', 'Entreprise supprimée avec succès.');
    }
}