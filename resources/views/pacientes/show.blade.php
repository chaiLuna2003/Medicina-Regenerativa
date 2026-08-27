<x-app-layout>

    <x-slot name="header">
        @include(
        'pacientes.sections.encabezado'
        )
    </x-slot>

    @include(
    'pacientes.sections.mensajes-sistema'
    )

    <div class="py-8">
        <div class="mx-auto max-w-[1500px] px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

                {{-- ========================================================= --}}
                {{-- COLUMNA IZQUIERDA --}}
                {{-- ========================================================= --}}
                <aside class="space-y-4 lg:col-span-4">

                    @include(
                    'pacientes.sections.perfil'
                    )

                    @include(
                    'pacientes.sections.datos-generales'
                    )

                    @include(
                    'pacientes.sections.contacto'
                    )

                    @include(
                    'pacientes.sections.notas'
                    )

                </aside>

                {{-- ========================================================= --}}
                {{-- COLUMNA DERECHA --}}
                {{-- ========================================================= --}}

                <main class="space-y-4 lg:col-span-8">

                    @include(
                    'pacientes.sections.historia-clinica'
                    )

                    @include(
                    'pacientes.sections.antecedentes-heredofamiliares'
                    )


                    @include(
                    'pacientes.sections.antecedentes-personales-patologicos'
                    )

                    @include(
                    'pacientes.sections.antecedentes-personales-no-patologicos'
                    )

                    {{-- ===================================================== --}}
                    {{-- ANTECEDENTES GINECOOBSTÉTRICOS --}}
                    {{-- Solo se muestran para pacientes femeninas --}}
                    {{-- ===================================================== --}}

                    @if ($pacientes->sexo === 'femenino')
                    @include(
                    'pacientes.partials.ginecoobstetricos'
                    )
                    @endif

                    @include(
                    'pacientes.sections.habitos-alimenticios'
                    )

                    @include(
                    'pacientes.sections.exploraciones-fisicas'
                    )

                    @include(
                    'pacientes.sections.resumen-clinico'
                    )

                    @include(
                    'pacientes.sections.historial-citas'
                    )


                    {{-- Estudios clínicos --}}
                    @if (
                    request()->user()->isAdmin()
                    || request()->user()->isMedico()
                    || request()->user()->isRecepcionista()
                    )

                    @include(
                    'pacientes.sections.estudios-clinicos'
                    )

                    @endif


                    {{-- Recetas médicas --}}
                    @if (
                    request()->user()->isAdmin()
                    || request()->user()->isMedico()
                    )

                    @include(
                    'pacientes.sections.recetas-medicas'
                    )

                    @endif


                    @include(
                    'pacientes.sections.signos-vitales'
                    )
                </main>
            </div>
        </div>
    </div>



    @include(
    'pacientes.modals.contacto'
    )

    @include(
    'pacientes.modals.datos-generales'
    )


    @include(
    'pacientes.modals.notas'
    )

    @include(
    'pacientes.modals.historia-clinica'
    )

    @include(
    'pacientes.modals.antecedentes-heredofamiliares'
    )

    @include(
    'pacientes.modals.antecedentes-personales-patologicos'
    )


    @include(
    'pacientes.modals.antecedentes-personales-no-patologicos'
    )
    @include(
    'pacientes.modals.habitos-alimenticios'
    )
    @include(
    'pacientes.modals.exploracion-fisica'
    )

        @include(
        'pacientes.modals.exploracion-fisica'
    )

    @include(
        'pacientes.modals.estudios'
    )



    @include(
    'pacientes.partials.estados-validacion'
    )

    @include(
    'pacientes.scripts.show'
    )
</x-app-layout>