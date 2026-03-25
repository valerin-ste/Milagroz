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
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'estado')) {
                 try {
                     // Mapear valores string a enteros
                     DB::table($table)->where('estado', 'activo')->update(['estado' => '1']);
                     DB::table($table)->where('estado', 'inactivo')->update(['estado' => '0']);
                     DB::table($table)->where('estado', 'aprobado')->update(['estado' => '1']);
                     DB::table($table)->where('estado', 'rechazado')->update(['estado' => '0']);
                     DB::table($table)->where('estado', 'en_proceso')->update(['estado' => '1']);

                     // Cambiar tipo de columna a entero
                     DB::statement("ALTER TABLE `$table` MODIFY COLUMN estado TINYINT(1) DEFAULT 1");
                 } catch (\Exception $e) {
                     // Log o ignorar si falla una tabla específica
                 }
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
