<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // `comentarios` sigue siendo el NARRATIVE (visible para el cliente en la Delivery Note).
        // `notas` son comentarios internos que nunca salen en el PDF.
        if (! Schema::hasColumn('servicios', 'notas')) {
            Schema::table('servicios', function (Blueprint $table) {
                $table->text('notas')->nullable()->after('comentarios');
            });
        }
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn('notas');
        });
    }
};
