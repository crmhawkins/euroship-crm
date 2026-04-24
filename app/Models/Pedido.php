<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'escala_id',
        'numero_pedido',
        'fecha_pedido',
        'puerto_entrega',
        'notas',
        'estado_general',
    ];

    protected function casts(): array
    {
        return [
            'fecha_pedido' => 'date',
        ];
    }

    public function escala(): BelongsTo
    {
        return $this->belongsTo(Escala::class);
    }

    public function pertrechos(): HasMany
    {
        return $this->hasMany(Pertrecho::class);
    }

    /**
     * Recalcula estado_general a partir del estado de las líneas (pertrechos).
     */
    public function recalcularEstado(): string
    {
        $total = $this->pertrechos()->count();
        if ($total === 0) {
            $this->estado_general = 'pendiente';
            $this->save();
            return $this->estado_general;
        }

        $entregados = $this->pertrechos()->where('estado', 'entregado')->count();

        $this->estado_general = match (true) {
            $entregados === 0 => 'pendiente',
            $entregados === $total => 'entregado',
            default => 'parcial',
        };
        $this->save();

        return $this->estado_general;
    }
}
