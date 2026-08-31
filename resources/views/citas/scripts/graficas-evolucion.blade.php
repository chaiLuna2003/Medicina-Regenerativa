@if (
    $puedeConsultarInformacionClinica
    && $cita->evolucionClinica?->casoClinico
)
    <script>
        const datosGraficasEvolucion =
            {{ Illuminate\Support\Js::from(
                $datosGraficasCaso
            ) }};

        const configuracionGraficasEvolucion = {
            peso_imc: {
                titulo: 'Peso e IMC',
                descripcion:
                    'Cambios de composición corporal durante el seguimiento.',
                series: [
                    {
                        clave: 'peso',
                        nombre: 'Peso',
                        unidad: 'kg',
                        color: '#0F766E',
                        decimales: 1
                    },
                    {
                        clave: 'imc',
                        nombre: 'IMC',
                        unidad: '',
                        color: '#2563EB',
                        decimales: 1
                    }
                ],
                ejesSeparados: true
            },

            presion: {
                titulo: 'Presión arterial',
                descripcion:
                    'Presión sistólica y diastólica en una misma lectura.',
                series: [
                    {
                        clave: 'presion_sistolica',
                        nombre: 'Sistólica',
                        unidad: 'mmHg',
                        color: '#DC2626',
                        decimales: 0
                    },
                    {
                        clave: 'presion_diastolica',
                        nombre: 'Diastólica',
                        unidad: 'mmHg',
                        color: '#F97316',
                        decimales: 0
                    }
                ]
            },

            frecuencia_cardiaca: {
                titulo: 'Frecuencia cardiaca',
                descripcion:
                    'Latidos por minuto registrados por Enfermería.',
                series: [
                    {
                        clave: 'frecuencia_cardiaca',
                        nombre: 'Frecuencia cardiaca',
                        unidad: 'lpm',
                        color: '#E11D48',
                        decimales: 0
                    }
                ]
            },

            frecuencia_respiratoria: {
                titulo: 'Frecuencia respiratoria',
                descripcion:
                    'Respiraciones por minuto durante el seguimiento.',
                series: [
                    {
                        clave: 'frecuencia_respiratoria',
                        nombre: 'Frecuencia respiratoria',
                        unidad: 'rpm',
                        color: '#7C3AED',
                        decimales: 0
                    }
                ]
            },

            saturacion_oxigeno: {
                titulo: 'Saturación de oxígeno',
                descripcion:
                    'Porcentaje de oxígeno registrado en cada cita.',
                series: [
                    {
                        clave: 'saturacion_oxigeno',
                        nombre: 'Saturación',
                        unidad: '%',
                        color: '#0284C7',
                        decimales: 0
                    }
                ]
            },

            temperatura: {
                titulo: 'Temperatura',
                descripcion:
                    'Temperatura corporal registrada durante el seguimiento.',
                series: [
                    {
                        clave: 'temperatura',
                        nombre: 'Temperatura',
                        unidad: '°C',
                        color: '#D97706',
                        decimales: 1
                    }
                ]
            },

            glucosa: {
                titulo: 'Glucosa',
                descripcion:
                    'Registros de glucosa vinculados con este caso.',
                series: [
                    {
                        clave: 'glucosa',
                        nombre: 'Glucosa',
                        unidad: 'mg/dL',
                        color: '#0891B2',
                        decimales: 1
                    }
                ]
            },

            estatura: {
                titulo: 'Estatura',
                descripcion:
                    'Histórico de estatura registrado por Enfermería.',
                series: [
                    {
                        clave: 'estatura',
                        nombre: 'Estatura',
                        unidad: 'cm',
                        color: '#4F46E5',
                        decimales: 1
                    }
                ]
            }
        };

        let instanciaGraficaEvolucion = null;
        let parametroGraficaActivo = 'peso_imc';

        function valorRegistrado(valor) {
            return valor !== null
                && valor !== ''
                && Number.isFinite(Number(valor));
        }

        function formatearValorGrafica(
            valor,
            serie,
            incluirSigno = false
        ) {
            const numero = Number(valor);

            const signo =
                incluirSigno && numero > 0
                    ? '+'
                    : '';

            const unidad =
                serie.unidad
                    ? ` ${serie.unidad}`
                    : '';

            return `${signo}${numero.toFixed(
                serie.decimales
            )}${unidad}`;
        }

        function registrosDeSerie(serie) {
            const valores =
                datosGraficasEvolucion[serie.clave]
                ?? [];

            const categorias =
                datosGraficasEvolucion.categorias
                ?? [];

            return valores
                .map(function(valor, indice) {
                    return {
                        valor: valor,
                        indice: indice,
                        categoria:
                            categorias[indice] ?? ''
                    };
                })
                .filter(function(registro) {
                    return valorRegistrado(
                        registro.valor
                    );
                });
        }

        function renderizarKpisGrafica(
            configuracion
        ) {
            const contenedor =
                document.querySelector(
                    '[data-kpis-grafica]'
                );

            if (!contenedor) {
                return;
            }

            contenedor.innerHTML =
                configuracion.series
                    .map(function(serie) {
                        const registros =
                            registrosDeSerie(serie);

                        const actual =
                            registros.at(-1);

                        const anterior =
                            registros.at(-2);

                        if (!actual) {
                            return `
                                <article
                                    class="rounded-xl border
                                           border-dashed
                                           border-slate-200
                                           bg-slate-50
                                           px-4 py-3">

                                    <p
                                        class="text-xs
                                               font-semibold
                                               text-slate-500">
                                        ${serie.nombre}
                                    </p>

                                    <p
                                        class="mt-1 text-lg
                                               font-bold
                                               text-slate-400">
                                        Sin registro
                                    </p>
                                </article>
                            `;
                        }

                        const diferencia =
                            anterior
                                ? Number(actual.valor)
                                    - Number(
                                        anterior.valor
                                    )
                                : null;

                        const comparacion =
                            diferencia === null
                                ? 'Primer registro disponible'
                                : `${
                                    formatearValorGrafica(
                                        diferencia,
                                        serie,
                                        true
                                    )
                                } vs. ${
                                    anterior.categoria
                                }`;

                        const colorCambio =
                            diferencia === null
                            || diferencia === 0
                                ? 'text-slate-500'
                                : 'text-[#0D3B7F]';

                        return `
                            <article
                                class="rounded-xl border
                                       border-slate-200
                                       bg-slate-50/70
                                       px-4 py-3">

                                <p
                                    class="text-xs
                                           font-semibold
                                           text-slate-500">
                                    ${serie.nombre}
                                </p>

                                <div
                                    class="mt-1 flex
                                           flex-wrap
                                           items-baseline
                                           gap-x-3 gap-y-1">

                                    <p
                                        class="text-2xl
                                               font-bold
                                               text-slate-900">
                                        ${
                                            formatearValorGrafica(
                                                actual.valor,
                                                serie
                                            )
                                        }
                                    </p>

                                    <p
                                        class="text-xs
                                               font-semibold
                                               ${colorCambio}">
                                        ${comparacion}
                                    </p>
                                </div>

                                <p
                                    class="mt-1 text-[11px]
                                           text-slate-400">
                                    Último:
                                    ${actual.categoria}
                                </p>
                            </article>
                        `;
                    })
                    .join('');
        }

        function mostrarEstadoVacioGrafica(
            elemento
        ) {
            elemento.innerHTML = `
                <div
                    class="flex min-h-[270px]
                           items-center justify-center
                           rounded-xl border
                           border-dashed
                           border-slate-200
                           bg-slate-50 px-6
                           text-center">

                    <div>
                        <p
                            class="font-semibold
                                   text-slate-600">
                            Sin registros para este
                            parámetro
                        </p>

                        <p
                            class="mt-1 text-xs
                                   text-slate-400">
                            La gráfica aparecerá cuando
                            Enfermería capture un valor
                            en una cita del caso.
                        </p>
                    </div>
                </div>
            `;
        }

        function actualizarSelectorGrafica(
            claveActiva
        ) {
            document
                .querySelectorAll(
                    '[data-selector-grafica]'
                )
                .forEach(function(boton) {
                    const activo =
                        boton.dataset
                            .selectorGrafica
                        === claveActiva;

                    boton.setAttribute(
                        'aria-selected',
                        activo
                            ? 'true'
                            : 'false'
                    );

                    boton.classList.toggle(
                        'border-[#0D3B7F]',
                        activo
                    );

                    boton.classList.toggle(
                        'bg-[#0D3B7F]',
                        activo
                    );

                    boton.classList.toggle(
                        'text-white',
                        activo
                    );

                    boton.classList.toggle(
                        'border-slate-200',
                        !activo
                    );

                    boton.classList.toggle(
                        'bg-white',
                        !activo
                    );

                    boton.classList.toggle(
                        'text-slate-600',
                        !activo
                    );
                });
        }

       function opcionesGraficaEvolucion(
    configuracion
) {
    /*
     * Categorías originales del caso.
     * Cada posición representa una evolución/cita.
     */
    const categoriasOriginales =
        datosGraficasEvolucion.categorias
        ?? [];

    /*
     * Conserva únicamente las citas que tengan al menos
     * un valor registrado para el parámetro seleccionado.
     */
    const indicesConDatos =
        categoriasOriginales
            .map(function(
                categoria,
                indice
            ) {
                return indice;
            })
            .filter(function(indice) {
                return configuracion.series.some(
                    function(serie) {
                        const valores =
                            datosGraficasEvolucion[
                                serie.clave
                            ] ?? [];

                        return valorRegistrado(
                            valores[indice]
                        );
                    }
                );
            });

    /*
     * Categorías filtradas. Las citas sin datos para
     * esta gráfica no ocupan espacio en el eje X.
     */
    const categorias =
        indicesConDatos.map(
            function(indice) {
                return categoriasOriginales[
                    indice
                ];
            }
        );

    /*
     * Las series deben filtrarse utilizando exactamente
     * los mismos índices que las categorías.
     */
    const series =
        configuracion.series.map(
            function(serie) {
                const valores =
                    datosGraficasEvolucion[
                        serie.clave
                    ] ?? [];

                return {
                    name: serie.nombre,

                    data:
                        indicesConDatos.map(
                            function(indice) {
                                return valores[
                                    indice
                                ] ?? null;
                            }
                        )
                };
            }
        );

    const crearEje = function(serie) {
        return {
            seriesName: serie.nombre,

            opposite:
                configuracion.ejesSeparados
                && serie.clave === 'imc',

            decimalsInFloat:
                serie.decimales,

            labels: {
                formatter:
                    function(valor) {
                        if (
                            !valorRegistrado(
                                valor
                            )
                        ) {
                            return '';
                        }

                        return formatearValorGrafica(
                            valor,
                            serie
                        );
                    },

                style: {
                    colors: '#64748B',
                    fontSize: '11px'
                }
            }
        };
    };

    return {
        chart: {
            type: 'line',
            height: 270,
            fontFamily: 'inherit',

            toolbar: {
                show: false
            },

            zoom: {
                enabled: false
            },

            animations: {
                enabled: true,
                speed: 300
            }
        },

        series: series,

        colors:
            configuracion.series.map(
                function(serie) {
                    return serie.color;
                }
            ),

        stroke: {
            curve: 'straight',
            width: 2.5,
            lineCap: 'round'
        },

        markers: {
            size: 3.5,
            strokeWidth: 2,
            strokeColors: '#FFFFFF',

            hover: {
                size: 5
            }
        },

        dataLabels: {
            enabled: false
        },

        grid: {
            borderColor: '#E2E8F0',
            strokeDashArray: 3,

            padding: {
                top: 4,
                right: 8,
                bottom: 0,
                left: 4
            },

            xaxis: {
                lines: {
                    show: false
                }
            }
        },

        xaxis: {
            categories: categorias,

            labels: {
                hideOverlappingLabels: true,
                trim: true,

                style: {
                    colors: '#64748B',
                    fontSize: '11px'
                }
            },

            axisBorder: {
                show: false
            },

            axisTicks: {
                show: false
            },

            tooltip: {
                enabled: false
            }
        },

        yaxis:
            configuracion.ejesSeparados
                ? configuracion.series.map(
                    crearEje
                )
                : crearEje(
                    configuracion.series[0]
                ),

        tooltip: {
            shared: true,
            intersect: false,

            x: {
                formatter:
                    function(
                        valor,
                        contexto
                    ) {
                        return categorias[
                            contexto.dataPointIndex
                        ] ?? '';
                    }
            },

            y: {
                formatter:
                    function(
                        valor,
                        contexto
                    ) {
                        if (
                            !valorRegistrado(
                                valor
                            )
                        ) {
                            return 'Sin registro';
                        }

                        const serie =
                            configuracion.series[
                                contexto.seriesIndex
                            ];

                        return formatearValorGrafica(
                            valor,
                            serie
                        );
                    }
            }
        },

        legend: {
            show:
                configuracion.series.length
                > 1,

            position: 'top',
            horizontalAlign: 'left',
            fontSize: '12px',

            markers: {
                size: 5
            }
        },

        responsive: [
            {
                breakpoint: 640,

                options: {
                    chart: {
                        height: 250
                    },

                    stroke: {
                        width: 2
                    },

                    xaxis: {
                        labels: {
                            rotate: -35,
                            rotateAlways: true
                        }
                    }
                }
            }
        ]
    };
}

        async function renderizarGraficaEvolucion(
            clave = parametroGraficaActivo
        ) {
            const configuracion =
                configuracionGraficasEvolucion[
                    clave
                ];

            const elemento =
                document.querySelector(
                    '[data-grafica-evolucion-principal]'
                );

            const titulo =
                document.querySelector(
                    '[data-titulo-grafica]'
                );

            const descripcion =
                document.querySelector(
                    '[data-descripcion-grafica]'
                );

            if (
                !configuracion
                || !elemento
                || !window.ApexCharts
            ) {
                return;
            }

            parametroGraficaActivo = clave;

            actualizarSelectorGrafica(clave);

            titulo.textContent =
                configuracion.titulo;

            descripcion.textContent =
                configuracion.descripcion;

            renderizarKpisGrafica(
                configuracion
            );

            if (instanciaGraficaEvolucion) {
                await instanciaGraficaEvolucion
                    .destroy();

                instanciaGraficaEvolucion = null;
            }

            elemento.innerHTML = '';

            const tieneDatos =
                configuracion.series.some(
                    function(serie) {
                        return registrosDeSerie(
                            serie
                        ).length > 0;
                    }
                );

            if (!tieneDatos) {
                mostrarEstadoVacioGrafica(
                    elemento
                );

                return;
            }

            instanciaGraficaEvolucion =
                new window.ApexCharts(
                    elemento,
                    opcionesGraficaEvolucion(
                        configuracion
                    )
                );

            await instanciaGraficaEvolucion
                .render();
        }

        document
            .querySelectorAll(
                '[data-selector-grafica]'
            )
            .forEach(function(boton) {
                boton.addEventListener(
                    'click',
                    function() {
                        renderizarGraficaEvolucion(
                            boton.dataset
                                .selectorGrafica
                        );
                    }
                );
            });

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
                        renderizarGraficaEvolucion(
                            parametroGraficaActivo
                        );
                    }
                );
            }
        );
    </script>
@endif