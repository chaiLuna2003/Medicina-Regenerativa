<?php

use App\Http\Controllers\CitasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicosController;
use App\Http\Controllers\PacientesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SignosVitalesController;
use App\Http\Controllers\UsuarioController;
use App\Http\Middleware\PreventBackHistory;
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
    | Solamente administración y recepción pueden acceder al módulo.
    | El médico no recibe ninguna ruta pacientes.*.
    |
    */

    Route::middleware('role:admin,recepcionista')
        ->group(function () {
            Route::resource('pacientes', PacientesController::class)
                ->except(['destroy'])
                ->parameters([
                    'pacientes' => 'pacientes',
                ]);
        });

    /*
    |--------------------------------------------------------------------------
    | Gestión de citas
    |--------------------------------------------------------------------------
    |
    | Solamente administración y recepción pueden:
    | crear, guardar, editar, actualizar o eliminar citas.
    |
    | Estas rutas especiales se declaran antes de /citas/{cita}.
    |
    */

    Route::middleware('role:admin,recepcionista')
        ->group(function () {
            Route::get(
                '/citas/horarios-disponibles',
                [CitasController::class, 'horariosDisponibles']
            )->name('citas.horarios-disponibles');

            Route::get(
                '/buscar-pacientes',
                [CitasController::class, 'buscarPacientes']
            )->name('pacientes.buscar');

            Route::resource('citas', CitasController::class)
                ->except(['index', 'show'])
                ->parameters([
                    'citas' => 'cita',
                ]);
        });

    /*
    |--------------------------------------------------------------------------
    | Consulta de citas
    |--------------------------------------------------------------------------
    |
    | El médico solamente puede consultar:
    | - Su propia agenda.
    | - El detalle de una cita que tenga asignada.
    |
    | El filtro y la validación de pertenencia también deben permanecer
    | dentro de CitasController.
    |
    */

    Route::middleware('role:admin,medico,recepcionista')
        ->group(function () {
            Route::get(
                '/citas',
                [CitasController::class, 'index']
            )->name('citas.index');

            Route::get(
                '/citas/{cita}',
                [CitasController::class, 'show']
            )->name('citas.show');
        });

    /*
    |--------------------------------------------------------------------------
    | Historial general de signos vitales
    |--------------------------------------------------------------------------
    |
    | El médico no puede abrir el historial general.
    | Sus signos vitales se muestran únicamente dentro del detalle
    | de la cita que tiene asignada.
    |
    */

    Route::middleware('role:admin,enfermero')
        ->group(function () {
            Route::get(
                '/signos-vitales',
                [SignosVitalesController::class, 'index']
            )->name('signos-vitales.index');

            Route::get(
                '/signos-vitales/{signoVital}',
                [SignosVitalesController::class, 'show']
            )->name('signos-vitales.show');
        });

    /*
    |--------------------------------------------------------------------------
    | Registro de signos vitales
    |--------------------------------------------------------------------------
    |
    | Solamente enfermería puede capturar signos vitales.
    |
    */

    Route::middleware('role:enfermero')
        ->group(function () {
            Route::get(
                '/citas/{cita}/signos-vitales/crear',
                [SignosVitalesController::class, 'create']
            )->name('signos-vitales.create');

            Route::post(
                '/citas/{cita}/signos-vitales',
                [SignosVitalesController::class, 'store']
            )->name('signos-vitales.store');
        });

    /*
    |--------------------------------------------------------------------------
    | Administración
    |--------------------------------------------------------------------------
    |
    | Solamente el administrador puede:
    | - Eliminar pacientes.
    | - Gestionar médicos.
    | - Gestionar usuarios.
    |
    */

    Route::middleware('role:admin')
        ->group(function () {
            Route::delete(
                '/pacientes/{pacientes}',
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