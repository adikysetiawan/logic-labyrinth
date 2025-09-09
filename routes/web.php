<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GamePlayController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PlayerRegistrationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Player registration (public, no auth)
Route::get('/player/register', [PlayerRegistrationController::class, 'show'])->name('player.register.show');
Route::post('/player/register', [PlayerRegistrationController::class, 'store'])->name('player.register.store');

// Difficulty selection after registration
Route::get('/player/difficulty', [PlayerRegistrationController::class, 'difficultyShow'])->name('player.difficulty.show');
Route::post('/player/difficulty', [PlayerRegistrationController::class, 'difficultyStore'])->name('player.difficulty.store');

// Play page, requires a player session
Route::get('/play', function () {
    if (!session()->has('player_id')) {
        return redirect()->route('player.register.show');
    }
    if (!session()->has('difficulty')) {
        return redirect()->route('player.difficulty.show');
    }
    return view('play');
})->name('play');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public endpoints (no login): create player identity & fetch levels for gameplay
Route::post('players', [PlayerController::class, 'store']);
Route::get('levels', [LevelController::class, 'index']);
Route::get('levels/random', [LevelController::class, 'random']);
Route::get('levels/{level}', [LevelController::class, 'show']);
Route::match(['GET','POST'], 'levels/{level}/check', [LevelController::class, 'check']);
Route::post('game-plays', [GamePlayController::class, 'store']);
Route::get('leaderboard/top-week', [LeaderboardController::class, 'weeklyTop']);

// Admin-protected endpoints: manage players and levels
Route::middleware(['auth', 'admin'])->group(function () {
    Route::apiResource('players', PlayerController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::apiResource('levels', LevelController::class)->only(['store', 'update', 'destroy']);

    // Simple admin pages
    Route::get('/admin/players', [AdminController::class, 'players'])->name('admin.players');
    Route::get('/admin/levels', [AdminController::class, 'levels'])->name('admin.levels');
    Route::get('/admin/game-plays', [AdminController::class, 'gamePlays'])->name('admin.game-plays');

    // Admin form actions (web forms)
    Route::post('/admin/levels', [AdminController::class, 'storeLevel'])->name('admin.levels.store');
    Route::put('/admin/levels/{level}', [AdminController::class, 'updateLevel'])->name('admin.levels.update');
    Route::delete('/admin/levels/{level}', [AdminController::class, 'destroyLevel'])->name('admin.levels.destroy');
    Route::delete('/admin/players/{player}', [AdminController::class, 'destroyPlayer'])->name('admin.players.destroy');
});

require __DIR__.'/auth.php';

// Graceful GET logout fallback: some UIs or users may hit /logout directly via GET.
// This ensures we still end the session and redirect home without throwing 419.
Route::get('/logout', function (Request $request) {
    if (Auth::check()) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
    return redirect('/');
})->name('logout.get');

