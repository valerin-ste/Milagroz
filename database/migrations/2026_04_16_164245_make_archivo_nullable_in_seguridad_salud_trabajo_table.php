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
            // La columna 'archivo' ya no se usa directamente;
            // el módulo usa la relación polimórfica con la tabla 'documentos'.
            // La hacemos nullable para que el store no falle con constraint NOT NULL.
            $table->string('archivo', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seguridad_salud_trabajo', function (Blueprint $table) {
            $table->string('archivo', 255)->nullable(false)->change();
        });
    }
};
