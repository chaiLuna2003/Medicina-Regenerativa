@if (request()->user()->isMedico() || request()->user()->isEnfermero())
    <div id="modal-control-peso" aria-hidden="true"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 px-3 py-4 sm:px-6"
        onclick="if (event.target === this) cerrarModalControlPeso()">
        <div role="dialog" aria-modal="true" aria-labelledby="titulo-modal-control-peso"
            class="flex max-h-full w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h2 id="titulo-modal-control-peso" class="text-lg font-semibold text-slate-900">Nuevo tratamiento de precisión</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $pacientes->nombre }} {{ $pacientes->apellido }} · {{ $pacientes->edad ?? 'Edad no disponible' }}</p>
                </div>
                <button type="button" onclick="cerrarModalControlPeso()" aria-label="Cerrar modal"
                    class="rounded-lg p-1 text-slate-500 hover:bg-slate-100">✕</button>
            </div>

            <form id="form-control-peso" method="POST" action="{{ route('pacientes.controles-peso.store', $pacientes) }}"
                data-store-url="{{ route('pacientes.controles-peso.store', $pacientes) }}"
                data-update-url="{{ route('pacientes.controles-peso.update', [$pacientes, '__ID__']) }}"
                class="flex min-h-0 flex-1 flex-col">
                @csrf
                <input type="hidden" name="_method" value="PUT" disabled>
                <input type="hidden" name="_control_peso_form" value="1">
                <input type="hidden" name="_control_peso_id" value="">

                <div class="min-h-0 space-y-6 overflow-y-auto px-5 py-5 sm:px-6">
                    @if (old('_control_peso_form') && $errors->any())
                        <div role="alert" class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            <p class="font-semibold">Revisa los campos indicados:</p>
                            <ul class="mt-1 list-inside list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block text-sm font-medium text-slate-700">Cita relacionada <span class="text-red-600">*</span>
                            <select name="cita_id" required class="mt-1 block w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Selecciona una cita</option>
                                @foreach ($pacientes->citas as $cita)
                                    @if (request()->user()->isEnfermero() || $cita->medico_id === request()->user()->medico?->id)
                                        <option value="{{ $cita->id }}">
                                            {{ $cita->fecha?->format('d/m/Y') }} · {{ substr((string) $cita->hora, 0, 5) }} · {{ $cita->medico?->user?->name ?? 'Médico no disponible' }} · {{ ucfirst($cita->estado) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </label>
                        <label class="block text-sm font-medium text-slate-700">Diagnóstico
                            <input name="diagnostico" type="text" maxlength="2000"
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </label>
                    </div>

                    <fieldset>
                        <legend class="mb-3 text-sm font-semibold text-slate-900">Mediciones manuales</legend>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach (['peso' => 'Peso (kg)', 'talla' => 'Talla (cm)', 'imc' => 'IMC',
                                'porcentaje_grasa' => 'Grasa (%)', 'porcentaje_musculo' => 'Músculo (%)',
                                'porcentaje_agua' => 'Agua (%)', 'porcentaje_hueso' => 'Hueso (%)'] as $campo => $etiqueta)
                                <label class="block text-sm font-medium text-slate-700">{{ $etiqueta }}
                                    @if (in_array($campo, ['peso', 'talla', 'imc'], true)) <span class="text-red-600">*</span> @endif
                                    <input name="{{ $campo }}" type="number" step="0.01" min="0"
                                        @if (in_array($campo, ['peso', 'talla', 'imc'], true)) required @endif
                                        class="mt-1 block w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                </label>
                            @endforeach
                            <label class="col-span-2 block text-sm font-medium text-slate-700">Índice antioxidante
                                <input name="indice_antioxidante" type="text" maxlength="100"
                                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </label>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="mb-3 text-sm font-semibold text-slate-900">Objetivos terapéuticos</legend>
                        <div class="space-y-2">
                            @for ($i = 0; $i < 3; $i++)
                                <label class="flex items-center gap-2 text-sm text-slate-500">{{ $i + 1 }}.
                                    <input name="objetivos[{{ $i }}]" type="text" maxlength="500"
                                        class="block w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                </label>
                            @endfor
                        </div>
                    </fieldset>

                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach (['tratamiento_base' => 'Tratamiento base', 'tratamiento_complementario' => 'Tratamiento complementario'] as $campo => $etiqueta)
                            <label class="block text-sm font-medium text-slate-700">{{ $etiqueta }}
                                <textarea name="{{ $campo }}" rows="3" maxlength="5000"
                                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </label>
                        @endforeach
                    </div>

                    <fieldset>
                        <legend class="mb-3 text-sm font-semibold text-slate-900">Péptidos de precisión</legend>
                        <div class="space-y-3">
                            @for ($i = 0; $i < 3; $i++)
                                <div class="grid gap-2 sm:grid-cols-3">
                                    @foreach (['nombre' => 'Péptido', 'dosis' => 'Dosis', 'tiempo' => 'Tiempo de uso'] as $campo => $etiqueta)
                                        <label class="block text-xs font-medium text-slate-700">{{ $etiqueta }} {{ $i + 1 }}
                                            <input name="peptidos[{{ $i }}][{{ $campo }}]" type="text" maxlength="200"
                                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        </label>
                                    @endforeach
                                </div>
                            @endfor
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="mb-3 text-sm font-semibold text-slate-900">Suplementos y cofactores indicados</legend>
                        <div class="space-y-2">
                            @for ($i = 0; $i < 3; $i++)
                                <label class="flex items-center gap-2 text-sm text-slate-500">{{ $i + 1 }}.
                                    <input name="suplementos[{{ $i }}]" type="text" maxlength="500"
                                        class="block w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                </label>
                            @endfor
                        </div>
                    </fieldset>

                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach (['indicacion_dietetica' => 'Indicación dietética', 'actividad_fisica' => 'Actividad física indicada'] as $campo => $etiqueta)
                            <label class="block text-sm font-medium text-slate-700">{{ $etiqueta }}
                                <textarea name="{{ $campo }}" rows="3" maxlength="5000"
                                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:px-6">
                    <button type="button" onclick="cerrarModalControlPeso()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">Cancelar</button>
                    <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Guardar tratamiento</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalControlPeso(id = null, valoresAnteriores = null) {
            const modal = document.getElementById('modal-control-peso');
            const form = document.getElementById('form-control-peso');
            form.reset();
            form.action = id ? form.dataset.updateUrl.replace('__ID__', id) : form.dataset.storeUrl;
            form.elements.namedItem('_method').disabled = !id;
            form.elements.namedItem('_control_peso_id').value = id || '';
            document.getElementById('titulo-modal-control-peso').textContent = id ? 'Editar tratamiento de precisión' : 'Nuevo tratamiento de precisión';

            const datos = valoresAnteriores || (id
                ? JSON.parse(document.getElementById('datos-control-peso-' + id).textContent)
                : null);

            if (datos) {
                const campos = [
                    'cita_id', 'diagnostico', 'peso', 'talla', 'imc', 'porcentaje_grasa',
                    'porcentaje_musculo', 'porcentaje_agua', 'porcentaje_hueso',
                    'indice_antioxidante', 'tratamiento_base', 'tratamiento_complementario',
                    'indicacion_dietetica', 'actividad_fisica'
                ];
                campos.forEach((campo) => {
                    const input = form.elements.namedItem(campo);
                    if (input) input.value = datos[campo] ?? '';
                });
                for (let i = 0; i < 3; i++) {
                    for (const campo of ['objetivos', 'suplementos']) {
                        const input = form.elements.namedItem(`${campo}[${i}]`);
                        if (input) input.value = datos[campo]?.[i] ?? '';
                    }
                    for (const campo of ['nombre', 'dosis', 'tiempo']) {
                        const input = form.elements.namedItem(`peptidos[${i}][${campo}]`);
                        if (input) input.value = datos.peptidos?.[i]?.[campo] ?? '';
                    }
                }
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            form.elements.namedItem('cita_id').focus();
        }

        function cerrarModalControlPeso() {
            const modal = document.getElementById('modal-control-peso');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
        }

        document.addEventListener('keydown', (evento) => {
            if (evento.key === 'Escape') cerrarModalControlPeso();
        });

        @if (old('_control_peso_form'))
            document.addEventListener('DOMContentLoaded', () => {
                abrirModalControlPeso(@js(old('_control_peso_id')), @js(old()));
            });
        @endif
    </script>
@endif
