<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('usuarios.index') }}"
               class="text-slate-400 hover:text-slate-700">
                ←
            </a>

            <div>
                <h2 class="text-xl font-semibold text-slate-900">
                    Nuevo usuario
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Crea una cuenta y asigna sus permisos.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-10">
        <div class="mx-auto max-w-2xl px-4 sm:px-6">
            <form
                action="{{ route('usuarios.store') }}"
                method="POST"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
            >
                @csrf

                @include('usuarios._form')

                <div class="mt-8 flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('usuarios.index') }}"
                        class="rounded-lg px-5 py-2.5 text-center text-sm font-medium text-slate-600 hover:bg-slate-100"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                    >
                        Crear usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>