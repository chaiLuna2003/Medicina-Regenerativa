<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Models\HistoriaClinica;
use App\Models\HistoriaClinicaDocumento;
use App\Models\Medicos;
use App\Models\Pacientes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HistoriaClinicaPdfTest extends TestCase
{
    use RefreshDatabase;

    private User $administrador;

    private User $usuarioMedico;

    private User $medicoAjeno;

    private User $recepcion;

    private Medicos $perfilMedico;

    private Pacientes $paciente;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        $this->administrador = User::factory()->create([
            'role' => 'admin',
            'status' => true,
        ]);

        $this->usuarioMedico = User::factory()->create([
            'role' => 'medico',
            'status' => true,
        ]);

        $this->medicoAjeno = User::factory()->create([
            'role' => 'medico',
            'status' => true,
        ]);

        $this->recepcion = User::factory()->create([
            'role' => 'recepcionista',
            'status' => true,
        ]);

        $this->perfilMedico = $this->crearMedico(
            $this->usuarioMedico,
            'PDF-AUTORIZADO'
        );

        $this->crearMedico(
            $this->medicoAjeno,
            'PDF-AJENO'
        );

        $this->paciente = Pacientes::query()->create([
            'nombre' => 'Héctor Miguel',
            'apellido' => 'González Muñoz',
            'fecha_nacimiento' => '1986-04-12',
            'sexo' => 'masculino',
            'categoria' => 'sin_categoria',
            'status' => true,
        ]);

        HistoriaClinica::query()->create([
            'paciente_id' => $this->paciente->id,
            'padecimiento_actual' =>
                'Dolor articular de evolución progresiva.',
            'tratamientos_actuales' =>
                'Tratamiento regenerativo de prueba.',
            'prioridad_analisis_medico' =>
                'Vigilar movilidad y respuesta clínica.',
        ]);

        Citas::query()->create([
            'paciente_id' => $this->paciente->id,
            'medico_id' => $this->perfilMedico->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora' => '10:00',
            'duracion_minutos' => 30,
            'modalidad' => 'presencial',
            'motivo' => 'consulta_inicial',
            'estado' => 'programada',
            'created_by' => $this->administrador->id,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_administrador_genera_y_almacena_expediente_privado(): void
    {
        $respuesta = $this
            ->actingAs($this->administrador)
            ->post(route(
                'pacientes.historia-clinica.documentos.store',
                $this->paciente
            ));

        $documento = HistoriaClinicaDocumento::query()
            ->sole();

        $respuesta->assertRedirect(route(
            'historias-clinicas.documentos.archivo',
            $documento
        ));

        $this->assertSame(
            $this->paciente->id,
            $documento->paciente_id
        );

        $this->assertSame(
            $this->administrador->id,
            $documento->generado_por
        );

        $this->assertSame(
            'application/pdf',
            $documento->mime_type
        );

        $this->assertStringContainsString(
            'historia-clinica-' . $this->paciente->id,
            $documento->archivo_nombre
        );

        Storage::disk('local')->assertExists(
            $documento->archivo_path
        );

        Storage::disk('public')->assertMissing(
            $documento->archivo_path
        );

        $contenido = Storage::disk('local')->get(
            $documento->archivo_path
        );

        $this->assertStringStartsWith('%PDF-', $contenido);
        $this->assertSame(strlen($contenido), $documento->archivo_size);
    }

    public function test_fotografia_valida_se_incorpora_al_generar_expediente(): void
    {
        $rutaFoto = 'pacientes/fotografias/paciente.png';

        Storage::disk('public')->put(
            $rutaFoto,
            base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJ'
                . 'AAAADUlEQVQIHWP4z8DwHwAFgAI/ScL9WQAAAABJRU5ErkJggg=='
            )
        );

        $this->paciente->update([
            'foto' => $rutaFoto,
        ]);

        $this
            ->actingAs($this->administrador)
            ->post(route(
                'pacientes.historia-clinica.documentos.store',
                $this->paciente
            ))
            ->assertRedirect();

        $documento = HistoriaClinicaDocumento::query()
            ->sole();

        Storage::disk('local')->assertExists(
            $documento->archivo_path
        );

        $this->assertGreaterThan(
            0,
            $documento->archivo_size
        );
    }

    public function test_cada_generacion_conserva_una_version_independiente(): void
    {
        Carbon::setTestNow('2026-09-02 09:00:00');

        $this
            ->actingAs($this->administrador)
            ->post(route(
                'pacientes.historia-clinica.documentos.store',
                $this->paciente
            ))
            ->assertRedirect();

        Carbon::setTestNow('2026-09-02 09:00:01');

        $this
            ->actingAs($this->administrador)
            ->post(route(
                'pacientes.historia-clinica.documentos.store',
                $this->paciente
            ))
            ->assertRedirect();

        $documentos = HistoriaClinicaDocumento::query()
            ->orderBy('id')
            ->get();

        $this->assertCount(2, $documentos);
        $this->assertNotSame(
            $documentos[0]->archivo_path,
            $documentos[1]->archivo_path
        );

        Storage::disk('local')->assertExists(
            $documentos[0]->archivo_path
        );

        Storage::disk('local')->assertExists(
            $documentos[1]->archivo_path
        );
    }

    public function test_medico_vinculado_genera_consulta_y_descarga_expediente(): void
    {
        $this
            ->actingAs($this->usuarioMedico)
            ->post(route(
                'pacientes.historia-clinica.documentos.store',
                $this->paciente
            ))
            ->assertRedirect();

        $documento = HistoriaClinicaDocumento::query()
            ->sole();

        $this
            ->actingAs($this->usuarioMedico)
            ->get(route(
                'historias-clinicas.documentos.archivo',
                $documento
            ))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader(
                'content-disposition',
                'inline; filename="'
                    . $documento->archivo_nombre
                    . '"'
            );

        $respuestaDescarga = $this
            ->actingAs($this->usuarioMedico)
            ->get(route(
                'historias-clinicas.documentos.descargar',
                $documento
            ));

        $respuestaDescarga
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertStringContainsString(
            'attachment;',
            (string) $respuestaDescarga->headers->get(
                'content-disposition'
            )
        );
    }

    public function test_medico_ajeno_no_genera_ni_consulta_expediente(): void
    {
        $documento = $this->crearDocumentoAlmacenado();

        $this
            ->actingAs($this->medicoAjeno)
            ->post(route(
                'pacientes.historia-clinica.documentos.store',
                $this->paciente
            ))
            ->assertForbidden();

        $this
            ->actingAs($this->medicoAjeno)
            ->get(route(
                'historias-clinicas.documentos.archivo',
                $documento
            ))
            ->assertForbidden();

        $this
            ->actingAs($this->medicoAjeno)
            ->get(route(
                'historias-clinicas.documentos.descargar',
                $documento
            ))
            ->assertForbidden();

        $this->assertDatabaseCount(
            'historia_clinica_documentos',
            1
        );
    }

    public function test_recepcion_no_accede_a_rutas_de_expedientes(): void
    {
        $documento = $this->crearDocumentoAlmacenado();

        $this
            ->actingAs($this->recepcion)
            ->post(route(
                'pacientes.historia-clinica.documentos.store',
                $this->paciente
            ))
            ->assertForbidden();

        $this
            ->actingAs($this->recepcion)
            ->get(route(
                'historias-clinicas.documentos.archivo',
                $documento
            ))
            ->assertForbidden();

        $this
            ->actingAs($this->recepcion)
            ->get(route(
                'historias-clinicas.documentos.descargar',
                $documento
            ))
            ->assertForbidden();
    }

    public function test_archivo_inexistente_responde_con_404(): void
    {
        $documento = HistoriaClinicaDocumento::query()->create([
            'paciente_id' => $this->paciente->id,
            'generado_por' => $this->administrador->id,
            'archivo_path' => 'historias-clinicas/archivo-ausente.pdf',
            'archivo_nombre' => 'archivo-ausente.pdf',
            'mime_type' => 'application/pdf',
            'archivo_size' => 100,
            'generado_en' => now(),
        ]);

        $this
            ->actingAs($this->administrador)
            ->get(route(
                'historias-clinicas.documentos.archivo',
                $documento
            ))
            ->assertNotFound();

        $this
            ->actingAs($this->administrador)
            ->get(route(
                'historias-clinicas.documentos.descargar',
                $documento
            ))
            ->assertNotFound();
    }

    public function test_ficha_muestra_acciones_del_expediente_segun_rol(): void
    {
        $documento = $this->crearDocumentoAlmacenado();

        $this
            ->actingAs($this->administrador)
            ->get(route('pacientes.show', $this->paciente))
            ->assertOk()
            ->assertSee('Ver expediente')
            ->assertSee('Descargar expediente')
            ->assertSee(
                route(
                    'historias-clinicas.documentos.archivo',
                    $documento
                ),
                false
            );

        $this
            ->actingAs($this->usuarioMedico)
            ->get(route('pacientes.show', $this->paciente))
            ->assertOk()
            ->assertSee('Ver expediente')
            ->assertSee('Descargar expediente');

        $this
            ->actingAs($this->recepcion)
            ->get(route('pacientes.show', $this->paciente))
            ->assertOk()
            ->assertDontSee('Ver expediente')
            ->assertDontSee('Descargar expediente')
            ->assertDontSee('Generar expediente');
    }

    private function crearMedico(
        User $usuario,
        string $cedula
    ): Medicos {
        return Medicos::query()->create([
            'user_id' => $usuario->id,
            'nombre' => 'Médico',
            'apellido_paterno' => $cedula,
            'apellido_materno' => 'Prueba',
            'especialidad' => 'Medicina general',
            'cedula' => $cedula,
            'telefono' => '5550000900',
            'consultorio' => 'Consultorio PDF',
            'status' => true,
        ]);
    }

    private function crearDocumentoAlmacenado(): HistoriaClinicaDocumento
    {
        $ruta = 'historias-clinicas/'
            . $this->paciente->id
            . '/prueba.pdf';

        $contenido = "%PDF-1.4\nDocumento de prueba\n%%EOF";

        Storage::disk('local')->put(
            $ruta,
            $contenido
        );

        return HistoriaClinicaDocumento::query()->create([
            'paciente_id' => $this->paciente->id,
            'generado_por' => $this->administrador->id,
            'archivo_path' => $ruta,
            'archivo_nombre' => 'historia-clinica-prueba.pdf',
            'mime_type' => 'application/pdf',
            'archivo_size' => strlen($contenido),
            'generado_en' => now(),
        ]);
    }
}
