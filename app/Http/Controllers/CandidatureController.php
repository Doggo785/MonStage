<?php

namespace App\Http\Controllers;


class CandidatureController extends Controller
{
    /**
     * Display a listing of the user's candidatures.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $candidatures = $user->candidatures()->with(['offre.entreprise', 'offre.Ville.region', 'statut'])->paginate(10);

        return view('dashboard.Candidature.index', compact('candidatures'));
    }
}