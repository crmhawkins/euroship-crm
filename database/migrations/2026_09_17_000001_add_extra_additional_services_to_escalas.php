<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = ['riggers', 'transport_trucks', 'assistants', 'escort'];

    public function up(): void
    {
        Schema::table('escalas', function (Blueprint $table) {
            foreach ($this->columns as $column) {
                if (! Schema::hasColumn('escalas', $column)) {
                    $table->boolean($column)->default(false);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('escalas', function (Blueprint $table) {
            $table->dropColumn($this->columns);
        });
    }
};
