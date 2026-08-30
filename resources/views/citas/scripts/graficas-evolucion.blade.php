@if (
    $puedeConsultarInformacionClinica
    && $cita->evolucionClinica?->casoClinico
)
    <script>
        const datosGraficasEvolucion =
            {{ Illuminate\Support\Js::from(
                $datosGraficasCaso
            ) }};

        let instanciasGraficasEvolucion = [];

        function destruirGraficasEvolucion() {
            instanciasGraficasEvolucion.forEach(
                function(instancia) {
                    instancia.destroy();
                }
            );

            instanciasGraficasEvolucion = [];
        }

        function contieneDatos(valores) {
            return Array.isArray(valores)
                && valores.some(
                    valor =>
                        valor !== null
                        && valor !== ''
                );
        }

        function mostrarGraficaVacia(
            elemento,
            mensaje = 'Sin registros suficientes'
        ) {
            elemento.innerHTML = `
                <div
                    class="flex min-h-[280px] items-center
                           justify-center rounded-xl
                           border border-dashed border-slate-200
                           bg-slate-50 px-6 text-center">
                    <div>
                        <p class="font-semibold text-slate-600">
                            ${mensaje}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            La gráfica aparecerá cuando enfermería
                            registre este parámetro.
                        </p>
                    </div>
                </div>
            `;
        }

        function opcionesBase(
            series,
            colores,
            unidad = ''
        ) {
            return {
                chart: {
                    type: 'area',
                    height: 280,
                    fontFamily: 'inherit',
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 450
                    }
                },

                series: series,

                colors: colores,

                stroke: {
                    curve: 'smooth',
                    width: 3
                },

                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.35,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },

                markers: {
                    size: 4,
                    strokeWidth: 2,
                    hover: {
                        size: 6
                    }
                },

                dataLabels: {
                    enabled: false
                },

                grid: {
                    borderColor: '#E2E8F0',
                    strokeDashArray: 4
                },

                xaxis: {
                    categories:
                        datosGraficasEvolucion
                            .categorias
                            ?? [],

                    labels: {
                        rotate: -35,
                        trim: true,
                        style: {
                            colors: '#64748B',
                            fontSize: '11px'
                        }
                    },

                    axisBorder: {
                        color: '#CBD5E1'
                    },

                    axisTicks: {
                        color: '#CBD5E1'
                    }
                },

                yaxis: {
                    labels: {
                        formatter: function(valor) {
                            if (valor === null) {
                                return '';
                            }

                            return Number(valor)
                                .toFixed(1)
                                + unidad;
                        },

                        style: {
                            colors: '#64748B',
                            fontSize: '11px'
                        }
                    }
                },

                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(valor) {
                            if (valor === null) {
                                return 'Sin registro';
                            }

                            return Number(valor)
                                .toFixed(2)
                                + unidad;
                        }
                    }
                },

                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    fontSize: '12px'
                },

                noData: {
                    text: 'Sin datos'
                }
            };
        }

        function crearGraficaSimple(
            clave,
            nombre,
            valores,
            color,
            unidad
        ) {
            const elemento = document.querySelector(
                `[data-grafica-evolucion="${clave}"]`
            );

            if (!elemento) {
                return;
            }

            elemento.innerHTML = '';

            if (!contieneDatos(valores)) {
                mostrarGraficaVacia(elemento);
                return;
            }

            const opciones = opcionesBase(
                [
                    {
                        name: nombre,
                        data: valores
                    }
                ],
                [color],
                unidad
            );

            const grafica =
                new window.ApexCharts(
                    elemento,
                    opciones
                );

            grafica.render();

            instanciasGraficasEvolucion.push(
                grafica
            );
        }

        function crearGraficaPesoImc() {
            const elemento = document.querySelector(
                '[data-grafica-evolucion="peso_imc"]'
            );

            if (!elemento) {
                return;
            }

            elemento.innerHTML = '';

            const peso =
                datosGraficasEvolucion.peso ?? [];

            const imc =
                datosGraficasEvolucion.imc ?? [];

            if (
                ! contieneDatos(peso)
                && ! contieneDatos(imc)
            ) {
                mostrarGraficaVacia(elemento);
                return;
            }

            const opciones = opcionesBase(
                [
                    {
                        name: 'Peso',
                        data: peso
                    },
                    {
                        name: 'IMC',
                        data: imc
                    }
                ],
                [
                    '#10B981',
                    '#2563EB'
                ]
            );

            opciones.yaxis = [
                {
                    title: {
                        text: 'Peso (kg)'
                    },

                    labels: {
                        formatter: valor =>
                            Number(valor).toFixed(1)
                            + ' kg'
                    }
                },
                {
                    opposite: true,

                    title: {
                        text: 'IMC'
                    },

                    labels: {
                        formatter: valor =>
                            Number(valor).toFixed(1)
                    }
                }
            ];

            const grafica =
                new window.ApexCharts(
                    elemento,
                    opciones
                );

            grafica.render();

            instanciasGraficasEvolucion.push(
                grafica
            );
        }

        function crearGraficaPresion() {
            const elemento = document.querySelector(
                '[data-grafica-evolucion="presion"]'
            );

            if (!elemento) {
                return;
            }

            elemento.innerHTML = '';

            const sistolica =
                datosGraficasEvolucion
                    .presion_sistolica
                    ?? [];

            const diastolica =
                datosGraficasEvolucion
                    .presion_diastolica
                    ?? [];

            if (
                ! contieneDatos(sistolica)
                && ! contieneDatos(diastolica)
            ) {
                mostrarGraficaVacia(elemento);
                return;
            }

            const opciones = opcionesBase(
                [
                    {
                        name: 'Sistólica',
                        data: sistolica
                    },
                    {
                        name: 'Diastólica',
                        data: diastolica
                    }
                ],
                [
                    '#EF4444',
                    '#EC4899'
                ],
                ' mmHg'
            );

            const grafica =
                new window.ApexCharts(
                    elemento,
                    opciones
                );

            grafica.render();

            instanciasGraficasEvolucion.push(
                grafica
            );
        }

        function renderizarGraficasEvolucion() {
            if (!window.ApexCharts) {
                return;
            }

            destruirGraficasEvolucion();

            crearGraficaPesoImc();
            crearGraficaPresion();

            crearGraficaSimple(
                'frecuencia_cardiaca',
                'Frecuencia cardiaca',
                datosGraficasEvolucion
                    .frecuencia_cardiaca
                    ?? [],
                '#F43F5E',
                ' lpm'
            );

            crearGraficaSimple(
                'frecuencia_respiratoria',
                'Frecuencia respiratoria',
                datosGraficasEvolucion
                    .frecuencia_respiratoria
                    ?? [],
                '#8B5CF6',
                ' rpm'
            );

            crearGraficaSimple(
                'saturacion_oxigeno',
                'Saturación de oxígeno',
                datosGraficasEvolucion
                    .saturacion_oxigeno
                    ?? [],
                '#F59E0B',
                '%'
            );

            crearGraficaSimple(
                'temperatura',
                'Temperatura',
                datosGraficasEvolucion
                    .temperatura
                    ?? [],
                '#D97706',
                ' °C'
            );

            crearGraficaSimple(
                'glucosa',
                'Glucosa',
                datosGraficasEvolucion
                    .glucosa
                    ?? [],
                '#06B6D4',
                ' mg/dL'
            );

            crearGraficaSimple(
                'estatura',
                'Estatura',
                datosGraficasEvolucion
                    .estatura
                    ?? [],
                '#0EA5E9',
                ' cm'
            );
        }

        document.addEventListener(
            'modal-clinico:abierto',
            function(event) {
                if (
                    event.detail.nombre
                    !== 'graficas-evolucion'
                ) {
                    return;
                }

                window.requestAnimationFrame(
                    function() {
                        renderizarGraficasEvolucion();
                    }
                );
            }
        );
    </script>
@endif