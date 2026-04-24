<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escala_id')->constrained('escalas')->cascadeOnDelete();
            $table->string('numero_pedido')->unique();
            $table->date('fecha_pedido');
            $table->string('puerto_entrega');
            $table->text('notas')->nullable();
            $table->enum('estado_general', ['pendiente', 'parcial', 'entregado'])->default('pendiente');
            $table->timestamps();

            $table->index(['escala_id', 'fecha_pedido']);
            $table->index('estado_general');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
