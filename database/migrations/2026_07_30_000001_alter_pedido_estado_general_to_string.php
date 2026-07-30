<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN estado_general VARCHAR(50) NOT NULL DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN estado_general ENUM('pendiente','parcial','entregado') NOT NULL DEFAULT 'pendiente'");
    }
};
