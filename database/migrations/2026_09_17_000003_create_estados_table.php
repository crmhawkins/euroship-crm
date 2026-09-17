<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('estados')) {
            Schema::create('estados', function (Blueprint $table) {
                $table->id();
                $table->string('tipo', 20);            // pedido | presupuesto
                $table->string('clave', 50);           // valor guardado en pedidos.estado_general / presupuestos.estado
                $table->string('nombre', 100);
                $table->string('color', 20)->default('gray');
                $table->unsignedInteger('orden')->default(0);
                $table->boolean('finalizado')->default(false); // pedido cerrado: no sale en pendientes
                $table->boolean('sistema')->default(false);    // usado por lógica automática: no se puede borrar
                $table->boolean('activo')->default(true);
                $table->timestamps();
                $table->unique(['tipo', 'clave']);
            });
        }

        // Valores que ya existían hardcodeados en los Resources.
        $estados = [
            ['pedido', 'pendiente', 'Pendiente', 'warning', 10, false, true],
            ['pedido', 'preparado', 'Preparado', 'info', 20, false, false],
            ['pedido', 'facturado', 'Facturado', 'gray', 30, false, false],
            ['pedido', 'despachado', 'Despachado', 'primary', 40, false, false],
            ['pedido', 'entregado_parcial', 'Entregado parcial', 'warning', 50, false, true],
            ['pedido', 'entregado', 'Entregado', 'success', 60, true, true],
            ['presupuesto', 'pendiente', 'Pendiente', 'warning', 10, false, true],
            ['presupuesto', 'completado', 'Completado', 'success', 20, true, false],
            ['presupuesto', 'asignado', 'Asignado', 'info', 30, false, false],
        ];

        foreach ($estados as [$tipo, $clave, $nombre, $color, $orden, $finalizado, $sistema]) {
            DB::table('estados')->updateOrInsert(
                ['tipo' => $tipo, 'clave' => $clave],
                [
                    'nombre'     => $nombre,
                    'color'      => $color,
                    'orden'      => $orden,
                    'finalizado' => $finalizado,
                    'sistema'    => $sistema,
                    'activo'     => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('estados');
    }
};
