<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('planta_personal_sena')) {
            Schema::create('planta_personal_sena', function (Blueprint $table) {
                $table->increments('id');
                $table->foreignId('empleado_id')
                    ->constrained('empleados')
                    ->onDelete('cascade');

                $table->text('observaciones')->nullable();
                $table->date('fecha_reporte')->nullable();
                $table->boolean('estado')->default(true);

                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('planta_personal_sena');
    }
};