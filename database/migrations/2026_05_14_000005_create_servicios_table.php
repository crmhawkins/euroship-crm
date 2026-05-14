<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escala_id')->constrained('escalas')->cascadeOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->nullOnDelete();
            $table->foreignId('ubicacion_id')->nullable()->constrained('ubicaciones')->nullOnDelete();
            $table->foreignId('estatus_aduanero_id')->nullable()->constrained('estatus_aduaneros')->nullOnDelete();
            $table->string('number')->nullable();
            $table->unsignedInteger('bx')->nullable();
            $table->decimal('kg', 10, 2)->nullable();
            $table->date('llegada')->nullable();
            $table->text('comentarios')->nullable();
            $table->boolean('entrada')->default(false);
            $table->boolean('facturado')->default(false);
            $table->boolean('incidencia')->default(false);
            $table->timestamps();

            $table->index('escala_id');
            $table->index('llegada');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
