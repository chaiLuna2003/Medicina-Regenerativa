@if (request()->user()->isMedico() || request()->user()->isEnfermero())
    <div id="modal-clasificaciones"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/40 px-4 py-8"
        aria-hidden="true"
        onclick="if (event.target === this) cerrarModalClasificaciones()">
        <div role="dialog" aria-modal="true" aria-labelledby="titulo-clasificaciones"
            class="max-h-full w-full max-w-xl overflow-y-auto rounded-2xl bg-white shadow-2xl">
            <form method="POST" action="{{ route('pacientes.clasificaciones.update', $pacientes) }}">
                @csrf
                @method('PUT')

                <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h2 id="titulo-clasificaciones" class="text-lg font-semibold text-slate-900">
                            Editar clasificación
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">Selecciona todas las que correspondan al paciente.</p>
                    </div>
                    <button type="button" onclick="cerrarModalClasificaciones()"
                        aria-label="Cerrar modal" class="rounded-lg p-1 text-slate-500 hover:bg-slate-100">✕</button>
                </div>

                <div class="grid max-h-[55vh] grid-cols-1 gap-2 overflow-y-auto px-6 py-5 sm:grid-cols-2">
                    @foreach (\App\Models\Pacientes::CLASIFICACIONES as $clave => $etiqueta)
                        <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 p-3 text-sm text-slate-700 hover:bg-slate-50">
                            <input type="checkbox" name="clasificaciones[]" value="{{ $clave }}"
                                @checked(in_array($clave, old('clasificaciones', $pacientes->clasificaciones ?? []), true))
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            {{ $etiqueta }}
                        </label>
                    @endforeach
                </div>
                @error('clasificaciones')
                    <p class="px-6 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('clasificaciones.*')
                    <p class="px-6 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
                    <button type="button" onclick="cerrarModalClasificaciones()"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm">Cancelar</button>
                    <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
                        Guardar clasificación
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function cerrarModalClasificaciones() {
            const modal = document.getElementById('modal-clasificaciones');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') cerrarModalClasificaciones();
        });
        @if ($errors->has('clasificaciones') || $errors->has('clasificaciones.*'))
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('modal-clasificaciones');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        @endif
    </script>
@endif
