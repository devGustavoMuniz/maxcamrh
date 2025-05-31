<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
  if (file_exists(resource_path('js/Pages/Welcome.vue'))) {
    return Inertia::render('Welcome', [
      'canLogin' => Route::has('login'),
      'canRegister' => Route::has('register'),
      'laravelVersion' => Illuminate\Foundation\Application::VERSION,
      'phpVersion' => PHP_VERSION,
    ]);
  }
  return view('welcome');
});

Route::get('/dashboard', function () {
  return Inertia::render('Dashboard', [
    'message' => 'Bem-vindo ao Dashboard com Inertia e Shadcn Vue!',
  ]);
})->name('dashboard');
