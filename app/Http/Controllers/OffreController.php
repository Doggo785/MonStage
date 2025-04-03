<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\Entreprise;
use App\Models\Region;
use App\Models\Secteur;
use App\Models\Ville;
use Illuminate\Http\Request;

class OffreController extends Controller
{
    // Liste des offres
    public function index(Request $request)
    {
        $query = Offre::query();

        // Si l'utilisateur est connecté et est un administrateur ou pilote
        if (auth()->check() && (auth()->user()->role->Libelle === 'Pilote' || auth()->user()->role->Libelle === 'Administrateur')) {
            $query->where(function ($q) {
                $q->where('Etat', 1)->orWhere('Etat', 0); // Inclut toutes les offres
            });
        } else {
            // Sinon, afficher uniquement les offres actives (Etat = 1)
            $query->where('Etat', 1);
        }

        // Recherche par titre, entreprise ou ville
        if ($search = $request->input('search')) {
            $query->where('Titre', 'LIKE', "%{$search}%")
                  ->orWhereHas('entreprise', function ($q) use ($search) {
                      $q->where('Nom', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('ville', function ($q) use ($search) {
                      $q->where('Nom', 'LIKE', "%{$search}%");
                  });
        }

        // Filtrer par entreprise
        if ($entreprise = $request->input('entreprise')) {
            $query->where('ID_Entreprise', $entreprise);
        }

        // Filtrer par région
        if ($region = $request->input('region')) {
            $query->whereHas('ville', function ($q) use ($region) {
                $q->where('ID_Region', $region);
            });
        }

        $offres = $query->with(['competences', 'entreprise', 'ville'])->paginate(6);
        $entreprises = Entreprise::all();
        $regions = Region::all();

        return view('offres.index', compact('offres', 'entreprises', 'regions'));
    }

    // Créer une nouvelle offre (formulaire)
    public function create()
    {
        $entreprises = Entreprise::all();
        $secteurs = Secteur::all();
        $villes = Ville::all();

        return view('offres.create', compact('entreprises', 'secteurs', 'villes'));
    }

    // Enregistrer une nouvelle offre
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'remuneration' => 'required|numeric|min:600',
            'date_publication' => 'required|date',
            'date_expiration' => 'required|date|after:date_publication',
            'entreprise' => 'required|integer',
            'secteur' => 'required|integer',
            'ville' => 'required|integer',
            'competences' => 'nullable|string', // Les compétences sont optionnelles
        ]);

        $offre = Offre::create([
            'Titre' => $validated['titre'],
            'Description' => $validated['description'],
            'Remuneration' => $validated['remuneration'],
            'Date_publication' => $validated['date_publication'],
            'Etat' => true,
            'Date_expiration' => $validated['date_expiration'],
            'ID_Entreprise' => $validated['entreprise'],
            'ID_Secteur' => $validated['secteur'],
            'ID_Ville' => $validated['ville'],
        ]);

        // Associer les compétences à l'offre
        if (!empty($validated['competences'])) {
            $competenceIds = explode(',', $validated['competences']);
            $offre->competences()->sync($competenceIds);
        }

        return redirect()->route('offres.index')->with('success', 'Offre créée avec succès !');
    }

    // Détails d'une offre
    public function show($id)
    {
        $offre = Offre::with(['competences', 'entreprise', 'ville'])->findOrFail($id);

        // Vérifie si l'offre est désactivée et si l'utilisateur n'est pas autorisé
        if ($offre->Etat == 0 && (!auth()->check() || !(auth()->user()->role->Libelle === 'Pilote' || auth()->user()->role->Libelle === 'Administrateur'))) {
            return redirect()->route('login');
        }

        return view('offres.show', compact('offre'));
    }

    // Modifier une offre (formulaire)
    public function edit($id)
    {
        $offre = Offre::findOrFail($id);
        $entreprises = Entreprise::all();
        $secteurs = Secteur::all();
        $villes = Ville::all();

        return view('offres.edit', compact('offre', 'entreprises', 'secteurs', 'villes'));
    }

    // Mettre à jour une offre
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'remuneration' => 'required|numeric|min:600',
            'date_publication' => 'required|date',
            'date_expiration' => 'required|date|after:date_publication',
            'entreprise' => 'required|integer',
            'secteur' => 'required|integer',
            'ville' => 'required|integer',
            'competences' => 'nullable|string', // Les compétences sont optionnelles
        ]);

        $offre = Offre::findOrFail($id);

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

        // Mettre à jour les compétences associées
        if (!empty($validated['competences'])) {
            $competenceIds = explode(',', $validated['competences']);
            $offre->competences()->sync($competenceIds);
        } else {
            $offre->competences()->detach(); // Supprime toutes les compétences si aucune n'est sélectionnée
        }

        return redirect()->route('offres.index')->with('success', 'Offre mise à jour avec succès !');
    }

    // Supprimer une offre (changer son état)
    public function destroy($id)
    {
        $offre = Offre::findOrFail($id);
        $offre->Etat = 0;
        $offre->save();

        return redirect()->route('offres.index')->with('success', 'Offre supprimée avec succès.');
    }

    // Postuler à une offre
    public function apply(Request $request, $id)
    {
        $offre = Offre::findOrFail($id);

        $validated = $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'motivation' => 'required|string|max:1000',
        ]);

        // Logique pour enregistrer la candidature ou envoyer un email

        return redirect()->route('offres.show', ['id' => $id])->with('success', 'Votre candidature a été envoyée avec succès.');
    }
}