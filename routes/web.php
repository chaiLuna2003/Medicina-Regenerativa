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
use App\Http\Controllers\ExploracionesFisicasController;
use App\Http\Middleware\PreventBackHistory;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HojaDiariaController;
use App\Http\Controllers\CasosClinicosController;
use App\Http\Controllers\EvolucionesClinicasController;

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
| Hoja diaria
|--------------------------------------------------------------------------
|
| Administración, recepción y enfermería pueden consultar todas las citas.
| El médico solamente podrá generar la hoja correspondiente a sus citas.
|
*/

    Route::middleware(
        'role:admin,recepcionista,medico,enfermero'
    )->group(function () {

        Route::get(
            '/hoja-diaria',
            [HojaDiariaController::class, 'index']
        )->name('hoja-diaria.index');

        Route::get(
            '/hoja-diaria/pdf',
            [HojaDiariaController::class, 'pdf']
        )->name('hoja-diaria.pdf');
    });


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

    Route::middleware('role:admin,medico')
        ->group(function () {
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
        });

    /*
|--------------------------------------------------------------------------
| Hábitos alimenticios
|--------------------------------------------------------------------------
|
| Administración y médicos autorizados pueden registrar o actualizar
| los hábitos alimenticios. El controlador comprueba además la relación
| clínica del médico con el paciente.
|
*/

    Route::middleware('role:admin,medico')
        ->group(function () {
            Route::put(
                '/pacientes/{paciente}/historia-clinica/'
                    . 'habitos-alimenticios',
                [
                    HistoriaClinicaController::class,
                    'updateHabitosAlimenticios',
                ]
            )->name(
                'pacientes.historia-clinica.'
                    . 'habitos-alimenticios.update'
            );

            Route::put(
                '/pacientes/{paciente}/historia-clinica/'
                    . 'antecedentes-ginecoobstetricos',
                [
                    HistoriaClinicaController::class,
                    'updateGinecoobstetricos',
                ]
            )->name(
                'pacientes.historia-clinica.'
                    . 'ginecoobstetricos.update'
            );
        });

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
| Exploración física
|--------------------------------------------------------------------------
|
| Solamente el médico asignado a la cita puede crear o actualizar
| la exploración. El controlador vuelve a comprobar la autorización.
|
*/

    Route::middleware('role:medico')
        ->group(function () {
            Route::put(
                '/citas/{cita}/exploracion-fisica',
                [
                    ExploracionesFisicasController::class,
                    'update',
                ]
            )->name(
                'citas.exploracion-fisica.update'
            );
        });

    /*
    |--------------------------------------------------------------------------
    | Casos clínicos - Apertura
    |--------------------------------------------------------------------------
    |
    | Solamente el médico asignado a la cita puede abrir un caso
    | y registrar su primera evolución.
    |
    */

    Route::middleware('role:medico')
        ->group(function () {
            /*
         * Abrir un caso clínico y crear
         * su primera evolución.
         */
            Route::post(
                '/citas/{cita}/casos-clinicos',
                [
                    CasosClinicosController::class,
                    'store',
                ]
            )->name(
                'citas.casos-clinicos.store'
            );

            /*
         * Agregar la cita actual como seguimiento
         * de un caso clínico existente.
         */
            Route::post(
                '/citas/{cita}/casos-clinicos/'
                    . '{casoClinico}/evoluciones',
                [
                    EvolucionesClinicasController::class,
                    'store',
                ]
            )->name(
                'citas.casos-clinicos.evoluciones.store'
            );

            /*
 * Actualizar el contenido de una evolución.
 */
            Route::put(
                '/evoluciones/{evolucionClinica}',
                [
                    EvolucionesClinicasController::class,
                    'update',
                ]
            )->name(
                'evoluciones.update'
            );

            /*
 * Crear o actualizar la valoración completa
 * de aparatos de una evolución.
 */
            Route::put(
                '/evoluciones/{evolucionClinica}/aparatos',
                [
                    EvolucionesClinicasController::class,
                    'updateAparatos',
                ]
            )->name(
                'evoluciones.aparatos.update'
            );
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

            /*
         * Cargar estudios desde una cita.
         */
            Route::post(
                '/citas/{cita}/estudios',
                [EstudiosController::class, 'store']
            )->name('estudios.store');

            /*
         * Cargar estudios desde la ficha del paciente.
         */
            Route::post(
                '/pacientes/{paciente}/estudios',
                [EstudiosController::class, 'storeDesdePaciente']
            )->name('pacientes.estudios.store');
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
