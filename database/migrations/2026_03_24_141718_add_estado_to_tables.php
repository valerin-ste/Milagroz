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
        $tables = ['seguridad_salud_trabajo', 'etapa_contractual', 'etapa_precontractual', 'areas', 'sedes', 'roles', 'personas', 'empleados'];
        
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'estado')) {
                    $table->string('estado')->default('activo')->after('id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['seguridad_salud_trabajo', 'etapa_contractual', 'etapa_precontractual', 'areas', 'sedes', 'roles', 'personas', 'empleados'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'estado')) {
                    $table->dropColumn('estado');
                }
            });
        }
    }
};
