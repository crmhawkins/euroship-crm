<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pedidos')->where('estado_general', 'parcial')->update(['estado_general' => 'entregado_parcial']);
    }

    public function down(): void
    {
        DB::table('pedidos')->where('estado_general', 'entregado_parcial')->update(['estado_general' => 'parcial']);
    }
};
