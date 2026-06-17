<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar columnas a SEDES
        Schema::table('sedes', function (Blueprint $table) {
            if (!Schema::hasColumn('sedes', 'nombre')) {
                $table->string('nombre', 100)->after('id');
            }
            if (!Schema::hasColumn('sedes', 'direccion')) {
                $table->string('direccion', 150)->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('sedes', 'ciudad')) {
                $table->string('ciudad', 100)->nullable()->after('direccion');
            }
            if (!Schema::hasColumn('sedes', 'telefono')) {
                $table->string('telefono', 20)->nullable()->after('ciudad');
            }
        });

        // 2. Agregar columnas a AREAS
        Schema::table('areas', function (Blueprint $table) {
            if (!Schema::hasColumn('areas', 'sede_id')) {
                $table->unsignedBigInteger('sede_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('areas', 'nombre')) {
                $table->string('nombre', 100)->after('sede_id');
            }
            if (!Schema::hasColumn('areas', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('nombre');
            }
        });

        // 3. Claves foráneas (separado para evitar errores si la columna ya existe pero la FK no)
        Schema::table('areas', function (Blueprint $table) {
            $fks = collect(\DB::select("
                SELECT COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'areas'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            "))->pluck('COLUMN_NAME')->toArray();

            if (!in_array('sede_id', $fks) && Schema::hasColumn('areas', 'sede_id')) {
                $table->foreign('sede_id')->references('id')->on('sedes')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropForeign(['sede_id']);
            $table->dropColumn(['sede_id', 'nombre', 'descripcion']);
        });

        Schema::table('sedes', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'direccion', 'ciudad', 'telefono']);
        });
    }
};
