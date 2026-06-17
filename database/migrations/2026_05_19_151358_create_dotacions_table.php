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
        Schema::create('dotaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empleado_id');
            $table->string('tipo_dotacion', 100);
            $table->string('talla', 20);
            $table->integer('cantidad')->default(1);
            $table->text('observaciones')->nullable();
            $table->string('archivo', 255)->nullable();
            $table->date('fecha');
            $table->boolean('estado')->default(1);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('empleado_id')->references('id')->on('empleados')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dotaciones');
    }
};
