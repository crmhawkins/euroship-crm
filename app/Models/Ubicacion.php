<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ubicacion extends Model
{
    protected $table = 'ubicaciones';

    protected $fillable = ['nombre', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class);
    }

    public static function activos(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('activo', true)->orderBy('nombre')->get();
    }
}
