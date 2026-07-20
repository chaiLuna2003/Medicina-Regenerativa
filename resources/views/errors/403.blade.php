<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
        <div class="bg-red-100 text-red-600 rounded-full w-16 h-16 flex items-center justify-center mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Acceso restringido</h1>
        <p class="text-gray-500 mb-6">No tienes permiso para ver esta sección con tu rol actual.</p>
        <a href="{{ route('dashboard') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg text-sm font-medium">
            Volver al Dashboard
        </a>
    </div>
</x-guest-layout>