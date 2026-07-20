<?php

use App\Http\Controllers\MedicosController;
use App\Http\Controllers\PacientesController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\PreventBackHistory;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', PreventBackHistory::class])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ver, buscar, crear y editar pacientes: admin, medico, enfermero, recepcionista
    Route::middleware('role:admin,medico,enfermero,recepcionista')->group(function () {
        Route::resource('pacientes', PacientesController::class)
            ->except(['destroy'])
            ->parameters(['pacientes' => 'pacientes']);
    });

    // Solo admin: eliminar pacientes y gestionar médicos
    Route::middleware('role:admin')->group(function () {
        Route::delete('pacientes/{pacientes}', [PacientesController::class, 'destroy'])
            ->name('pacientes.destroy');

        Route::resource('medicos', MedicosController::class)
            ->parameters(['medicos' => 'medicos']);
    });
});

require __DIR__.'/auth.php';