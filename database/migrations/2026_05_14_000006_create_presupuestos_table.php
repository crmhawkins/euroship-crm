<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escala_id')->constrained('escalas')->cascadeOnDelete();
            $table->string('numero_presupuesto')->unique();
            $table->date('fecha_presupuesto');
            $table->enum('estado', ['ofertado', 'aceptado', 'rechazado'])->default('ofertado');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index(['escala_id', 'fecha_presupuesto']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
