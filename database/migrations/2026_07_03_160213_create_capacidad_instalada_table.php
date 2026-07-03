<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capacidad_instalada', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('empleado_id')
                ->constrained('empleados')
                ->onDelete('cascade');

            $table->string('proceso', 150)->nullable();
            $table->integer('capacidad_disponible')->nullable();
            $table->integer('capacidad_utilizada')->nullable();
            $table->text('observaciones')->nullable();
            $table->date('fecha')->nullable();
            $table->boolean('estado')->default(true);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capacidad_instalada');
    }
};