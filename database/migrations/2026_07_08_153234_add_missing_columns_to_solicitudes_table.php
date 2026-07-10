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
            if (!Schema::hasColumn('solicitudes', 'archivo')) {
                $table->string('archivo')->nullable();
            }
            if (!Schema::hasColumn('solicitudes', 'nombre_archivo')) {
                $table->string('nombre_archivo')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            if (Schema::hasColumn('solicitudes', 'archivo')) {
                $table->dropColumn('archivo');
            }
            if (Schema::hasColumn('solicitudes', 'nombre_archivo')) {
                $table->dropColumn('nombre_archivo');
            }
        });
    }
};
