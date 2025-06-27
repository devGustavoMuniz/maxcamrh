<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FranchiseController;
use App\Http\Controllers\CollaboratorController;
use Illuminate\Support\Facades\Redirect;

Route::get('/', function () {
    return Redirect::route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('admins', AdminController::class)->except([
        'show'
    ]);

    Route::resource('franchises', FranchiseController::class)->except([
        'show'
    ]);

    Route::resource('clients', ClientController::class)->except([
        'show'
    ]);

    Route::resource('collaborators', CollaboratorController::class)->except([
        'show'
    ]);
});

require __DIR__.'/auth.php';
