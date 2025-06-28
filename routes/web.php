<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LeagueController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// League routes
Route::get('/leagues', [LeagueController::class, 'index'])->name('leagues.index');
Route::get('/leagues/create', [LeagueController::class, 'create'])->name('leagues.create');
Route::post('/leagues', [LeagueController::class, 'store'])->name('leagues.store');
Route::get('/leagues/{league}', [LeagueController::class, 'show'])->name('leagues.show');
Route::post('/leagues/{league}/reset', [LeagueController::class, 'reset'])->name('leagues.reset');