<?php

use App\Http\Controllers\CandidatureController;
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
use App\Http\Controllers\UserController;

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

        // Vérifie si le mot de passe est encore le mot de passe par défaut
        if (Hash::check('DefaultPassword!', Auth::user()->Password)) {
            return redirect()->route('password.reset.prompt');
        }

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

Route::get('/password/reset', [UserController::class, 'showResetPasswordForm'])->name('password.reset.prompt');
Route::post('/password/reset', [UserController::class, 'resetPassword'])->name('password.reset');

Route::group(['prefix' => '/dashboard', 'middleware' => ['auth']], function () {
    Route::get('/', function () {
        return view('dashboard.dashboard');
    });

    // Routes pour la wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('dashboard.wishlist.index')->middleware(CheckStudent::class);
    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
    // Routes pour les utilisateurs
    Route::group(['prefix' => 'users', 'middleware' => [CheckAdminOrPilote::class]], function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index'); // Liste des utilisateurs
        Route::get('/{id}', [UserController::class, 'show'])->name('users.show'); // Voir les détails d'un utilisateur
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit'); // Modifier un utilisateur
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update'); // Mettre à jour un utilisateur
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy'); // Supprimer un utilisateur
        Route::post('/store', [UserController::class, 'store'])->name('users.store'); // Ajouter un utilisateur
    });

    Route::get('/candidatures', [CandidatureController::class, 'index'])->name('dashboard.candidatures.index')->middleware('auth');
});

// Routes pour les offres
Route::prefix('offres')->group(function () {
    Route::get('/', [OffreController::class, 'index'])->name('offres.index');
    Route::get('/create', [OffreController::class, 'create'])->name('offres.create')->middleware(CheckAdminOrPilote::class);
    Route::post('/store', [OffreController::class, 'store'])->name('offres.store')->middleware(CheckAdminOrPilote::class);
    Route::get('/{id}', [OffreController::class, 'show'])->name('offres.show');
    Route::get('/{id}/edit', [OffreController::class, 'edit'])->name('offres.edit')->middleware(CheckAdminOrPilote::class);
    Route::post('/{id}/apply', [OffreController::class, 'apply'])->name('offres.apply.submit')->middleware(CheckStudent::class);
    Route::put('/{id}', [OffreController::class, 'update'])->name('offres.update')->middleware(CheckAdminOrPilote::class);
    Route::delete('/{id}', [OffreController::class, 'destroy'])->name('offres.destroy')->middleware(CheckAdminOrPilote::class);
    Route::delete('/{id}/delete-file/{type}', [OffreController::class, 'deleteFile'])->name('offres.deleteFile');
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
    Route::put('/{id}/update-picture', [EntrepriseController::class, 'updatePicture'])->name('entreprises.update_picture')->middleware(CheckAdminOrPilote::class);
    Route::get('/search', [EntrepriseController::class, 'search'])->name('entreprises.search');
    Route::post('/store', [EntrepriseController::class, 'store'])->name('entreprises.store')->middleware(CheckAdminOrPilote::class);
    Route::delete('/{id}', [EntrepriseController::class, 'destroy'])->name('entreprises.destroy')->middleware(CheckAdminOrPilote::class);
    Route::put('/{id}', [EntrepriseController::class, 'update'])->name('entreprises.update')->middleware(CheckAdminOrPilote::class);
    Route::post('/{id}/rate', [EntrepriseController::class, 'rate'])->name('entreprises.rate')->middleware(CheckStudent::class); // uniquement Student normalement
});

Route::group(['prefix'=> 'wishlist', 'middleware' => [CheckStudent::class]], function () {
    Route::get('/', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/remove/{id}', [WishlistController::class, 'remove'])->name('wishlist.remove');
});