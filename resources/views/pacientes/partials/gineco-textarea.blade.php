<div>
    <label
        for="gineco_{{ $campo }}"
        class="mb-1.5 block text-xs
               font-semibold text-slate-600">
        {{ $etiqueta }}
    </label>

    <textarea
        id="gineco_{{ $campo }}"
        name="{{ $campo }}"
        rows="3"
        maxlength="{{ $campo === 'observaciones'
            ? 10000
            : 5000 }}"
        class="w-full resize-y rounded-xl
               border-slate-300 text-sm
               shadow-sm
               focus:border-rose-500
               focus:ring-rose-500">{{ old(
            $campo,
            $ginecoobstetricos?->{$campo}
        ) }}</textarea>

    @error($campo, 'ginecoobstetricos')
        <p class="mt-1 text-xs text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>