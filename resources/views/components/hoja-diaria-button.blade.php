<a
    href="{{ route('hoja-diaria.index') }}"
    {{ $attributes->merge([
        'class' =>
            'inline-flex items-center justify-center gap-2
             rounded-xl border border-slate-300 bg-white
             px-4 py-2.5 text-sm font-semibold text-slate-700
             shadow-sm transition
             hover:border-blue-200 hover:bg-blue-50
             hover:text-[#0D3B7F]
             focus:outline-none focus:ring-2
             focus:ring-[#0D3B7F]/30'
    ]) }}>

    <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-5 w-5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="1.8">

        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M9 12h6m-6 4h6m2 5H7
               a2 2 0 01-2-2V5a2 2 0 012-2
               h5.586a1 1 0 01.707.293
               l3.414 3.414A1 1 0 0117 7.414V19
               a2 2 0 01-2 2z" />
    </svg>

    Hoja diaria
</a>