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
        // 1. Etapa Precontractual: ENUM -> STRING
        DB::statement("ALTER TABLE etapa_precontractual MODIFY COLUMN estado VARCHAR(50) DEFAULT 'en_proceso'");
        
        // 2. Areas: TINYINT -> STRING
        DB::statement("ALTER TABLE areas MODIFY COLUMN estado VARCHAR(50) DEFAULT 'activo'");
        
        // 3. Empleados: ENUM -> STRING
        DB::statement("ALTER TABLE empleados MODIFY COLUMN estado VARCHAR(50) DEFAULT 'activo'");

        // 4. Asegurar otras tablas son STRING
        $tables = ['seguridad_salud_trabajo', 'etapa_contractual', 'sedes', 'roles', 'personas'];
        
        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'estado')) {
                 DB::statement("ALTER TABLE $table MODIFY COLUMN estado VARCHAR(50) DEFAULT 'activo'");
            } else {
                 Schema::table($table, function (Blueprint $table) {
                     $table->string('estado', 50)->default('activo')->after('id');
                 });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertir por ahora para no perder datos si ya hay 'inactivo'
    }
};
