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
use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\WishlistController;

// Liste de toutes les offres avec tous leurs attributs
Route::get('/', function (Request $request) {
    // Trié par Date_publication en ordre décroissant pour prendre les 4 dernières offres
    $offres = Offre::with(['entreprise', 'ville'])
                ->orderBy('Date_publication', 'desc')
                ->take(4)
                ->get();
    return view('index', compact('offres'));
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
Route::prefix('offres')->group(function () {
    Route::get('/', [OffreController::class, 'index'])->name('offres.index');
    Route::get('/create', [OffreController::class, 'create'])->name('offres.create')->middleware('auth');
    Route::post('/store', [OffreController::class, 'store'])->name('offres.store')->middleware('auth');
    Route::get('/{id}', [OffreController::class, 'show'])->name('offres.show');
    Route::get('/{id}/edit', [OffreController::class, 'edit'])->name('offres.edit')->middleware('auth');
    Route::post('/{id}/apply', [OffreController::class, 'apply'])->name('offres.apply.submit')->middleware('auth');
    Route::put('/{id}', [OffreController::class, 'update'])->name('offres.update')->middleware('auth');
    Route::delete('/{id}', [OffreController::class, 'destroy'])->name('offres.destroy')->middleware('auth');
});

Route::get('/villes/search', function (Request $request) {
    $query = $request->input('query');
    $villes = Ville::where('Nom', 'LIKE', "%{$query}%")->limit(10)->get(); // Limite à 10 résultats
    return response()->json($villes);
});

Route::get('/competences/search', function (Request $request) {
    $query = $request->input('query');
    $competences = App\Models\Competence::where('Libelle', 'LIKE', "%{$query}%")->get();
    return response()->json($competences);
});

Route::post('/profile/update-picture', [ProfileController::class, 'updatePicture'])->name('profile.update_picture')->middleware('auth');

Route::group(['prefix'=> 'entreprises'], function () {
    Route::get('/', [EntrepriseController::class, 'index'])->name('entreprises.index');
    Route::put('/{id}/update-picture', [EntrepriseController::class, 'updatePicture'])->name('entreprises.update_picture');
    Route::get('/search', [EntrepriseController::class, 'search'])->name('entreprises.search');
    Route::post('/store', [EntrepriseController::class, 'store'])->name('entreprises.store');
    Route::delete('/entreprises/{id}', [EntrepriseController::class, 'destroy'])->name('entreprises.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{id}', [WishlistController::class, 'remove'])->name('wishlist.remove');
});

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

