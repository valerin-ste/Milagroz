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
        if (!Schema::hasTable('fechas_especiales')) {
            Schema::create('fechas_especiales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('empleado_id')->constrained('empleados')->onDelete('cascade');
                $table->string('titulo')->nullable();
                $table->date('fecha');
                $table->string('tipo');
                $table->text('descripcion')->nullable();
                $table->string('archivo')->nullable(); // Conservamos archivo porque el controlador lo usa
                $table->integer('estado')->default(1);
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fechas_especiales');
    }
};
