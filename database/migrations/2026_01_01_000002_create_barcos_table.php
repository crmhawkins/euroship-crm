<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barcos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('bandera', 100)->nullable();
            $table->string('imo_number', 20)->nullable()->index();
            $table->string('tipo', 100)->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index(['cliente_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barcos');
    }
};
