<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // presupuestos.estado and presupuesto_lineas.estado were still
        // ENUM('ofertado','aceptado','rechazado') — the 2026_07_28 value-update
        // migration ran against the ENUM and truncated everything to '' while
        // inserts of the new values ('pendiente'/'completado'/'asignado') fail
        // with "Data truncated for column 'estado'". Same fix as pedidos got in
        // 2026_07_30_000002: force VARCHAR via raw statement.
        DB::unprepared("ALTER TABLE `presupuestos` MODIFY COLUMN `estado` VARCHAR(50) NOT NULL DEFAULT 'pendiente'");
        DB::unprepared("ALTER TABLE `presupuesto_lineas` MODIFY COLUMN `estado` VARCHAR(50) NOT NULL DEFAULT 'pendiente'");

        // Repair rows truncated to '' and remap any surviving legacy values.
        DB::table('presupuestos')->whereIn('estado', ['', 'ofertado'])->update(['estado' => 'pendiente']);
        DB::table('presupuestos')->whereIn('estado', ['aceptado', 'rechazado'])->update(['estado' => 'completado']);

        DB::table('presupuesto_lineas')->whereIn('estado', ['', 'ofertado'])->update(['estado' => 'pendiente']);
        DB::table('presupuesto_lineas')->whereIn('estado', ['aceptado', 'rechazado'])->update(['estado' => 'completado']);
    }

    public function down(): void
    {
        // No revert — preserves data integrity
    }
};
