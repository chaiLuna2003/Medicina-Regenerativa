<?php

use App\Http\Controllers\CitasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicosController;
use App\Http\Controllers\PacientesController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Página pública
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware([
        'auth',
        'active',
        'verified',
        PreventBackHistory::class,
    ])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Rutas protegidas
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'active',
    'verified',
    PreventBackHistory::class,
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Perfil
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Pacientes
    |--------------------------------------------------------------------------
    |
    | Administrador, médico, enfermero y recepcionista pueden:
    | - Consultar pacientes
    | - Buscar pacientes
    | - Registrar pacientes
    | - Editar pacientes
    |
    */

    Route::middleware(
        'role:admin,medico,enfermero,recepcionista'
    )->group(function () {

        Route::resource('pacientes', PacientesController::class)
            ->except(['destroy'])
            ->parameters([
                'pacientes' => 'pacientes',
            ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Citas
    |--------------------------------------------------------------------------
    |
    | Administrador, médico y recepcionista pueden gestionar las citas.
    |
    */

    Route::middleware(
        'role:admin,medico,recepcionista'
    )->group(function () {

        Route::resource('citas', CitasController::class)
            ->parameters([
                'citas' => 'cita',
            ]);
    });

    Route::get(
    '/buscar-pacientes',
    [CitasController::class, 'buscarPacientes']
)->name('pacientes.buscar');

    /*
    |--------------------------------------------------------------------------
    | Administración
    |--------------------------------------------------------------------------
    |
    | Solo el administrador puede:
    | - Eliminar pacientes
    | - Gestionar médicos
    |
    */

    Route::middleware('role:admin')->group(function () {

        Route::delete(
            'pacientes/{pacientes}',
            [PacientesController::class, 'destroy']
        )->name('pacientes.destroy');

        Route::resource('medicos', MedicosController::class)
            ->parameters([
                'medicos' => 'medicos',
            ]);

        Route::resource('usuarios', UsuarioController::class)
    ->except(['show', 'destroy']);
    });
});

require __DIR__.'/auth.php';