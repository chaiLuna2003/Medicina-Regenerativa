@props(['active'])

@php
    $classes = ($active ?? false)
        ? 'inline-flex items-center rounded-xl bg-[#0D3B7F]/10
           px-4 py-2 text-sm font-semibold text-[#0D3B7F]
           ring-1 ring-inset ring-[#0D3B7F]/10
           transition duration-150 ease-in-out
           focus:outline-none focus-visible:ring-2
           focus-visible:ring-[#0D3B7F]
           focus-visible:ring-offset-2'
        : 'inline-flex items-center rounded-xl px-4 py-2
           text-sm font-medium text-slate-600
           transition duration-150 ease-in-out
           hover:bg-slate-100 hover:text-slate-900
           focus:outline-none focus-visible:ring-2
           focus-visible:ring-[#0D3B7F]
           focus-visible:ring-offset-2';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>