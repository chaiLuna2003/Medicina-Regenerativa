<?php

use App\Http\Controllers\PacientesController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedicosController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

use App\Http\Middleware\PreventBackHistory;

Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('pacientes', PacientesController::class)
        ->parameters(['pacientes' => 'pacientes']);
});

Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    // ... rutas existentes de profile y pacientes ...

    Route::resource('medicos', MedicosController::class)
        ->parameters(['medicos' => 'medicos']);
});

require __DIR__.'/auth.php';