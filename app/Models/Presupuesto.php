<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Presupuesto extends Model
{
    use HasFactory;

    protected $table = 'presupuestos';

    protected $fillable = [
        'escala_id',
        'numero_presupuesto',
        'fecha_presupuesto',
        'estado',
        'notas',
        'enlace',
    ];

    protected function casts(): array
    {
        return [
            'fecha_presupuesto' => 'date',
        ];
    }

    public function escala(): BelongsTo
    {
        return $this->belongsTo(Escala::class);
    }

    public function lineas(): HasMany
    {
        return $this->hasMany(PresupuestoLinea::class);
    }

    public function getTotalAttribute(): float
    {
        return $this->lineas->sum(fn ($l) => $l->cantidad * $l->precio_unitario);
    }

    protected static function booted(): void
    {
        static::creating(function (Presupuesto $presupuesto) {
            if (empty($presupuesto->numero_presupuesto)) {
                \Illuminate\Support\Facades\DB::transaction(function () use ($presupuesto) {
                    $year = now()->year;
                    $last = static::whereYear('created_at', $year)
                        ->lockForUpdate()
                        ->orderByDesc('id')
                        ->value('numero_presupuesto');

                    $seq = $last
                        ? ((int) substr($last, strrpos($last, '-') + 1)) + 1
                        : 1;

                    $presupuesto->numero_presupuesto = sprintf('PRES-%d-%03d', $year, $seq);
                });
            }
        });
    }
}
