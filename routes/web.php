<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\SimulationController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// League routes
Route::get('/leagues', [LeagueController::class, 'index'])->name('leagues.index');
Route::get('/leagues/create', [LeagueController::class, 'create'])->name('leagues.create');
Route::post('/leagues', [LeagueController::class, 'store'])->name('leagues.store');
Route::get('/leagues/{league}', [LeagueController::class, 'show'])->name('leagues.show');
Route::post('/leagues/{league}/reset', [LeagueController::class, 'reset'])->name('leagues.reset');

// Simulation routes
Route::post('/simulation/{league}/week', [SimulationController::class, 'simulateWeek'])->name('simulation.run');
Route::post('/simulation/{league}/all', [SimulationController::class, 'simulateAll'])->name('simulation.runAll');
