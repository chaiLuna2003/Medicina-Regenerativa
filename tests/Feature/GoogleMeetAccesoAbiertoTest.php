<?php

namespace Tests\Feature;

use App\Models\Citas;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class GoogleMeetAccesoAbiertoTest extends TestCase
{
    private function cita(): Citas
    {
        config()->set('services.google_calendar', [
            'client_id' => 'client-test',
            'client_secret' => 'secret-test',
            'refresh_token' => 'refresh-test',
            'calendar_id' => 'primary',
            'timezone' => 'America/Mexico_City',
        ]);

        return new Citas([
            'fecha' => '2026-10-01',
            'hora' => '12:00:00',
            'duracion_minutos' => 30,
        ]);
    }

    public function test_crea_la_sala_y_confirma_acceso_abierto_antes_de_entregar_enlace(): void
    {
        Http::fake(function (Request $request) {
            if (str_contains($request->url(), 'oauth2.googleapis.com/token')) {
                return Http::response(['access_token' => 'access-test']);
            }

            if ($request->method() === 'POST' && str_contains($request->url(), '/calendar/v3/')) {
                return Http::response([
                    'id' => 'evento-1',
                    'hangoutLink' => 'https://meet.google.com/abc-defg-hij',
                ]);
            }

            if ($request->method() === 'GET' && str_contains($request->url(), 'meet.googleapis.com')) {
                return Http::response(['name' => 'spaces/space123']);
            }

            if ($request->method() === 'PATCH' && str_contains($request->url(), 'meet.googleapis.com')) {
                return Http::response(['config' => ['accessType' => 'OPEN']]);
            }

            return Http::response([], 404);
        });

        $resultado = app(GoogleCalendarService::class)->crearVideoconsulta($this->cita());

        $this->assertSame('https://meet.google.com/abc-defg-hij', $resultado['meet_url']);

        Http::assertSent(fn (Request $request) => $request->method() === 'PATCH'
            && $request->url() === 'https://meet.googleapis.com/v2/spaces/space123?updateMask=config.accessType'
            && $request['config']['accessType'] === 'OPEN');
    }

    public function test_no_entrega_una_sala_restringida_si_meet_rechaza_el_cambio(): void
    {
        Http::fake(function (Request $request) {
            if (str_contains($request->url(), 'oauth2.googleapis.com/token')) {
                return Http::response(['access_token' => 'access-test']);
            }

            if ($request->method() === 'POST') {
                return Http::response([
                    'id' => 'evento-2',
                    'hangoutLink' => 'https://meet.google.com/abc-defg-hij',
                ]);
            }

            if ($request->method() === 'GET') {
                return Http::response(['name' => 'spaces/space123']);
            }

            if ($request->method() === 'PATCH') {
                return Http::response(['error' => 'forbidden'], 403);
            }

            return Http::response([], 204);
        });

        try {
            app(GoogleCalendarService::class)->crearVideoconsulta($this->cita());
            $this->fail('Debió rechazar la sala sin acceso abierto.');
        } catch (RuntimeException $exception) {
            $this->assertSame('No fue posible abrir el acceso de Google Meet.', $exception->getMessage());
        }

        Http::assertSent(fn (Request $request) => $request->method() === 'DELETE'
            && str_contains($request->url(), '/events/evento-2?sendUpdates=all'));
    }
}
