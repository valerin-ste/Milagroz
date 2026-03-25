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
        Schema::table('seguridad_salud_trabajo', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('etapa_contractual', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('etapa_precontractual', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('empleados', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seguridad_salud_trabajo', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('etapa_contractual', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('etapa_precontractual', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
