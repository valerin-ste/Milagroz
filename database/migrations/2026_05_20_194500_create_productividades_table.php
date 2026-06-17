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
        Schema::create('productividades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empleado_id');
            $table->string('tipo', 100)->nullable();
            $table->string('titulo', 150);
            $table->text('descripcion');
            $table->string('archivo', 255)->nullable();
            $table->date('fecha');
            $table->integer('mes')->nullable();
            $table->integer('anio')->nullable();
            $table->boolean('estado')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productividades');
    }
};
