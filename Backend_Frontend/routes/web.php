<?php


use App\Models\Demande;
use App\Http\Controllers\LigneDevisController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\FicheTravailController;
use App\Http\Controllers\LivraisonController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\StockPieceController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    // Logique de filtrage selon le rôle
    if ($user->role === 'chef_atelier') {
        $demandes = Demande::with('employe')->latest()->get();
    } else {
        $demandes = Demande::where('employe_id', $user->id)->latest()->get();
    }

    // On envoie les demandes à la vue Vue.js
    return Inertia::render('Dashboard', [
        'demandes' => $demandes
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




// Routes protégées par l'authentification
Route::middleware('auth')->group(function () {
    // L'Aiguilleur (Smart Dashboard)
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        if ($role === 'chef_atelier') {
            // Le chef a accès au vrai tableau de bord
            return Inertia::render('Dashboard');
        } elseif ($role === 'ouvrier') {
            // L'ouvrier est redirigé vers sa liste de tâches du jour
            return redirect()->route('tasks.index');
        } elseif ($role === 'employe') {
            // L'employé est redirigé vers la gestion des véhicules
            return redirect()->route('vehicules.index');
        }

        // Par sécurité
        abort(403);
    })->name('dashboard');
    Route::get('/demandes', [DemandeController::class, 'index']);
    Route::get('/demandes/create', [DemandeController::class, 'create'])->name('demandes.create');
    Route::post('/demandes', [DemandeController::class, 'store'])->name('demandes.store');
    Route::patch('/demandes/{demande}/accepter', [DemandeController::class, 'accepter'])->name('demandes.accepter');
    Route::patch('/demandes/{demande}/refuser', [DemandeController::class, 'refuser'])->name('demandes.refuser');
    // Routes pour les Véhicules
    Route::get('/vehicules', [VehiculeController::class, 'index'])->name('vehicules.index');
    Route::get('/vehicules/create', [VehiculeController::class, 'create'])->name('vehicules.create');
    Route::post('/vehicules', [VehiculeController::class, 'store'])->name('vehicules.store');
    Route::resource('stock-pieces', StockPieceController::class);
    Route::resource('commandes', CommandeController::class);
    Route::resource('livraisons', LivraisonController::class)->only(['index', 'store']);
    Route::resource('fiches-travail', FicheTravailController::class)->only(['create', 'store']);
    Route::resource('tasks', TaskController::class)->only(['index', 'create', 'store']);
    // 1. Routes accessibles uniquement par le CHEF D'ATELIER
    Route::middleware(['role:chef_atelier'])->group(function () {
      //  Route::get('/dashboard', function () { return Inertia::render('Dashboard'); })->name('dashboard');
        Route::patch('/demandes/{demande}/accepter', [DemandeController::class, 'accepter'])->name('demandes.accepter');
        Route::patch('/demandes/{demande}/refuser', [DemandeController::class, 'refuser'])->name('demandes.refuser');
        Route::resource('tasks', TaskController::class)->only(['create', 'store']);
        Route::patch('/ligne-devis/{ligneDevis}/valider', [LigneDevisController::class, 'valider'])->name('ligne-devis.valider');
        Route::patch('/ligne-devis/{ligneDevis}/refuser', [LigneDevisController::class, 'refuser'])->name('ligne-devis.refuser');
        // Route::resource('vehicules', VehicleController::class);
        // Gestion des fournisseurs — réservée au chef
        Route::resource('fournisseurs', FournisseurController::class)->only(['index', 'create', 'store']);
    });

    // 2. Routes accessibles par l'OUVRIER (et le chef d'atelier par confort si besoin)
    Route::middleware(['role:ouvrier,chef_atelier'])->group(function () {
        Route::resource('tasks', TaskController::class)->only(['index']); // Consulter ses tâches
        Route::resource('fiches-travail', FicheTravailController::class)->only(['create', 'store']);
        Route::get('/stock-pieces', [StockPieceController::class, 'index'])->name('stock-pieces.index');
    });
    // 3. Routes de l'EMPLOYÉ
    Route::middleware(['role:employe,chef_atelier'])->group(function () {
        // Tes futures routes pour soumettre une demande de tâche (UC1 / UC2)
        Route::resource('demandes', DemandeController::class)->only(['index', 'create', 'store']);
    });

    // 4. Routes pour Ouvrier

    Route::middleware(['role:ouvrier'])->group(function () {
        // ... tes autres routes ouvrier s'il y en a ...

        // Actions sur les tâches
        Route::patch('/tasks/{task}/start', [TaskController::class, 'start'])->name('tasks.start');
        Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
        Route::post('/tasks/{task}/devis', [LigneDevisController::class, 'store'])->name('ligne-devis.store');
    });

    // la route pour la gestion des véhicules
    Route::resource('vehicule', VehicleController::class);

});


require __DIR__.'/auth.php';
