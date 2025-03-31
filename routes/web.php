<?php

use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckAdminOrPilote;
use App\Http\Middleware\CheckPilote;
use App\Http\Middleware\CheckStudent;
use App\Models\Ville;
use Illuminate\Support\Facades\Route;
use App\Models\Offre; // Assurez-vous d'importer le modèle Offre
use App\Models\Entreprise;
use App\Models\Secteur;
use Illuminate\Http\Request;
use App\Http\Controllers\OffreController;

Route::get('/', function () {
    $offres = Offre::with('Entreprise')->get(); 
    $offres = Offre::with('Ville')->get(); 
    return view('index', ['offres' => $offres]);
})->name('home');

Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        echo 'Connected successfully to: ' . DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        die('Could not connect to the database. Please check your configuration. error:' . $e);
    }
});

Route::get('/db-tables', function () {
    try {
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::connection()->getDatabaseName();
        $tableCounts = [];

        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0]; // Get the table name
            $count = DB::table($tableName)->count(); // Count the rows in the table
            $tableCounts[$tableName] = $count;
        }

        return response()->json([
            'database' => $databaseName,
            'tables' => $tableCounts,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Could not retrieve tables. Please check your configuration.',
            'message' => $e->getMessage(),
        ]);
    }
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard'); // Redirige vers le tableau de bord
    }

    return back()->withErrors([
        'email' => 'Les informations d’identification fournies sont incorrectes.',
    ]);
});

Route::post('/logout', function (Request $request) {
    Auth::logout(); // Déconnecte l'utilisateur
    $request->session()->invalidate(); // Invalide la session
    $request->session()->regenerateToken(); // Regénère le token CSRF

    return redirect('/login'); // Redirige vers la page de connexion
})->name('logout');

Route::group(['prefix'=>'/dashboard', 'middleware'=> ['auth']], function () {
    Route::get('/', function () {
        return view('dashboard.dashboard');
    });

    Route::get('applications', function () {
        return view('applications');
    })->middleware(CheckStudent::class);

    Route::get('create-company', function () {
        return view('create-company');
    })->middleware(CheckAdminOrPilote::class);

    Route::get('create-account', function () {
        return view('create-account');
    })->middleware(CheckAdminOrPilote::class);

    Route::get('accounts', function () {
        return view('accounts');
    })->middleware(CheckAdminOrPilote::class);

    Route::get('accounts/{id}', function ($id) {
        return view('account-details', ['id' => $id]);
    })->middleware(CheckAdminOrPilote::class);

    Route::get('accounts/{id}/edit', function ($id) {
        return view('edit-account', ['id' => $id]);
    })->middleware(CheckAdminOrPilote::class);

    Route::get('wishlist', function () {
        return view('wishlist');
    })->middleware(CheckStudent::class);
});

// Routes pour les offres
Route::group(['prefix' => '/offres'], function () {
    // Liste de toutes les offres avec tous leurs attributs
    Route::get('/', function (Request $request) {
        $query = Offre::query();

        // Si l'utilisateur est connecté et est un administrateur ou pilote
        if (auth()->check() && (auth()->user()->role->Libelle === 'Pilote' || auth()->user()->role->Libelle === 'Administrateur')) {
            $query->where('Etat', 1)->orWhere('Etat', 0); // Affiche toutes les offres
        } else {
            // Sinon, afficher uniquement les offres actives (Etat = 1)
            $query->where('Etat', 1);
        }

                if ($search = $request->input('search')) {
            $query->where('Titre', 'LIKE', "%{$search}%")
                  ->orWhereHas('entreprise', function ($q) use ($search) {
                      $q->where('Nom', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('ville', function ($q) use ($search) {
                      $q->where('Nom', 'LIKE', "%{$search}%");
                  });
        }

        $offres = $query->with(['entreprise', 'ville'])->get();

        return view('index', compact('offres'));
    })->name('offres.index');

    // Créer une nouvelle offre
    Route::get('/create', function () {
        $entreprises = Entreprise::all(); // Récupère toutes les entreprises
        $secteurs = Secteur::all(); // Récupère tous les secteurs
        $villes = Ville::all();
        return view('offres.create', compact('entreprises', 'secteurs', 'villes'));
    })->name('offres.create');    

    Route::post('/store', function (Request $request) {
        //dd($request->all());
        try {
            // Validation des données
            $validated = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'required|string',
                'remuneration' => 'required|numeric|min:600',
                'date_publication' => 'required|date',
                'date_expiration' => 'required|date|after:date_publication',
                'entreprise' => 'required|integer',
                'secteur' => 'required|integer',
                'ville' => 'required|integer',
            ]);

            // Création de l'offre
            Offre::create([
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

            return redirect()->route('offres.index')->with('success', 'Offre créée avec succès !');
        } catch (\Exception $e) {
            // Enregistre l'erreur dans les logs
            \Log::error('Erreur lors de la création de l\'offre : ' . $e->getMessage());

            // Retourne une réponse avec l'erreur
            return redirect()->back()->withErrors(['error' => 'Une erreur est survenue : ' . $e->getMessage()])->withInput();
        }
    })->name('offres.store');

    // Détails d'une offre
    Route::get('{id}', function ($id) {
        $offre = Offre::where('ID_Offre', $id)->firstOrFail(); // Recherche l'offre par ID_Offre

        // Vérifie si l'offre est désactivée et si l'utilisateur n'est pas autorisé
        if ($offre->Etat == 0 && (!auth()->check() || !(auth()->user()->role->Libelle === 'Pilote' || auth()->user()->role->Libelle === 'Administrateur'))) {
            return redirect()->route('login'); // Redirige vers la page de connexion
        }

        return view('offres.show', ['offre' => $offre]); // Passe l'offre à la vue
    })->name('offres.show');

    Route::get('{id}/apply', function ($id) {
        $offre = Offre::where('ID_Offre', $id)->firstOrFail(); // Vérifie que l'offre existe
        return view('offres.apply', ['offre' => $offre]); // Passe l'offre à la vue
    })->name('offres.apply')->middleware('auth'); // Ajout du middleware auth pour sécuriser l'accès

    Route::post('/{id}/apply', function (Request $request, $id) {
        $offre = Offre::where('ID_Offre', $id)->firstOrFail(); // Vérifie que l'offre existe

        // Validation des données
        $validated = $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048', // Max 2 Mo
            'motivation' => 'required|string|max:1000',
        ]);

        // Logique pour enregistrer la candidature ou envoyer un email
        return redirect()->route('offres.show', ['id' => $id])->with('success', 'Votre candidature a été envoyée avec succès.');
    })->name('offres.apply.submit')->middleware('auth');
    
    // Modifier une offre
    Route::get('/{id}/edit', function ($id) {
        $offre = Offre::findOrFail($id); // Récupère l'offre par ID
        $entreprises = Entreprise::all(); // Récupère toutes les entreprises
        $secteurs = Secteur::all(); // Récupère tous les secteurs
        $villes = Ville::all(); // Récupère toutes les villes

        return view('offres.edit', compact('offre', 'entreprises', 'secteurs', 'villes')); // Passe les données à la vue
    })->name('offres.edit')->middleware(CheckAdminOrPilote::class);

    Route::put('/offres/{id}', [OffreController::class, 'update'])->name('offres.update');

    // Modifier l'état d'une offre pour la marquer comme supprimée
    Route::delete('/{id}', function ($id) {
        $offre = Offre::findOrFail($id); 
        $offre->Etat = 0; 
        $offre->save(); 

        return redirect()->route('offres.index')->with('success', 'Offre supprimée avec succès.');
    })->name('offres.destroy')->middleware(CheckAdminOrPilote::class); // Middleware pour vérifier les autorisations
});

Route::get('/villes/search', function (Request $request) {
    $query = $request->input('query');
    $villes = Ville::where('Nom', 'LIKE', "%{$query}%")->limit(10)->get(); // Limite à 10 résultats
    return response()->json($villes);
});