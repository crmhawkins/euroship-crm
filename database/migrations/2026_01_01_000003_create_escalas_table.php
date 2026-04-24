<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barco_id')->constrained('barcos')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('puerto');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index(['barco_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalas');
    }
};
