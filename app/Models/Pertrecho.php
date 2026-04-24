<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pertrecho extends Model
{
    use HasFactory;

    protected $table = 'pertrechos';

    protected $fillable = [
        'pedido_id',
        'descripcion',
        'cantidad',
        'unidad',
        'estado',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        $recalc = function (Pertrecho $pertrecho): void {
            $pertrecho->pedido?->recalcularEstado();
        };

        static::saved($recalc);
        static::deleted($recalc);
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
