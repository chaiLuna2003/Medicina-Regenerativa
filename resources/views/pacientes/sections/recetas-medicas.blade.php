 {{-- ================================================= --}}
 {{-- RECETAS MÉDICAS --}}
 {{-- ================================================= --}}
 
 <details
     class="group overflow-hidden rounded-2xl
               border border-slate-200
               bg-white shadow-sm">
     <summary
         class="flex cursor-pointer
                   list-none items-center
                   justify-between px-6 py-5">
         <div class="flex items-center gap-3">

             <div
                 class="flex h-9 w-9 items-center
                           justify-center rounded-lg
                           bg-emerald-50 text-emerald-600">
                 <svg
                     xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">
                     <path
                         stroke-linecap="round"
                         stroke-linejoin="round"
                         d="M9 12h6m-6 4h6
                               M7 3h7l4 4v14H7z" />
                 </svg>
             </div>

             <div>
                 <h3 class="font-semibold text-slate-900">
                     Recetas médicas
                 </h3>

                 <p class="text-xs text-slate-400">
                     Prescripciones emitidas al paciente
                 </p>
             </div>
         </div>

         <div class="flex items-center gap-3">
             <span
                 class="rounded-full bg-emerald-50
                           px-2.5 py-1 text-xs
                           font-semibold text-emerald-700">
                 {{ $pacientes->recetas->count() }}
             </span>

             <svg
                 xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5 text-slate-400
                           transition group-open:rotate-180"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                 <path
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     stroke-width="2"
                     d="M19 9l-7 7-7-7" />
             </svg>
         </div>
     </summary>

     <div class="border-t border-slate-100">

         @forelse ($pacientes->recetas as $receta)

         <div
             class="border-b border-slate-100
                           px-6 py-5 last:border-0">
             <div
                 class="flex flex-col gap-4
                               sm:flex-row
                               sm:items-center
                               sm:justify-between">

                 {{-- Información --}}
                 <div class="min-w-0">

                     <div
                         class="flex flex-wrap
                                       items-center gap-2">
                         <p
                             class="font-semibold
                                           text-slate-900">
                             Receta #{{ $receta->id }}
                         </p>

                         <span
                             class="rounded-full
                                           bg-emerald-50
                                           px-2 py-0.5
                                           text-[11px]
                                           font-semibold
                                           text-emerald-700">
                             Receta médica
                         </span>
                     </div>

                     <p
                         class="mt-1 text-sm
                                       text-slate-500">
                         Expedida:
                         {{ $receta->fecha_expedicion
                                    ? \Carbon\Carbon::parse(
                                        $receta->fecha_expedicion
                                    )->format('d/m/Y')
                                    : 'Fecha no disponible' }}
                     </p>

                     <p
                         class="mt-2 text-sm
                                       text-slate-600">
                         Médico:
                         <span class="font-medium">
                             {{ $receta
                                        ->cita
                                        ?->medico
                                        ?->user
                                        ?->name
                                        ?? 'No disponible' }}
                         </span>
                     </p>

                     @if ($receta->cita)
                     <p
                         class="mt-1 text-xs
                                           text-slate-400">
                         Cita #{{ $receta->cita->id }}

                         @if ($receta->cita->fecha)
                         ·
                         {{ $receta
                                            ->cita
                                            ->fecha
                                            ->format('d/m/Y') }}
                         @endif
                     </p>
                     @endif
                 </div>

                 {{-- Acciones --}}
                 <div
                     class="flex shrink-0
                                   flex-wrap items-center gap-2">
                     <a
                         href="{{ route(
                                    'recetas.show',
                                    $receta
                                ) }}"
                         class="inline-flex items-center
                                       gap-1.5 rounded-lg
                                       border border-slate-200
                                       bg-white px-3 py-2
                                       text-xs font-semibold
                                       text-slate-700
                                       transition
                                       hover:bg-slate-50">
                         <svg
                             xmlns="http://www.w3.org/2000/svg"
                             class="h-4 w-4"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">
                             <path
                                 stroke-linecap="round"
                                 stroke-linejoin="round"
                                 d="M2.25 12s3.75-6.75
                                           9.75-6.75S21.75 12
                                           21.75 12 18 18.75
                                           12 18.75 2.25 12
                                           2.25 12Z" />

                             <circle
                                 cx="12"
                                 cy="12"
                                 r="2.75" />
                         </svg>

                         Ver receta
                     </a>

                     <a
                         href="{{ route(
                                    'recetas.pdf',
                                    $receta
                                ) }}"
                         target="_blank"
                         rel="noopener noreferrer"
                         class="inline-flex items-center
                                       gap-1.5 rounded-lg
                                       bg-emerald-600
                                       px-3 py-2
                                       text-xs font-semibold
                                       text-white transition
                                       hover:bg-emerald-700">
                         <svg
                             xmlns="http://www.w3.org/2000/svg"
                             class="h-4 w-4"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">
                             <path
                                 stroke-linecap="round"
                                 stroke-linejoin="round"
                                 d="M6 2h9l3 3v17H6z" />

                             <path
                                 stroke-linecap="round"
                                 stroke-linejoin="round"
                                 d="M9 13h6M9 17h4" />
                         </svg>

                         PDF
                     </a>
                 </div>

             </div>
         </div>

         @empty

         <div class="px-6 py-10 text-center">
             <p
                 class="text-sm font-medium
                               text-slate-600">
                 No hay recetas registradas.
             </p>

             <p
                 class="mt-1 text-xs
                               text-slate-400">
                 Las recetas emitidas aparecerán aquí.
             </p>
         </div>

         @endforelse

         @if ($pacientes->recetas->isNotEmpty())
         <div
             class="border-t border-slate-100
                           bg-slate-50/60 px-6 py-4
                           text-right">
             <a
                 href="{{ route(
                            'pacientes.recetas.index',
                            $pacientes
                        ) }}"
                 class="text-sm font-semibold
                               text-emerald-600
                               hover:text-emerald-800">
                 Ver historial completo →
             </a>
         </div>
         @endif

     </div>
 </details>