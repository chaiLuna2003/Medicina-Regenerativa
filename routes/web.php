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
|
| Cualquier usuario autenticado, activo y verificado puede entrar.
| DashboardController decide qué dashboard mostrar según su rol.
|
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
    |
    | Todos los usuarios autenticados pueden administrar su propio perfil.
    |
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
    | Solamente administración y recepción tienen acceso al listado y CRUD.
    | Médicos y enfermería deben acceder al paciente desde sus citas.
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
    | Citas
    |--------------------------------------------------------------------------
    |
    | Administración, médicos y recepción pueden consultar las citas.
    |
    */

    Route::middleware('role:admin,medico,recepcionista')
        ->group(function () {

            /*
             * Debe estar antes del resource para que Laravel no interprete
             * "horarios-disponibles" como el parámetro {cita}.
             */
            Route::get(
                '/citas/horarios-disponibles',
                [CitasController::class, 'horariosDisponibles']
            )->name('citas.horarios-disponibles');

            Route::get(
                '/buscar-pacientes',
                [CitasController::class, 'buscarPacientes']
            )->name('pacientes.buscar');

            Route::resource('citas', CitasController::class)
                ->parameters([
                    'citas' => 'cita',
                ]);
        });

    /*
    |--------------------------------------------------------------------------
    | Consulta de signos vitales
    |--------------------------------------------------------------------------
    |
    | Administración, médicos y enfermería pueden consultar el historial
    | y el detalle de los signos vitales.
    |
    */

    Route::middleware('role:admin,medico,enfermero')
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
    | Solamente enfermería puede abrir el formulario y registrar los signos.
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
    | Solamente administración puede eliminar pacientes y gestionar
    | médicos y usuarios.
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

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
|
| Carga las rutas de inicio de sesión, recuperación de contraseña,
| cierre de sesión y verificación de correo.
|
*/

require __DIR__.'/auth.php';