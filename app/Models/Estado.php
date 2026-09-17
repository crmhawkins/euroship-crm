<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Estado extends Model
{
    public const TIPO_PEDIDO = 'pedido';
    public const TIPO_PRESUPUESTO = 'presupuesto';

    protected $table = 'estados';

    protected $fillable = ['tipo', 'clave', 'nombre', 'color', 'orden', 'finalizado', 'sistema', 'activo'];

    /** Cache por request: una query por tipo aunque la tabla pinte cientos de badges. */
    private static array $cache = [];

    protected function casts(): array
    {
        return [
            'finalizado' => 'boolean',
            'sistema'    => 'boolean',
            'activo'     => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Estado $estado) {
            if (blank($estado->clave)) {
                $estado->clave = Str::slug($estado->nombre, '_');
            }
        });

        static::saved(fn () => static::$cache = []);
        static::deleted(fn () => static::$cache = []);
    }

    public static function deTipo(string $tipo): Collection
    {
        return static::$cache[$tipo] ??= static::where('tipo', $tipo)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get()
            ->keyBy('clave');
    }

    /** Opciones para Select: solo activos, más el valor actual aunque esté desactivado. */
    public static function opciones(string $tipo, ?string $incluir = null): array
    {
        return static::deTipo($tipo)
            ->filter(fn (Estado $e) => $e->activo || $e->clave === $incluir)
            ->map(fn (Estado $e) => $e->nombre)
            ->all();
    }

    public static function etiqueta(string $tipo, ?string $clave): string
    {
        if (blank($clave)) {
            return '—';
        }

        return static::deTipo($tipo)->get($clave)?->nombre ?? Str::headline($clave);
    }

    public static function colorDe(string $tipo, ?string $clave): string
    {
        return static::deTipo($tipo)->get($clave)?->color ?? 'gray';
    }

    /** Claves de estados que cierran el registro (p. ej. pedido entregado). */
    public static function clavesFinalizadas(string $tipo): array
    {
        return static::deTipo($tipo)->where('finalizado', true)->keys()->all();
    }

    public function enUso(): bool
    {
        return match ($this->tipo) {
            self::TIPO_PEDIDO      => Pedido::where('estado_general', $this->clave)->exists(),
            self::TIPO_PRESUPUESTO => Presupuesto::where('estado', $this->clave)->exists()
                || PresupuestoLinea::where('estado', $this->clave)->exists(),
            default                => false,
        };
    }
}
