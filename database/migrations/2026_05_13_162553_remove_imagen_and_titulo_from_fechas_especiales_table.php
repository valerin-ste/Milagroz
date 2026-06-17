<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fechas_especiales', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('fechas_especiales', 'imagen')) {
                $columns[] = 'imagen';
            }
            if (Schema::hasColumn('fechas_especiales', 'titulo')) {
                $columns[] = 'titulo';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fechas_especiales', function (Blueprint $table) {
            $table->string('titulo')->nullable()->after('tipo');
            $table->string('imagen')->nullable()->after('mensaje');
        });
    }
};
