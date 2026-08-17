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
use App\Http\Controllers\RecetasController;
use App\Http\Controllers\EstudiosController;

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
            Route::post(
                '/citas/{cita}/generar-meet',
                [
                    CitasController::class,
                    'generarMeet',
                ]
            )->name('citas.generar-meet');
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
| Recetas médicas
|--------------------------------------------------------------------------
|
| El médico puede consultar el historial completo de un paciente cuando
| tenga al menos una cita asignada con él.
|
| Solamente puede crear y modificar la receta de una cita propia.
| El administrador únicamente tiene acceso de consulta.
|
*/

    Route::middleware('role:admin,medico')
        ->group(function () {
            /*
         * Historial de recetas de un paciente.
         */
            Route::get(
                '/pacientes/{paciente}/recetas',
                [RecetasController::class, 'historial']
            )->name('pacientes.recetas.index');

            /*
         * Detalle de una receta.
         */
            Route::get(
                '/recetas/{receta}',
                [RecetasController::class, 'show']
            )->name('recetas.show');
        });

    Route::middleware('role:medico')
        ->group(function () {
            /*
         * Formulario para elaborar una receta.
         */
            Route::get(
                '/citas/{cita}/receta/crear',
                [RecetasController::class, 'create']
            )->name('citas.receta.create');

            /*
         * Guardar la receta.
         */
            Route::post(
                '/citas/{cita}/receta',
                [RecetasController::class, 'store']
            )->name('citas.receta.store');

            /*
         * Formulario para editar una receta.
         */
            Route::get(
                '/recetas/{receta}/editar',
                [RecetasController::class, 'edit']
            )->name('recetas.edit');

            /*
         * Actualizar la receta.
         */
            Route::put(
                '/recetas/{receta}',
                [RecetasController::class, 'update']
            )->name('recetas.update');
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
| Estudios clínicos
|--------------------------------------------------------------------------
|
| Administración y recepción pueden cargar estudios asociados
| directamente a una cita.
|
*/

    Route::middleware('role:admin,recepcionista')
        ->group(function () {

            Route::post(
                '/citas/{cita}/estudios',
                [EstudiosController::class, 'store']
            )->name('estudios.store');

            Route::post(
                '/citas/{cita}/generar-meet',
                [
                    CitasController::class,
                    'generarMeet',
                ]
            )->name('citas.generar-meet');
        });

    /*
|--------------------------------------------------------------------------
| Consulta de estudios clínicos
|--------------------------------------------------------------------------
|
| Administración, médicos y recepción pueden consultar documentos.
| Después reforzaremos en el controlador que un médico solamente pueda
| consultar pacientes con los que tenga relación clínica.
|
*/

    Route::middleware('role:admin,medico,recepcionista')
        ->group(function () {

            Route::get(
                '/pacientes/{paciente}/estudios',
                [EstudiosController::class, 'historial']
            )->name('pacientes.estudios.index');

            Route::get(
                '/estudios/{estudio}/archivo',
                [EstudiosController::class, 'archivo']
            )->name('estudios.archivo');

            Route::get(
                '/estudios/{estudio}/descargar',
                [EstudiosController::class, 'descargar']
            )->name('estudios.descargar');
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

require __DIR__ . '/auth.php';
