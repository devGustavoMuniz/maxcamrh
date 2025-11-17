<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Franchise\FranchiseController;
use App\Http\Controllers\Collaborator\CollaboratorController;
use App\Http\Controllers\Report\ReportController;
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

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/clients-by-franchise', [ReportController::class, 'clientsByFranchiseIndex'])
            ->name('clients-by-franchise.index');
        Route::post('/clients-by-franchise/generate', [ReportController::class, 'clientsByFranchiseGenerate'])
            ->name('clients-by-franchise.generate');

        Route::get('/collaborators-by-client', [ReportController::class, 'collaboratorsByClientIndex'])
            ->name('collaborators-by-client.index');
        Route::post('/collaborators-by-client/generate', [ReportController::class, 'collaboratorsByClientGenerate'])
            ->name('collaborators-by-client.generate');
    });
});

require __DIR__.'/auth.php';
