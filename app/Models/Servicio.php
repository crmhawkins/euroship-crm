<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $fillable = [
        'escala_id',
        'courier_id',
        'ubicacion_id',
        'estatus_aduanero_id',
        'number',
        'bx',
        'kg',
        'llegada',
        'comentarios',
        'enlace',
        'entrada',
        'facturado',
        'incidencia',
    ];

    protected function casts(): array
    {
        return [
            'llegada'    => 'date',
            'entrada'    => 'boolean',
            'facturado'  => 'boolean',
            'incidencia' => 'boolean',
            'kg'         => 'decimal:2',
        ];
    }

    public function escala(): BelongsTo
    {
        return $this->belongsTo(Escala::class);
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class);
    }

    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(Ubicacion::class);
    }

    public function estatusAduanero(): BelongsTo
    {
        return $this->belongsTo(EstatusAduanero::class, 'estatus_aduanero_id');
    }

    public function getColorFila(): string
    {
        if ($this->incidencia) {
            return 'danger';
        }
        if ($this->llegada !== null) {
            return 'success';
        }
        return 'gray';
    }
}
