<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presupuesto_lineas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presupuesto_id')->constrained('presupuestos')->cascadeOnDelete();
            $table->string('descripcion');
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->string('unidad')->nullable();
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->enum('estado', ['ofertado', 'aceptado', 'rechazado'])->default('ofertado');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuesto_lineas');
    }
};
