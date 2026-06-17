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
        Schema::create('certificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->onDelete('cascade');
            $table->string('nombre_certificacion');
            $table->string('tipo_certificacion')->nullable();
            $table->string('institucion');
            $table->string('codigo_certificado')->nullable();
            $table->date('fecha_expedicion');
            $table->date('fecha_vencimiento')->nullable();
            $table->string('archivo')->nullable();
            $table->text('observaciones')->nullable();
            $table->integer('estado')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificaciones');
    }
};
