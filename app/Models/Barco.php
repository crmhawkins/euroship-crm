<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barco extends Model
{
    use HasFactory;

    protected $table = 'barcos';

    protected $fillable = [
        'cliente_id',
        'nombre',
        'bandera',
        'imo_number',
        'tipo',
        'notas',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function escalas(): HasMany
    {
        return $this->hasMany(Escala::class);
    }
}
