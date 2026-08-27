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


                    @if (
                    request()->user()->isAdmin()
                    || request()->user()->isMedico()
                    )

                    @include(
                    'pacientes.sections.estudios-clinicos'
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
    'pacientes.partials.estados-validacion'
)

    <script>
        /*
    |--------------------------------------------------------------------------
    | Funciones generales para modales
    |--------------------------------------------------------------------------
    */

        function abrirModal(
            idModal,
            idPrimerCampo = null
        ) {
            const modal = document.getElementById(idModal);

            if (!modal) {
                return;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');

            document.body.style.overflow = 'hidden';

            if (idPrimerCampo) {
                document
                    .getElementById(idPrimerCampo)
                    ?.focus();
            }
        }

        function cerrarModal(idModal) {
            const modal = document.getElementById(idModal);

            if (!modal) {
                return;
            }

            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');

            document.body.style.overflow = '';
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de exploración física
        |--------------------------------------------------------------------------
        */

        function abrirModalExploracionFisica() {
            abrirModal(
                'modal-exploracion-fisica',
                'exploracion_cita_id'
            );
        }

        function cerrarModalExploracionFisica() {
            cerrarModal('modal-exploracion-fisica');
        }

        function obtenerDatosExploracionesFisicas() {
            const elemento = document.getElementById(
                'datos-exploraciones-fisicas'
            );

            if (!elemento) {
                return {};
            }

            try {
                return JSON.parse(
                    elemento.textContent.trim() || '{}'
                );
            } catch (error) {
                console.error(
                    'No fue posible cargar las exploraciones:',
                    error
                );

                return {};
            }
        }

        function establecerTextoExploracion(
            idElemento,
            valor
        ) {
            const elemento = document.getElementById(
                idElemento
            );

            if (elemento) {
                elemento.textContent = valor;
            }
        }

        function actualizarFormularioExploracionFisica(
            conservarValores = false
        ) {
            const selector = document.getElementById(
                'exploracion_cita_id'
            );

            const formulario = document.getElementById(
                'form-exploracion-fisica'
            );

            const resumenSignos = document.getElementById(
                'resumen-signos-exploracion'
            );

            if (!selector || !formulario) {
                return;
            }

            const citaId = selector.value;

            const datos =
                obtenerDatosExploracionesFisicas();

            const datosCita = datos[citaId] ?? null;

            /*
            |--------------------------------------------------------------------------
            | Acción dinámica del formulario
            |--------------------------------------------------------------------------
            */

            if (citaId) {
                const plantilla =
                    formulario.dataset.routeTemplate;

                formulario.action = plantilla.replace(
                    '__CITA__',
                    citaId
                );
            } else {
                formulario.action = '#';
            }

            /*
            |--------------------------------------------------------------------------
            | Campos clínicos
            |--------------------------------------------------------------------------
            */

            const campos = [
                'interrogatorio',
                'anotaciones',
                'recomendaciones',
            ];

            if (!conservarValores) {
                campos.forEach(function(campo) {
                    const elemento = document.getElementById(
                        `exploracion_${campo}`
                    );

                    if (elemento) {
                        elemento.value =
                            datosCita?.campos?.[campo] ??
                            '';
                    }
                });

                document
                    .querySelectorAll(
                        '[data-exploracion-sistema]'
                    )
                    .forEach(function(elemento) {
                        const sistema =
                            elemento.dataset.exploracionSistema;

                        elemento.value =
                            datosCita?.sistemas?.[sistema] ??
                            '';
                    });
            }



            /*
            |--------------------------------------------------------------------------
            | Signos vitales
            |--------------------------------------------------------------------------
            */

            if (!citaId || !resumenSignos) {
                resumenSignos?.classList.add('hidden');

                return;
            }

            resumenSignos.classList.remove('hidden');

            const signos = datosCita?.signos ?? null;

            const sinSignos = document.getElementById(
                'exploracion_sin_signos'
            );

            sinSignos?.classList.toggle(
                'hidden',
                signos !== null
            );

            establecerTextoExploracion(
                'exploracion_signo_peso',
                signos?.peso ?
                `${signos.peso} kg` :
                '—'
            );

            const presion =
                signos?.presion_sistolica &&
                signos?.presion_diastolica ?
                `${signos.presion_sistolica}` +
                `/${signos.presion_diastolica}` :
                '—';

            establecerTextoExploracion(
                'exploracion_signo_presion',
                presion
            );

            establecerTextoExploracion(
                'exploracion_signo_fc',
                signos?.frecuencia_cardiaca ??
                '—'
            );

            establecerTextoExploracion(
                'exploracion_signo_fr',
                signos?.frecuencia_respiratoria ??
                '—'
            );

            establecerTextoExploracion(
                'exploracion_signo_temperatura',
                signos?.temperatura ?
                `${signos.temperatura} °C` :
                '—'
            );

            establecerTextoExploracion(
                'exploracion_signo_saturacion',
                signos?.saturacion_oxigeno ?
                `${signos.saturacion_oxigeno} %` :
                '—'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de hábitos alimenticios
        |--------------------------------------------------------------------------
        */

        function abrirModalHabitosAlimenticios() {
            abrirModal(
                'modal-habitos-alimenticios',
                'habito_comida_desayuno'
            );
        }

        function cerrarModalHabitosAlimenticios() {
            cerrarModal('modal-habitos-alimenticios');
        }

        /*
|--------------------------------------------------------------------------
| Modal de antecedentes ginecoobstétricos
|--------------------------------------------------------------------------
*/

        function abrirModalGinecoobstetricos() {
            abrirModal(
                'modal-ginecoobstetricos',
                'gineco_edad_menarca'
            );
        }

        function cerrarModalGinecoobstetricos() {
            cerrarModal(
                'modal-ginecoobstetricos'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de antecedentes personales no patológicos
        |--------------------------------------------------------------------------
        */

        function abrirModalPersonalesNoPatologicos() {
            abrirModal(
                'modal-personales-no-patologicos',
                'personal_no_patologico_casa_habitacion'
            );
        }

        function cerrarModalPersonalesNoPatologicos() {
            cerrarModal(
                'modal-personales-no-patologicos'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de antecedentes personales patológicos
        |--------------------------------------------------------------------------
        */

        function abrirModalPersonalesPatologicos() {
            abrirModal(
                'modal-personales-patologicos',
                'personal_patologico_enfermedades_infancia'
            );
        }

        function cerrarModalPersonalesPatologicos() {
            cerrarModal(
                'modal-personales-patologicos'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de antecedentes heredofamiliares
        |--------------------------------------------------------------------------
        */

        function abrirModalHeredofamiliares() {
            abrirModal(
                'modal-heredofamiliares',
                'numero_hermanos'
            );
        }

        function cerrarModalHeredofamiliares() {
            cerrarModal('modal-heredofamiliares');
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de datos generales
        |--------------------------------------------------------------------------
        */

        function abrirModalDatosGenerales() {
            abrirModal(
                'modal-datos-generales',
                'modal_nombre'
            );
        }

        function cerrarModalDatosGenerales() {
            cerrarModal('modal-datos-generales');
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de contacto
        |--------------------------------------------------------------------------
        */

        function abrirModalContacto() {
            abrirModal(
                'modal-contacto',
                'modal_telefono'
            );
        }

        function cerrarModalContacto() {
            cerrarModal('modal-contacto');
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de notas
        |--------------------------------------------------------------------------
        */

        function abrirModalNotas() {
            abrirModal(
                'modal-notas',
                'modal_notas'
            );
        }

        function cerrarModalNotas() {
            cerrarModal('modal-notas');
        }

        /*
        |--------------------------------------------------------------------------
        | Modal de historia clínica
        |--------------------------------------------------------------------------
        */

        function abrirModalHistoriaClinica() {
            abrirModal(
                'modal-historia-clinica',
                'patologia_base'
            );
        }

        function cerrarModalHistoriaClinica() {
            cerrarModal('modal-historia-clinica');
        }

        /*
        |--------------------------------------------------------------------------
        | Cerrar modales al presionar Escape
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'keydown',
            function(event) {
                if (event.key !== 'Escape') {
                    return;
                }

                cerrarModalDatosGenerales();
                cerrarModalContacto();
                cerrarModalNotas();
                cerrarModalHistoriaClinica();
                cerrarModalHeredofamiliares();
                cerrarModalPersonalesPatologicos();
                cerrarModalPersonalesNoPatologicos();
                cerrarModalHabitosAlimenticios();
                cerrarModalGinecoobstetricos();
                cerrarModalExploracionFisica();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Cerrar al hacer clic sobre el fondo
        |--------------------------------------------------------------------------
        */

        const modales = [{
                id: 'modal-datos-generales',
                cerrar: cerrarModalDatosGenerales,
            },
            {
                id: 'modal-contacto',
                cerrar: cerrarModalContacto,
            },
            {
                id: 'modal-notas',
                cerrar: cerrarModalNotas,
            },
            {
                id: 'modal-historia-clinica',
                cerrar: cerrarModalHistoriaClinica,
            },
            {
                id: 'modal-heredofamiliares',
                cerrar: cerrarModalHeredofamiliares,
            },
            {
                id: 'modal-personales-patologicos',
                cerrar: cerrarModalPersonalesPatologicos,
            },
            {
                id: 'modal-personales-no-patologicos',
                cerrar: cerrarModalPersonalesNoPatologicos,
            },
            {
                id: 'modal-habitos-alimenticios',
                cerrar: cerrarModalHabitosAlimenticios,
            },
            {
                id: 'modal-exploracion-fisica',
                cerrar: cerrarModalExploracionFisica,
            },

            {
                id: 'modal-habitos-alimenticios',
                cerrar: cerrarModalHabitosAlimenticios,
            },
        ];

        modales.forEach(function(configuracion) {
            document
                .getElementById(configuracion.id)
                ?.addEventListener(
                    'click',
                    function(event) {
                        if (event.target === this) {
                            configuracion.cerrar();
                        }
                    }
                );
        });

        /*
        |--------------------------------------------------------------------------
        | Inicialización y errores de validación
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'DOMContentLoaded',
            function() {
                /*
                |--------------------------------------------------------------------------
                | Historia clínica principal
                |--------------------------------------------------------------------------
                */

                /*
|--------------------------------------------------------------------------
| Mover modal ginecoobstétrico al nivel del body
|--------------------------------------------------------------------------
|
| Evita que contenedores superiores limiten el fondo del modal.
|
*/

                const modalGinecoobstetricos =
                    document.getElementById(
                        'modal-ginecoobstetricos'
                    );

                if (modalGinecoobstetricos) {
                    document.body.appendChild(
                        modalGinecoobstetricos
                    );
                }

                const estadoValidacion =
                    document.getElementById(
                        'estado-validacion-historia'
                    );

                const tieneErrores =
                    estadoValidacion
                    ?.dataset.tieneErrores === 'true';

                /*
                |--------------------------------------------------------------------------
                | Antecedentes heredofamiliares
                |--------------------------------------------------------------------------
                */

                const estadoHeredofamiliares =
                    document.getElementById(
                        'estado-validacion-heredofamiliares'
                    );

                const tieneErroresHeredofamiliares =
                    estadoHeredofamiliares
                    ?.dataset.tieneErrores === 'true';

                /*
                |--------------------------------------------------------------------------
                | Antecedentes personales patológicos
                |--------------------------------------------------------------------------
                */

                const estadoPersonalesPatologicos =
                    document.getElementById(
                        'estado-validacion-personales-patologicos'
                    );

                const tieneErroresPersonalesPatologicos =
                    estadoPersonalesPatologicos
                    ?.dataset.tieneErrores === 'true';

                /*
                |--------------------------------------------------------------------------
                | Antecedentes personales no patológicos
                |--------------------------------------------------------------------------
                */

                const estadoPersonalesNoPatologicos =
                    document.getElementById(
                        'estado-validacion-personales-no-patologicos'
                    );

                const tieneErroresPersonalesNoPatologicos =
                    estadoPersonalesNoPatologicos
                    ?.dataset.tieneErrores === 'true';

                /*
                |--------------------------------------------------------------------------
                | Hábitos alimenticios
                |--------------------------------------------------------------------------
                */

                const estadoHabitosAlimenticios =
                    document.getElementById(
                        'estado-validacion-habitos-alimenticios'
                    );

                const tieneErroresHabitosAlimenticios =
                    estadoHabitosAlimenticios
                    ?.dataset.tieneErrores === 'true';

                /*
                |--------------------------------------------------------------------------
                | Exploración física
                |--------------------------------------------------------------------------
                */

                const estadoExploracionFisica =
                    document.getElementById(
                        'estado-validacion-exploracion-fisica'
                    );

                const tieneErroresExploracionFisica =
                    estadoExploracionFisica
                    ?.dataset.tieneErrores === 'true';

                const selectorExploracion =
                    document.getElementById(
                        'exploracion_cita_id'
                    );

                selectorExploracion?.addEventListener(
                    'change',
                    function() {
                        actualizarFormularioExploracionFisica();
                    }
                );

                /*
                |--------------------------------------------------------------------------
                | Antecedentes ginecoobstétricos
                |--------------------------------------------------------------------------
                */

                const estadoGinecoobstetricos =
                    document.getElementById(
                        'estado-validacion-ginecoobstetricos'
                    );

                const tieneErroresGinecoobstetricos =
                    estadoGinecoobstetricos
                    ?.dataset.tieneErrores === 'true';

                if (selectorExploracion?.value) {
                    actualizarFormularioExploracionFisica(
                        tieneErroresExploracionFisica
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Reabrir el modal correspondiente
                |--------------------------------------------------------------------------
                */

                if (tieneErroresExploracionFisica) {
                    abrirModalExploracionFisica();
                }

                if (tieneErroresHabitosAlimenticios) {
                    abrirModalHabitosAlimenticios();
                }

                if (tieneErroresPersonalesNoPatologicos) {
                    abrirModalPersonalesNoPatologicos();
                }

                if (tieneErroresPersonalesPatologicos) {
                    abrirModalPersonalesPatologicos();
                }

                if (tieneErroresHeredofamiliares) {
                    abrirModalHeredofamiliares();
                }

                if (tieneErrores) {
                    abrirModalHistoriaClinica();
                }

                if (tieneErroresGinecoobstetricos) {
                    abrirModalGinecoobstetricos();
                }
            }
        );
    </script>
</x-app-layout>