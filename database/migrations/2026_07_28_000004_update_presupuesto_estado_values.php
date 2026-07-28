<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('presupuestos')->where('estado', 'ofertado')->update(['estado' => 'pendiente']);
        DB::table('presupuestos')->where('estado', 'aceptado')->update(['estado' => 'completado']);
        DB::table('presupuestos')->where('estado', 'rechazado')->update(['estado' => 'completado']);
    }

    public function down(): void
    {
        //
    }
};
