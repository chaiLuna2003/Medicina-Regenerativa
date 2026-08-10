<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxWords implements ValidationRule
{
    public function __construct(
        private readonly int $maxWords
    ) {
    }

    /**
     * Valida que el contenido no exceda el máximo de palabras permitido.
     *
     * @param Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        if (! is_string($value)) {
            return;
        }

        /*
         * Elimina las etiquetas HTML que posteriormente generará
         * el editor enriquecido.
         */
        $plainText = strip_tags($value);

        /*
         * Convierte entidades como &nbsp; en espacios normales.
         */
        $plainText = html_entity_decode(
            $plainText,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        /*
         * Cuenta palabras Unicode para admitir correctamente
         * acentos y caracteres del español.
         */
        preg_match_all('/[\p{L}\p{N}]+(?:[\'’-][\p{L}\p{N}]+)*/u', $plainText, $matches);

        $wordCount = count($matches[0]);

        if ($wordCount > $this->maxWords) {
            $fail(
                "El contenido de la receta no puede superar las {$this->maxWords} palabras."
            );
        }
    }
}