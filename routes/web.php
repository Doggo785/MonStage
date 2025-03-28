<?php

use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckAdminOrPilote;
use App\Http\Middleware\CheckPilote;
use App\Http\Middleware\CheckStudent;
use Illuminate\Support\Facades\Route;
use App\Models\Offre; // Assurez-vous d'importer le modèle Offre

Route::get('/', function () {
    return view('index');
});

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

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard'); // Redirige vers le tableau de bord
    }

    return back()->withErrors([
        'email' => 'Les informations d’identification fournies sont incorrectes.',
    ]);
});

Route::post('/logout', function (\Illuminate\Http\Request $request) {
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
    Route::get('/', function () {
        $offres = Offre::with('Entreprise')->get(); 
        $offres = Offre::with('Ville')->get(); 
        return view('index', ['offres' => $offres]);
    })->name('offres.index');

    // Détails d'une offre
    Route::get('{id}', function ($id) {
        $offre = Offre::where('ID_Offre', $id)->firstOrFail(); // Recherche l'offre par ID_Offre
        return view('offres.show', ['offre' => $offre]); // Passe l'offre à la vue
    })->name('offres.show');

    // Postuler à une offre
    Route::post('/{id}/apply', function ($id) {
        // Logique pour postuler à une offre
        return redirect()->route('offres.show', ['id' => $id])->with('success', 'Votre candidature a été envoyée.');
    })->name('offres.apply')->middleware('can:apply-offer');

    // Modifier une offre
    Route::get('/{id}/edit', function ($id) {
        return view('offres.edit', ['id' => $id]); // Vue pour modifier une offre
    })->name('offres.edit')->middleware('can:edit-offer');

    // Créer une nouvelle offre
    Route::get('/create', function () {
        return view('offres.create'); // Vue pour créer une nouvelle offre
    })->name('offres.create')->middleware('can:create-offer');
});