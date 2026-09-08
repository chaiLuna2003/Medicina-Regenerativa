<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\SignoVital;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnfermeriaValoracionesDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $enfermero;

    private User $usuarioMedico;

    private Medicos $medico;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(
            Carbon::parse('2026-09-02 10:00:00')
        );

        $this->enfermero = User::factory()->create([
            'name' => 'Enfermero de prueba',
            'role' => 'enfermero',
            'status' => true,
        ]);

        $this->usuarioMedico = User::factory()->create([
            'name' => 'Médico de prueba',
            'role' => 'medico',
            'status' => true,
        ]);

        $this->medico = Medicos::query()->create([
            'user_id' => $this->usuarioMedico->id,
            'nombre' => 'Médico',
            'apellido_paterno' => 'Responsable',
            'apellido_materno' => 'Pruebas',
            'especialidad' => 'Medicina general',
            'cedula' => 'CED-ENFERMERIA-001',
            'telefono' => '5550000100',
            'consultorio' => 'Consultorio 1',
            'status' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_dashboard_clasifica_valoraciones_por_horario_y_estado(): void
    {
        $citaProxima = $this->crearCita(
            paciente: 'Paciente Próximo',
            hora: '11:00'
        );

        $citaAtrasada = $this->crearCita(
            paciente: 'Paciente Atrasado',
            hora: '09:00'
        );

        $citaRealizada = $this->crearCita(
            paciente: 'Paciente Realizado',
            hora: '08:00'
        );

        $this->crearSignosVitales($citaRealizada);

        $respuesta = $this
            ->actingAs($this->enfermero)
            ->get(route('dashboard'));

        $respuesta->assertOk();

        $this->assertSame(
            [$citaProxima->id],
            $respuesta
                ->viewData('pendientesProximas')
                ->pluck('id')
                ->all()
        );

        $this->assertSame(
            [$citaAtrasada->id],
            $respuesta
                ->viewData('pendientesAtrasadas')
                ->pluck('id')
                ->all()
        );

        $this->assertSame(
            [$citaRealizada->id],
            $respuesta
                ->viewData('valoracionesRealizadasLista')
                ->pluck('id')
                ->all()
        );

        $this->assertSame(
            $citaProxima->id,
            $respuesta->viewData('proximaCita')->id
        );

        $respuesta
            ->assertSee('Valoraciones pendientes')
            ->assertSee('Valoraciones atrasadas')
            ->assertSee('Valoraciones realizadas');
    }

    public function test_valoraciones_proximas_se_ordenan_por_hora(): void
    {
        $citaMasTarde = $this->crearCita(
            paciente: 'Paciente de las Once',
            hora: '11:00'
        );

        $citaMasProxima = $this->crearCita(
            paciente: 'Paciente de las Diez Treinta',
            hora: '10:30'
        );

        $respuesta = $this
            ->actingAs($this->enfermero)
            ->get(route('dashboard'));

        $respuesta->assertOk();

        $this->assertSame(
            [
                $citaMasProxima->id,
                $citaMasTarde->id,
            ],
            $respuesta
                ->viewData('pendientesProximas')
                ->pluck('id')
                ->all()
        );

        $this->assertSame(
            $citaMasProxima->id,
            $respuesta->viewData('proximaCita')->id
        );
    }

    public function test_dashboard_filtra_valoraciones_por_fecha_y_prepara_calendario(): void
    {
        $citaHoy = $this->crearCita(
            paciente: 'Paciente de Hoy',
            hora: '11:00'
        );

        $citaSeleccionada = $this->crearCita(
            paciente: 'Paciente del Calendario',
            hora: '09:30',
            fecha: '2026-09-05'
        );

        $respuesta = $this
            ->actingAs($this->enfermero)
            ->get(route('dashboard', [
                'fecha' => '2026-09-05',
                'mes' => '2026-09',
            ]));

        $respuesta->assertOk();

        $this->assertSame(
            '2026-09-05',
            $respuesta
                ->viewData('fechaSeleccionada')
                ->toDateString()
        );

        $this->assertSame(
            '2026-09',
            $respuesta
                ->viewData('mesCalendario')
                ->format('Y-m')
        );

        $this->assertSame(
            [$citaSeleccionada->id],
            $respuesta
                ->viewData('citasHoy')
                ->pluck('id')
                ->all()
        );

        $this->assertNotContains(
            $citaHoy->id,
            $respuesta
                ->viewData('citasHoy')
                ->pluck('id')
                ->all()
        );

        $this->assertSame(
            1,
            $respuesta
                ->viewData('citasPorDia')
                ->get('2026-09-05')['total']
        );

        $this->assertTrue(
            $respuesta
                ->viewData('diasCalendario')
                ->contains(
                    fn (Carbon $dia) => $dia->toDateString() === '2026-09-05'
                )
        );

        $respuesta
            ->assertSee('Calendario de valoraciones')
            ->assertSee('Seleccionar fecha')
            ->assertSee('Paciente del Calendario')
            ->assertDontSee('Paciente de Hoy')
            ->assertSee(
                route('dashboard', [
                    'fecha' => '2026-09-05',
                    'mes' => '2026-09',
                ])
            );
    }

    public function test_dashboard_muestra_modal_y_botones_para_citas_pendientes(): void
    {
        $cita = $this->crearCita(
            paciente: 'Paciente Modal',
            hora: '11:30'
        );

        $respuesta = $this
            ->actingAs($this->enfermero)
            ->get(route('dashboard'));

        $respuesta
            ->assertOk()
            ->assertSee('modal-signos-vitales')
            ->assertSee('form-modal-signos')
            ->assertSee('data-abrir-modal-signos', false)
            ->assertSee(
                'data-cita-id="'.$cita->id.'"',
                false
            )
            ->assertSee(
                route('signos-vitales.store', $cita),
                false
            );
    }

    public function test_modal_recibe_datos_de_seguridad_y_enlace_del_paciente(): void
    {
        $cita = $this->crearCita(
            paciente: 'Paciente Seguridad',
            hora: '11:30'
        );

        $cita->paciente->update([
            'tipo_sangre' => 'O+',
            'alergias' => 'Penicilina y sulfas',
        ]);

        $respuesta = $this
            ->actingAs($this->enfermero)
            ->get(route('dashboard'));

        $respuesta
            ->assertOk()
            ->assertSee(
                'data-tipo-sangre="O+"',
                false
            )
            ->assertSee(
                'data-alergias="Penicilina y sulfas"',
                false
            )
            ->assertSee(
                'data-paciente-url="'.
                    route('pacientes.show', $cita->paciente).
                    '"',
                false
            )
            ->assertSee('modal-tipo-sangre-paciente')
            ->assertSee('modal-alergias-paciente')
            ->assertSee('modal-enlace-paciente')
            ->assertSee('Consultar datos del paciente')
            ->assertSee(
                'boton.dataset.tipoSangre',
                false
            )
            ->assertSee(
                'boton.dataset.alergias',
                false
            )
            ->assertSee(
                'boton.dataset.pacienteUrl',
                false
            );
    }

    public function test_enfermero_guarda_valoracion_desde_modal_y_regresa_al_dashboard(): void
    {
        $cita = $this->crearCita(
            paciente: 'Paciente Guardado',
            hora: '09:00'
        );

        $respuesta = $this
            ->actingAs($this->enfermero)
            ->post(
                route('signos-vitales.store', $cita),
                [
                    'desde_dashboard_enfermeria' => '1',
                    'modal_cita_id' => $cita->id,
                    'modal_paciente_nombre' => 'Paciente Guardado',

                    'peso' => 72.5,
                    'estatura' => 175,
                    'temperatura' => 36.5,
                    'presion_sistolica' => 120,
                    'presion_diastolica' => 80,
                    'frecuencia_cardiaca' => 75,
                    'frecuencia_respiratoria' => 18,
                    'saturacion_oxigeno' => 98,
                    'glucosa' => 95,
                    'observaciones' => 'Valoración registrada desde el modal.',
                ]
            );

        $respuesta
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas(
                'success',
                'Los signos vitales se registraron correctamente.'
            );

        $this->assertDatabaseHas('signos_vitales', [
            'cita_id' => $cita->id,
            'paciente_id' => $cita->paciente_id,
            'enfermero_id' => $this->enfermero->id,
            'peso' => 72.5,
            'estatura' => 175,
            'presion_sistolica' => 120,
            'presion_diastolica' => 80,
        ]);
    }

    public function test_presion_diastolica_puede_ser_mayor_que_sistolica(): void
    {
        $cita = $this->crearCita(
            paciente: 'Paciente Presion',
            hora: '09:15'
        );

        $respuesta = $this
            ->actingAs($this->enfermero)
            ->from(route('dashboard'))
            ->post(
                route(
                    'signos-vitales.store',
                    $cita
                ),
                [
                    'desde_dashboard_enfermeria' => '1',
                    'peso' => 70,
                    'estatura' => 170,
                    'presion_sistolica' => 80,
                    'presion_diastolica' => 120,
                ]
            );

        $respuesta
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas(
            'signos_vitales',
            [
                'cita_id' => $cita->id,
                'presion_sistolica' => 80,
                'presion_diastolica' => 120,
            ]
        );
    }

    public function test_error_de_validacion_conserva_datos_para_reabrir_modal(): void
    {
        $cita = $this->crearCita(
            paciente: 'Paciente Validación',
            hora: '09:30'
        );

        $respuesta = $this
            ->actingAs($this->enfermero)
            ->from(route('dashboard'))
            ->post(
                route('signos-vitales.store', $cita),
                [
                    'desde_dashboard_enfermeria' => '1',
                    'modal_cita_id' => $cita->id,
                    'modal_paciente_nombre' => 'Paciente Validación',

                    'peso' => 70,
                    'estatura' => 170,
                    'presion_sistolica' => 120,
                    'presion_diastolica' => 201,
                    'observaciones' => 'Datos que deben conservarse.',
                ]
            );

        $respuesta
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors(
                'presion_diastolica'
            )
            ->assertSessionHasInput(
                'modal_cita_id',
                $cita->id
            )
            ->assertSessionHasInput(
                'modal_paciente_nombre',
                'Paciente Validación'
            )
            ->assertSessionHasInput(
                'peso',
                70
            );

        $this->assertDatabaseMissing('signos_vitales', [
            'cita_id' => $cita->id,
        ]);
    }

    public function test_dashboard_rechaza_fechas_invalidas_del_calendario(): void
    {
        $respuestaFecha = $this
            ->actingAs($this->enfermero)
            ->from(route('dashboard'))
            ->get(route('dashboard', [
                'fecha' => '06-09-2026',
            ]));

        $respuestaFecha
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors('fecha');

        $respuestaMes = $this
            ->actingAs($this->enfermero)
            ->from(route('dashboard'))
            ->get(route('dashboard', [
                'mes' => 'septiembre-2026',
            ]));

        $respuestaMes
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors('mes');
    }

    public function test_cita_vencida_muestra_estado_efectivo_y_conserva_triage_pendiente(): void
    {
        $cita = $this->crearCita(
            paciente: 'Paciente Estado Efectivo',
            hora: '09:00'
        );

        $cita->update([
            'estado' => 'en_espera',
        ]);

        $respuesta = $this
            ->actingAs($this->enfermero)
            ->get(route('dashboard'));

        $respuesta
            ->assertOk()
            ->assertSee('Paciente Estado Efectivo')
            ->assertSee('Finalizada')
            ->assertSee('Valoración atrasada')
            ->assertSee('Registrar signos')
            ->assertSee(
                'data-cita-id="'.$cita->id.'"',
                false
            )
            ->assertDontSee('En espera');
    }

    private function crearCita(
        string $paciente,
        string $hora,
        ?string $fecha = null
    ): Citas {
        [$nombre, $apellido] = array_pad(
            explode(' ', $paciente, 2),
            2,
            'Prueba'
        );

        $registroPaciente = Pacientes::query()->create([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        return Citas::query()->create([
            'paciente_id' => $registroPaciente->id,
            'medico_id' => $this->medico->id,
            'fecha' => $fecha ?? now()->toDateString(),
            'hora' => $hora,
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_subsecuente',
            'notas' => 'Cita para valoración de enfermería.',
            'estado' => 'programada',
            'created_by' => $this->enfermero->id,
        ]);
    }

    private function crearSignosVitales(
        Citas $cita
    ): SignoVital {
        return SignoVital::query()->create([
            'paciente_id' => $cita->paciente_id,
            'cita_id' => $cita->id,
            'enfermero_id' => $this->enfermero->id,
            'peso' => 70,
            'estatura' => 170,
            'temperatura' => 36.5,
            'presion_sistolica' => 120,
            'presion_diastolica' => 80,
            'frecuencia_cardiaca' => 75,
            'frecuencia_respiratoria' => 18,
            'saturacion_oxigeno' => 98,
            'glucosa' => 95,
        ]);
    }
}
