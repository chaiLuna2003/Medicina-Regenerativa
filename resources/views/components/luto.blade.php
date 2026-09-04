@props([
    'size' => 'md',
    'label' => 'Paciente finado',
])

@php
    $claseTamano = match ($size) {
        'sm' => 'h-4 w-4',
        'lg' => 'h-7 w-7',
        default => 'h-5 w-5',
    };
@endphp

<span
    data-mono-luto
    role="img"
    aria-label="{{ $label }}"
    title="{{ $label }}"
    {{ $attributes->class([
        'inline-flex shrink-0 items-center justify-center text-black',
        $claseTamano,
    ]) }}
>
    <svg
        viewBox="0 0 24 24"
        fill="currentColor"
        class="h-full w-full"
        aria-hidden="true"
    >
        <path
            d="M12 2C9.3 2 7.4 3.8 7.4 6.4
               c0 2.4 1.6 4.8 3.1 6.9L6.7 22
               h3.5l1.8-4.2 1.8 4.2h3.5
               l-3.8-8.7c1.5-2.1 3.1-4.5
               3.1-6.9C16.6 3.8 14.7 2 12 2Zm0 2.8
               c1.1 0 1.8.7 1.8 1.7
               0 1.1-.7 2.5-1.8 4.1
               -1.1-1.6-1.8-3-1.8-4.1
               0-1 .7-1.7 1.8-1.7Z"
        />
    </svg>

    <span class="sr-only">
        {{ $label }}
    </span>
</span>