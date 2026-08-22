<?php

namespace App\Services;

use App\Models\Citas;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GoogleCalendarService
{
    

    /**
     * Crea un evento de Google Calendar y solicita
     * una sala nueva de Google Meet.
     *
     * @return array{
     *     event_id: string,
     *     meet_url: ?string,
     *     calendar_url: ?string
     * }
     */
    public function crearVideoconsulta(Citas $cita): array
    {
        $cita->loadMissing([
            'paciente',
            'medico',
        ]);

        $inicio = Carbon::parse(
            $cita->fecha->format('Y-m-d')
                . ' '
                . $cita->hora,
            $this->timezone()
        );

        $correos = collect([
            $cita->paciente?->email,
            $cita->medico?->user?->email,
        ])
            ->filter(
                fn(?string $correo) =>
                filter_var(
                    $correo,
                    FILTER_VALIDATE_EMAIL
                )
            )
            ->unique()
            ->map(
                fn(string $correo) => [
                    'email' => $correo,
                ]
            )
            ->values()
            ->all();

        $cliente = $this->cliente();

        $respuesta = $cliente->post(
            $this->eventosUrl()
                . '?conferenceDataVersion=1'
                . '&sendUpdates=all',
            [
                'summary' => 'Videoconsulta médica',

                'description' =>
                'Videoconsulta programada desde '
                    . 'Medicina Regenerativa.',

                'start' => [
                    'dateTime' =>
                    $inicio->toRfc3339String(),

                    'timeZone' =>
                    $this->timezone(),
                ],

                'end' => [
                    'dateTime' =>
                    $inicio
                        ->copy()
                        ->addMinutes(
                            $cita->duracion_minutos ?? 15
                        )
                        ->toRfc3339String(),

                    'timeZone' =>
                    $this->timezone(),
                ],

                'attendees' => $correos,

                'conferenceData' => [
                    'createRequest' => [
                        'requestId' =>
                        'cita-'
                            . $cita->id
                            . '-'
                            . Str::uuid(),

                        'conferenceSolutionKey' => [
                            'type' => 'hangoutsMeet',
                        ],
                    ],
                ],
            ]
        );

        if ($respuesta->failed()) {
            throw new RuntimeException(
                'Google Calendar no pudo crear '
                    . 'la videoconsulta.'
            );
        }

        $evento = $respuesta->json();

        if (!isset($evento['id'])) {
            throw new RuntimeException(
                'Google Calendar no devolvió '
                    . 'el identificador del evento.'
            );
        }

        $meetUrl = $this->extraerMeetUrl(
            $evento
        );

        /*
         * Google puede tardar unos instantes
         * en terminar de crear la conferencia.
         */
        for (
            $intento = 0;
            $meetUrl === null && $intento < 3;
            $intento++
        ) {
            usleep(400000);

            $consulta = $cliente->get(
                $this->eventoUrl($evento['id'])
                    . '?conferenceDataVersion=1'
            );

            if ($consulta->successful()) {
                $evento = $consulta->json();

                $meetUrl = $this->extraerMeetUrl(
                    $evento
                );
            }
        }

        return [
            'event_id' => $evento['id'],
            'meet_url' => $meetUrl,
            'calendar_url' =>
            $evento['htmlLink'] ?? null,
        ];
    }

    /**
     * Consulta un evento cuando el enlace de Meet
     * todavía se encontraba pendiente.
     *
     * @return array{
     *     meet_url: ?string,
     *     calendar_url: ?string
     * }
     */
    public function consultarVideoconsulta(
        Citas $cita
    ): array {
        if (blank($cita->google_event_id)) {
            throw new RuntimeException(
                'La cita todavía no tiene '
                    . 'un evento de Google asociado.'
            );
        }

        $respuesta = $this
            ->cliente()
            ->get(
                $this->eventoUrl(
                    $cita->google_event_id
                )
                    . '?conferenceDataVersion=1'
            );

        if ($respuesta->failed()) {
            throw new RuntimeException(
                'No fue posible consultar el evento '
                    . 'de Google Calendar.'
            );
        }

        $evento = $respuesta->json();

        return [
            'meet_url' =>
            $this->extraerMeetUrl($evento),

            'calendar_url' =>
            $evento['htmlLink'] ?? null,
        ];
    }

    /**
     * Actualiza la fecha, hora y participantes
     * del evento existente en Google Calendar.
     */
    public function actualizarVideoconsulta(
        Citas $cita
    ): void {
        if (blank($cita->google_event_id)) {
            throw new RuntimeException(
                'La videoconsulta no tiene '
                    . 'un evento de Google asociado.'
            );
        }

        $cita->loadMissing([
            'paciente',
            'medico',
        ]);

        $inicio = Carbon::parse(
            $cita->fecha->format('Y-m-d')
                . ' '
                . $cita->hora,
            $this->timezone()
        );

        $correos = collect([
            $cita->paciente?->email,
            $cita->medico?->user?->email,
        ])
            ->filter(
                fn(?string $correo) =>
                filter_var(
                    $correo,
                    FILTER_VALIDATE_EMAIL
                )
            )
            ->unique()
            ->map(
                fn(string $correo) => [
                    'email' => $correo,
                ]
            )
            ->values()
            ->all();

        $respuesta = $this
            ->cliente()
            ->patch(
                $this->eventoUrl(
                    $cita->google_event_id
                )
                    . '?conferenceDataVersion=1'
                    . '&sendUpdates=all',
                [
                    'summary' =>
                    'Videoconsulta médica',

                    'description' =>
                    'Videoconsulta programada '
                        . 'desde Medicina Regenerativa.',

                    'start' => [
                        'dateTime' =>
                        $inicio
                            ->toRfc3339String(),

                        'timeZone' =>
                        $this->timezone(),
                    ],

                    'end' => [
                        'dateTime' =>
                        $inicio
                            ->copy()
                            ->addMinutes(
                                $cita->duracion_minutos ?? 15
                            )
                            ->toRfc3339String(),

                        'timeZone' =>
                        $this->timezone(),
                    ],

                    'attendees' => $correos,
                ]
            );

        if ($respuesta->failed()) {
            throw new RuntimeException(
                'Google Calendar no pudo actualizar '
                    . 'la videoconsulta.'
            );
        }
    }

    /**
     * Elimina el evento de Google Calendar cuando
     * la videoconsulta se cancela.
     */
    public function cancelarVideoconsulta(
        Citas $cita
    ): void {
        if (blank($cita->google_event_id)) {
            return;
        }

        $respuesta = $this
            ->cliente()
            ->delete(
                $this->eventoUrl(
                    $cita->google_event_id
                )
                    . '?sendUpdates=all'
            );

        /*
     * 404 significa que el evento ya había sido
     * eliminado. Para nuestra aplicación también
     * cuenta como cancelado.
     */
        if (
            $respuesta->failed()
            && $respuesta->status() !== 404
        ) {
            throw new RuntimeException(
                'Google Calendar no pudo cancelar '
                    . 'la videoconsulta.'
            );
        }
    }

    /**
     * Cliente autenticado para Google Calendar.
     */
    private function cliente(): PendingRequest
    {
        return Http::acceptJson()
            ->asJson()
            ->withToken(
                $this->accessToken()
            )
            ->timeout(20);
    }

    /**
     * Genera un access token temporal utilizando
     * el refresh token guardado en .env.
     */
    private function accessToken(): string
    {
        $config = config(
            'services.google_calendar'
        );

        foreach (
            [
                'client_id',
                'client_secret',
                'refresh_token',
            ] as $campo
        ) {
            if (blank($config[$campo] ?? null)) {
                throw new RuntimeException(
                    'Falta configurar Google Calendar '
                        . 'en el archivo .env.'
                );
            }
        }

        $respuesta = Http::asForm()
            ->timeout(15)
            ->post(
                'https://oauth2.googleapis.com/token',
                [
                    'client_id' =>
                    $config['client_id'],

                    'client_secret' =>
                    $config['client_secret'],

                    'refresh_token' =>
                    $config['refresh_token'],

                    'grant_type' =>
                    'refresh_token',
                ]
            );

        $token = $respuesta->json(
            'access_token'
        );

        if (
            $respuesta->failed()
            || !is_string($token)
        ) {
            throw new RuntimeException(
                'No fue posible autenticar '
                    . 'la cuenta de Google Calendar.'
            );
        }

        return $token;
    }

    /**
     * URL de la colección de eventos.
     */
    private function eventosUrl(): string
    {
        $calendarId = rawurlencode(
            (string) config(
                'services.google_calendar.calendar_id',
                'primary'
            )
        );

        return
            'https://www.googleapis.com/calendar/v3'
            . '/calendars/'
            . $calendarId
            . '/events';
    }

    /**
     * URL de un evento específico.
     */
    private function eventoUrl(
        string $eventId
    ): string {
        return $this->eventosUrl()
            . '/'
            . rawurlencode($eventId);
    }

    /**
     * Obtiene el enlace de video devuelto por Google.
     */
    private function extraerMeetUrl(
        array $evento
    ): ?string {
        $puntoVideo = collect(
            $evento['conferenceData']['entryPoints']
                ?? []
        )->firstWhere(
            'entryPointType',
            'video'
        );

        return $evento['hangoutLink']
            ?? data_get(
                $puntoVideo,
                'uri'
            );
    }

    private function timezone(): string
    {
        return (string) config(
            'services.google_calendar.timezone',
            'America/Mexico_City'
        );
    }
}
