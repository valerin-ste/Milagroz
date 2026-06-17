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
        Schema::table('solicitudes', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('solicitudes', 'archivo')) {
                $columns[] = 'archivo';
            }
            if (Schema::hasColumn('solicitudes', 'nombre_archivo')) {
                $columns[] = 'nombre_archivo';
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
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->string('archivo')->nullable();
            $table->string('nombre_archivo')->nullable();
        });
    }
};
