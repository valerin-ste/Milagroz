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
    Schema::create('solicitudes', function (Blueprint $table) {
        $table->id();

        $table->foreignId('empleado_id')
              ->constrained('empleados')
              ->onDelete('cascade');

        $table->string('tipo', 100);
        $table->text('descripcion')->nullable();
        $table->string('archivo')->nullable();

        $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])
              ->default('pendiente');

        $table->date('fecha');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicituds');
    }
};
