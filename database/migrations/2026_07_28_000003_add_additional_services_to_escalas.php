<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('escalas', function (Blueprint $table) {
            $table->boolean('overtime')->default(false)->after('remarks');
            $table->boolean('handling_express')->default(false)->after('overtime');
            $table->boolean('crane_service')->default(false)->after('handling_express');
        });
    }

    public function down(): void
    {
        Schema::table('escalas', function (Blueprint $table) {
            $table->dropColumn(['overtime', 'handling_express', 'crane_service']);
        });
    }
};
