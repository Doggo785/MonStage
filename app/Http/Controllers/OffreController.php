<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\Entreprise;
use App\Models\Secteur;
use App\Models\Ville;
use Illuminate\Http\Request;

class OffreController extends Controller
{
    public function update(Request $request, $id)
    {
        // Validation des données
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'remuneration' => 'required|numeric|min:600',
            'date_publication' => 'required|date',
            'date_expiration' => 'required|date|after:date_publication',
            'entreprise' => 'required|integer|exists:Entreprise,ID_Entreprise',
            'secteur' => 'required|integer|exists:Secteur,ID_Secteur',
            'ville' => 'required|integer|exists:Ville,ID_Ville',
        ]);

        // Recherche de l'offre
        $offre = Offre::findOrFail($id);

        // Mise à jour des données de l'offre
        $offre->update([
            'Titre' => $validated['titre'],
            'Description' => $validated['description'],
            'Remuneration' => $validated['remuneration'],
            'Date_publication' => $validated['date_publication'],
            'Date_expiration' => $validated['date_expiration'],
            'ID_Entreprise' => $validated['entreprise'],
            'ID_Secteur' => $validated['secteur'],
            'ID_Ville' => $validated['ville'],
        ]);

        // Redirection avec un message de succès
        return redirect()->route('offres.index')->with('success', 'Offre mise à jour avec succès.');
    }
}