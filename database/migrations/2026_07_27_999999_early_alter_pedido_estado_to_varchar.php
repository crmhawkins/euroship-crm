<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Disable strict mode for this session so the ALTER succeeds
        // regardless of MySQL configuration
        try {
            DB::statement("SET SESSION sql_mode = ''");
        } catch (\Throwable $e) {
            // best-effort; ignore if not supported
        }

        // Convert ENUM to VARCHAR so new estado values are accepted
        DB::statement("ALTER TABLE `pedidos` MODIFY COLUMN `estado_general` VARCHAR(50) NOT NULL DEFAULT 'pendiente'");

        // Normalise any legacy 'parcial' rows while we have the open session
        DB::table('pedidos')
            ->where('estado_general', 'parcial')
            ->update(['estado_general' => 'entregado_parcial']);
    }

    public function down(): void
    {
        // No revert — preserves data integrity
    }
};
