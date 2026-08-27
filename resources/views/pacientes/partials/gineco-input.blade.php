@php
    $tipo = $tipo ?? 'text';

    $valor = $ginecoobstetricos?->{$campo};

    if ($valor instanceof \Carbon\CarbonInterface) {
        $valor = $valor->format('Y-m-d');
    }
@endphp

<div>
    <label
        for="gineco_{{ $campo }}"
        class="mb-1.5 block text-xs
               font-semibold text-slate-600">
        {{ $etiqueta }}
    </label>

    <input
        id="gineco_{{ $campo }}"
        type="{{ $tipo }}"
        name="{{ $campo }}"
        value="{{ old($campo, $valor) }}"
        @isset($min)
            min="{{ $min }}"
        @endisset
        @isset($max)
            max="{{ $max }}"
        @endisset
        @isset($placeholder)
            placeholder="{{ $placeholder }}"
        @endisset
        class="w-full rounded-xl
               border-slate-300 text-sm
               shadow-sm
               focus:border-rose-500
               focus:ring-rose-500">

    @error($campo, 'ginecoobstetricos')
        <p class="mt-1 text-xs text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>