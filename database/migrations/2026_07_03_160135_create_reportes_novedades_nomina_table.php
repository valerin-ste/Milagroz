<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reportes_novedades_nomina')) {
            Schema::create('reportes_novedades_nomina', function (Blueprint $table) {
                $table->increments('id');
                $table->foreignId('empleado_id')
                    ->constrained('empleados')
                    ->onDelete('cascade');

                $table->string('tipo_novedad', 100)->nullable();
                $table->decimal('cantidad', 10, 2)->nullable();
                $table->text('observaciones')->nullable();
                $table->string('archivo')->nullable();
                $table->date('fecha')->nullable();
                $table->boolean('estado')->default(true);

                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_novedades_nomina');
    }
};