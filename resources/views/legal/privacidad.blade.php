<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Información sobre el tratamiento de datos en SW Clínico y su integración con Google Calendar y Meet.">
    <title>Privacidad | SW Clínico</title>
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
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Privacidad</h1>
        <p class="mt-5 leading-7">SW Clínico es una plataforma de gestión de pacientes, citas y expedientes para personal autorizado. Para consultas relacionadas con este aviso o con el uso de datos en la plataforma, escribe a <a class="text-teal-800 underline" href="mailto:angelinpilin90@gmail.com">angelinpilin90@gmail.com</a>.</p>

        <div class="mt-10 space-y-8 leading-7">
            <section>
                <h2 class="text-lg font-semibold text-slate-950">Datos y finalidades</h2>
                <p class="mt-2">La plataforma puede gestionar datos de identificación y contacto, citas, expedientes y datos de salud que el personal autorizado registre para organizar la atención médica. El acceso se asigna según el rol de cada usuario. La institución o profesional que atiende al paciente debe informarle sobre el tratamiento de su expediente clínico y gestionar los permisos que correspondan.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-slate-950">Google Calendar y videollamadas</h2>
                <p class="mt-2">Cuando se agenda una videoconsulta, SW Clínico usa el calendario de la cuenta anfitriona autorizada para crear, consultar, actualizar o cancelar el evento y generar un enlace de Google Meet. El evento incluye una descripción general, fecha, hora y duración; puede incluir como invitados los correos del paciente y del médico, si están disponibles. Google puede enviarles invitaciones y actualizaciones del evento. La aplicación conserva el identificador del evento y el enlace de la reunión para gestionar la cita. No envía el diagnóstico ni el expediente clínico en la descripción del evento.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-slate-950">Uso y conservación</h2>
                <p class="mt-2">El acceso a Google Calendar se utiliza para gestionar las videoconsultas de la plataforma; los datos obtenidos mediante esa integración no se usan para publicidad ni se venden. Los registros de la plataforma se conservan conforme a las necesidades de operación y las obligaciones de conservación aplicables. Google trata los datos que recibe conforme a sus propias condiciones y políticas.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-slate-950">Consultas y solicitudes</h2>
                <p class="mt-2">Para solicitar información, corrección o eliminación de datos, o plantear una duda sobre privacidad, escribe a <a class="text-teal-800 underline" href="mailto:angelinpilin90@gmail.com">angelinpilin90@gmail.com</a>. La atención de cada solicitud estará sujeta a la identidad del solicitante y a las obligaciones legales de conservación de información clínica.</p>
            </section>
        </div>
    </main>
    <footer class="border-t border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-3xl flex-wrap gap-x-5 gap-y-2 px-5 py-6 text-sm text-slate-600" aria-label="Información legal">
            <a href="{{ url('/') }}" class="hover:text-teal-800">Inicio</a>
            <a href="{{ route('legal.terminos') }}" class="hover:text-teal-800">Condiciones del servicio</a>
        </nav>
    </footer>
</body>
</html>
