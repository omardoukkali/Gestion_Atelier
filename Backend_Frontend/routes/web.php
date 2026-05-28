<?php

use App\Models\Demande;
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
    Route::get('/demandes', [DemandeController::class, 'index']);
    Route::get('/demandes/create', [DemandeController::class, 'create'])->name('demandes.create');
    Route::post('/demandes', [DemandeController::class, 'store'])->name('demandes.store');
    Route::patch('/demandes/{demande}/accepter', [DemandeController::class, 'accepter'])->name('demandes.accepter');
    Route::patch('/demandes/{demande}/refuser', [DemandeController::class, 'refuser'])->name('demandes.refuser');
});


require __DIR__.'/auth.php';
