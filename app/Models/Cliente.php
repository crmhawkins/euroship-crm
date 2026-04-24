<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'direccion',
        'notas',
    ];

    public function barcos(): HasMany
    {
        return $this->hasMany(Barco::class);
    }

    public function escalas(): HasManyThrough
    {
        return $this->hasManyThrough(Escala::class, Barco::class);
    }
}
