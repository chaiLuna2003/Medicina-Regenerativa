<?php

namespace Database\Seeders;

use App\Models\Medicos;
use App\Models\Universidad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class DoctoraIrisUneveSeeder extends Seeder
{
    public function run(): void
    {
        $universidad = Universidad::query()
            ->where('abreviatura', 'UNEVE')
            ->firstOrFail();

        $coincidencias = Medicos::query()->get()->filter(function (Medicos $medico): bool {
            $nombreCompleto = implode(' ', array_filter([
                $medico->nombre,
                $medico->apellido_paterno,
                $medico->apellido_materno,
            ]));

            return Str::lower(preg_replace('/\s+/u', ' ', trim(Str::ascii($nombreCompleto))))
                === 'iris rivera padilla';
        });

        if ($coincidencias->count() > 1) {
            throw new RuntimeException('Hay varias fichas médicas de Iris Rivera Padilla; revise los duplicados antes de asignar UNEVE.');
        }

        $doctora = $coincidencias->first();

        if (! $doctora) {
            $this->command?->warn('No se encontró la ficha médica de Iris Rivera Padilla; UNEVE y su logo quedaron disponibles en el catálogo.');

            return;
        }

        $doctora->update(['universidad_id' => $universidad->id]);
    }
}
