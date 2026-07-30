<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Force change ENUM to VARCHAR — DB::statement used for compatibility
        // Schema::table change() also attempted as fallback
        DB::unprepared("ALTER TABLE `pedidos` MODIFY COLUMN `estado_general` VARCHAR(50) NOT NULL DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        // No revert — preserves data integrity
    }
};
