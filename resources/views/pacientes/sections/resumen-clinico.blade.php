 {{-- Resumen --}}
                    <section
                        class="rounded-2xl border
                               border-slate-200 bg-white
                               p-5 shadow-sm">
                        <div
                            class="grid grid-cols-2 gap-3
                                   sm:grid-cols-4">
                            <div
                                class="rounded-xl bg-slate-50 p-4">
                                <p
                                    class="text-xs font-medium
                                           text-slate-500">
                                    Citas
                                </p>

                                <p
                                    class="mt-1 text-2xl font-bold
                                           text-slate-900">
                                    {{ $pacientes->citas->count() }}
                                </p>
                            </div>

                            <div
                                class="rounded-xl bg-slate-50 p-4">
                                <p
                                    class="text-xs font-medium
                                           text-slate-500">
                                    Estudios
                                </p>

                                <p
                                    class="mt-1 text-2xl font-bold
                                           text-slate-900">
                                    {{ $pacientes->estudios->count() }}
                                </p>
                            </div>

                            <div
                                class="rounded-xl bg-slate-50 p-4">
                                <p
                                    class="text-xs font-medium
                                           text-slate-500">
                                    Recetas
                                </p>

                                <p
                                    class="mt-1 text-2xl font-bold
                                           text-slate-900">
                                    {{ $pacientes->recetas->count() }}
                                </p>
                            </div>

                            <div
                                class="rounded-xl bg-slate-50 p-4">
                                <p
                                    class="text-xs font-medium
                                           text-slate-500">
                                    Signos vitales
                                </p>

                                <p
                                    class="mt-1 text-2xl font-bold
                                           text-slate-900">
                                    {{ $pacientes->signosVitales->count() }}
                                </p>
                            </div>
                        </div>
                    </section>