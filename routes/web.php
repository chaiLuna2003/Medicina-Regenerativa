<?php

use App\Http\Controllers\CitasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstudiosController;
use App\Http\Controllers\MedicosController;
use App\Http\Controllers\PacientesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecetasController;
use App\Http\Controllers\SignosVitalesController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\HistoriaClinicaController;
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

Route::get(
    '/dashboard',
    [DashboardController::class, 'index']
)
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

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');



    /*
|--------------------------------------------------------------------------
| Gestión administrativa de pacientes
|--------------------------------------------------------------------------
|
| Administración y recepción pueden consultar el listado, registrar
| pacientes y modificar los datos administrativos permitidos.
|
*/

    Route::middleware('role:admin,recepcionista')
        ->group(function () {

            Route::resource(
                'pacientes',
                PacientesController::class
            )
                ->except([
                    'show',
                    'destroy',
                ])
                ->parameters([
                    'pacientes' => 'pacientes',
                ]);
        });

    /*
|--------------------------------------------------------------------------
| Ficha del paciente
|--------------------------------------------------------------------------
|
| El médico puede acceder únicamente cuando el controlador confirme
| que existe una relación clínica mediante una cita.
|
*/

    Route::middleware('role:admin,medico,recepcionista')
        ->group(function () {

            Route::get(
                '/pacientes/{pacientes}',
                [PacientesController::class, 'show']
            )->name('pacientes.show');
        });

    /*
|--------------------------------------------------------------------------
| Historia clínica principal
|--------------------------------------------------------------------------
|
| Administración y médicos autorizados pueden crear o actualizar
| el resumen clínico principal.
|
*/

    Route::middleware('role:admin,medico')
        ->group(function () {

            Route::put(
                '/pacientes/{paciente}/historia-clinica',
                [HistoriaClinicaController::class, 'update']
            )->name('pacientes.historia-clinica.update');
        });

    Route::put(
        '/pacientes/{paciente}/historia-clinica/'
            . 'antecedentes-heredofamiliares',
        [
            HistoriaClinicaController::class,
            'updateHeredofamiliares',
        ]
    )->name(
        'pacientes.historia-clinica.'
            . 'heredofamiliares.update'
    );

    Route::put(
        '/pacientes/{paciente}/historia-clinica/'
            . 'antecedentes-personales-patologicos',
        [
            HistoriaClinicaController::class,
            'updatePersonalesPatologicos',
        ]
    )->name(
        'pacientes.historia-clinica.'
            . 'personales-patologicos.update'
    );

    Route::put(
    '/pacientes/{paciente}/historia-clinica/'
        . 'antecedentes-personales-no-patologicos',
    [
        HistoriaClinicaController::class,
        'updatePersonalesNoPatologicos',
    ]
)->name(
    'pacientes.historia-clinica.'
        . 'personales-no-patologicos.update'
);

    /*
    |--------------------------------------------------------------------------
    | Gestión de citas
    |--------------------------------------------------------------------------
    |
    | Administración y recepción pueden crear, modificar y gestionar citas.
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

            Route::resource(
                'citas',
                CitasController::class
            )
                ->except([
                    'index',
                    'show',
                ])
                ->parameters([
                    'citas' => 'cita',
                ]);

            /*
             * Generar enlace de Google Meet.
             */
            Route::post(
                '/citas/{cita}/generar-meet',
                [CitasController::class, 'generarMeet']
            )->name('citas.generar-meet');
        });

    /*
    |--------------------------------------------------------------------------
    | Consulta de citas
    |--------------------------------------------------------------------------
    |
    | Administración y recepción pueden consultar todas las citas.
    | El médico solamente debe poder consultar las citas autorizadas
    | por CitasController.
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
    | Recetas médicas - Consulta
    |--------------------------------------------------------------------------
    |
    | Administración y médicos pueden consultar recetas.
    | El controlador mantiene las reglas de relación clínica.
    |
    */

    Route::middleware('role:admin,medico')
        ->group(function () {

            Route::get(
                '/pacientes/{paciente}/recetas',
                [RecetasController::class, 'historial']
            )->name('pacientes.recetas.index');

            Route::get(
                '/recetas/{receta}',
                [RecetasController::class, 'show']
            )->name('recetas.show');

            Route::get(
                '/recetas/{receta}/pdf',
                [RecetasController::class, 'pdf']
            )->name('recetas.pdf');
        });

    /*
    |--------------------------------------------------------------------------
    | Recetas médicas - Gestión
    |--------------------------------------------------------------------------
    |
    | Solamente el médico puede elaborar o modificar recetas.
    | RecetasController verifica además que la cita le pertenezca.
    |
    */

    Route::middleware('role:medico')
        ->group(function () {

            Route::get(
                '/citas/{cita}/receta/crear',
                [RecetasController::class, 'create']
            )->name('citas.receta.create');

            Route::post(
                '/citas/{cita}/receta',
                [RecetasController::class, 'store']
            )->name('citas.receta.store');

            Route::get(
                '/recetas/{receta}/editar',
                [RecetasController::class, 'edit']
            )->name('recetas.edit');

            Route::put(
                '/recetas/{receta}',
                [RecetasController::class, 'update']
            )->name('recetas.update');
        });

    /*
    |--------------------------------------------------------------------------
    | Signos vitales - Consulta
    |--------------------------------------------------------------------------
    |
    | Administración y enfermería pueden consultar el historial general.
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
    | Signos vitales - Registro
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
    | Estudios clínicos - Registro
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
        });

    /*
    |--------------------------------------------------------------------------
    | Estudios clínicos - Consulta
    |--------------------------------------------------------------------------
    |
    | Administración, médicos y recepción pueden consultar documentos.
    | EstudiosController aplica las restricciones clínicas adicionales.
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
    | Operaciones exclusivas del administrador.
    |
    */

    Route::middleware('role:admin')
        ->group(function () {

            Route::delete(
                '/pacientes/{pacientes}',
                [PacientesController::class, 'destroy']
            )->name('pacientes.destroy');

            Route::resource(
                'medicos',
                MedicosController::class
            )->parameters([
                'medicos' => 'medicos',
            ]);

            Route::resource(
                'usuarios',
                UsuarioController::class
            )->except([
                'show',
                'destroy',
            ]);
        });
});

require __DIR__ . '/auth.php';
