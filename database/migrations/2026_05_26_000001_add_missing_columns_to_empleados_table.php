<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega las columnas faltantes a la tabla empleados.
     * La migración original solo creó id y timestamps.
     */
    public function up(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            if (!Schema::hasColumn('empleados', 'persona_id')) {
                $table->unsignedBigInteger('persona_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('empleados', 'area_id')) {
                $table->unsignedBigInteger('area_id')->nullable()->after('persona_id');
            }
            if (!Schema::hasColumn('empleados', 'sede_id')) {
                $table->unsignedBigInteger('sede_id')->nullable()->after('area_id');
            }
            if (!Schema::hasColumn('empleados', 'rol_id')) {
                $table->unsignedBigInteger('rol_id')->nullable()->after('sede_id');
            }
            if (!Schema::hasColumn('empleados', 'cargo')) {
                $table->string('cargo', 150)->nullable()->after('rol_id');
            }
            if (!Schema::hasColumn('empleados', 'fecha_ingreso')) {
                $table->date('fecha_ingreso')->nullable()->after('cargo');
            }
        });

        // Foreign keys (en bloque separado para evitar errores si la columna ya existe)
        Schema::table('empleados', function (Blueprint $table) {
            $fks = collect(\DB::select("
                SELECT COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'empleados'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            "))->pluck('COLUMN_NAME')->toArray();

            if (!in_array('persona_id', $fks) && Schema::hasColumn('empleados', 'persona_id')) {
                $table->foreign('persona_id')->references('id')->on('personas')->onDelete('set null');
            }
            if (!in_array('area_id', $fks) && Schema::hasColumn('empleados', 'area_id')) {
                $table->foreign('area_id')->references('id')->on('areas')->onDelete('set null');
            }
            if (!in_array('sede_id', $fks) && Schema::hasColumn('empleados', 'sede_id')) {
                $table->foreign('sede_id')->references('id')->on('sedes')->onDelete('set null');
            }
            if (!in_array('rol_id', $fks) && Schema::hasColumn('empleados', 'rol_id')) {
                $table->foreign('rol_id')->references('id')->on('roles')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropForeign(['persona_id']);
            $table->dropForeign(['area_id']);
            $table->dropForeign(['sede_id']);
            $table->dropForeign(['rol_id']);
            $table->dropColumn(['persona_id', 'area_id', 'sede_id', 'rol_id', 'cargo', 'fecha_ingreso']);
        });
    }
};
