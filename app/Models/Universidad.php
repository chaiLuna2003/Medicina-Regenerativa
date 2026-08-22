<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Universidad extends Model
{
    protected $table = 'universidades';

    protected $fillable = [
        'nombre',
        'abreviatura',
        'logo_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function medicos(): HasMany
    {
        return $this->hasMany(
            Medicos::class,
            'universidad_id'
        );
    }
}