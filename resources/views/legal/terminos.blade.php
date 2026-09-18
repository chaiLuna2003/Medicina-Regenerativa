<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Condiciones generales de uso de SW Clínico.">
    <title>Condiciones del servicio | SW Clínico</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-3xl items-center justify-between gap-4 px-5 py-5">
            <a href="{{ url('/') }}" class="font-semibold text-teal-800">SW Clínico</a>
            <a href="{{ url('/') }}" class="text-sm text-slate-600 hover:text-teal-800">Volver al inicio</a>
        </div>
    </header>
    <main class="mx-auto max-w-3xl px-5 py-10 sm:py-16">
        <p class="text-xs font-semibold uppercase tracking-widest text-teal-700">Información pública</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Condiciones del servicio</h1>
        <p class="mt-5 leading-7">SW Clínico facilita la gestión de pacientes, citas, expedientes y videoconsultas para los profesionales y establecimientos que tienen acceso autorizado.</p>

        <div class="mt-10 space-y-8 leading-7">
            <section>
                <h2 class="text-lg font-semibold text-slate-950">Acceso y uso</h2>
                <p class="mt-2">Cada usuario debe utilizar únicamente su propia cuenta, proteger sus credenciales y acceder solo a la información necesaria para sus funciones. El registro de información clínica corresponde al personal autorizado y a la institución o profesional que presta la atención.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-slate-950">Videoconsultas</h2>
                <p class="mt-2">Las citas de video pueden generar eventos en Google Calendar y enlaces de Google Meet desde la cuenta anfitriona configurada. La recepción y el uso de las invitaciones también están sujetos a las condiciones de Google. La disponibilidad del enlace depende de esos servicios y de la conexión a internet de los participantes.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-slate-950">Atención médica</h2>
                <p class="mt-2">La plataforma sirve para organizar información y comunicaciones; las decisiones clínicas y la atención médica corresponden a los profesionales responsables. Una cita o un enlace de videollamada no sustituye la atención de urgencias.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-slate-950">Contacto</h2>
                <p class="mt-2">Para dudas sobre el servicio, escribe a <a class="text-teal-800 underline" href="mailto:angelinpilin90@gmail.com">angelinpilin90@gmail.com</a>. Consulta también nuestra <a class="text-teal-800 underline" href="{{ route('legal.privacidad') }}">información de privacidad</a>.</p>
            </section>
        </div>
    </main>
    <footer class="border-t border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-3xl flex-wrap gap-x-5 gap-y-2 px-5 py-6 text-sm text-slate-600" aria-label="Información legal">
            <a href="{{ url('/') }}" class="hover:text-teal-800">Inicio</a>
            <a href="{{ route('legal.privacidad') }}" class="hover:text-teal-800">Privacidad</a>
        </nav>
    </footer>
</body>
</html>
