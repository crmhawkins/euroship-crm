<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Escala extends Model
{
    use HasFactory;

    protected $table = 'escalas';

    protected $fillable = [
        'barco_id',
        'fecha',
        'puerto',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function barco(): BelongsTo
    {
        return $this->belongsTo(Barco::class);
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function getLabelAttribute(): string
    {
        $fecha = $this->fecha?->format('Y-m-d') ?? '—';
        return "{$this->puerto} ({$fecha})";
    }
}
