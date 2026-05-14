<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Puerto extends Model
{
    protected $table = 'puertos';

    protected $fillable = ['nombre', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public static function activos(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('activo', true)->orderBy('nombre')->get();
    }
}
