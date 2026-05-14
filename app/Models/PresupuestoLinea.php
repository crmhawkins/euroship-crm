<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresupuestoLinea extends Model
{
    use HasFactory;

    protected $table = 'presupuesto_lineas';

    protected $fillable = [
        'presupuesto_id',
        'descripcion',
        'cantidad',
        'unidad',
        'precio_unitario',
        'estado',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'cantidad'        => 'decimal:2',
            'precio_unitario' => 'decimal:2',
        ];
    }

    public function presupuesto(): BelongsTo
    {
        return $this->belongsTo(Presupuesto::class);
    }
}
