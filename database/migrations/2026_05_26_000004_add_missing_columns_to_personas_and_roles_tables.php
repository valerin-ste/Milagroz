<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar columnas a PERSONAS
        Schema::table('personas', function (Blueprint $table) {
            if (!Schema::hasColumn('personas', 'tipo_documento')) {
                $table->string('tipo_documento', 50)->nullable()->after('id');
            }
            if (!Schema::hasColumn('personas', 'numero_documento')) {
                $table->string('numero_documento', 50)->nullable()->after('tipo_documento');
            }
            if (!Schema::hasColumn('personas', 'nombres')) {
                $table->string('nombres', 100)->after('numero_documento');
            }
            if (!Schema::hasColumn('personas', 'apellidos')) {
                $table->string('apellidos', 100)->after('nombres');
            }
            if (!Schema::hasColumn('personas', 'telefono')) {
                $table->string('telefono', 20)->nullable()->after('apellidos');
            }
            if (!Schema::hasColumn('personas', 'correo')) {
                $table->string('correo', 150)->nullable()->after('telefono');
            }
            if (!Schema::hasColumn('personas', 'direccion')) {
                $table->string('direccion', 200)->nullable()->after('correo');
            }
            if (!Schema::hasColumn('personas', 'fecha_nacimiento')) {
                $table->date('fecha_nacimiento')->nullable()->after('direccion');
            }
        });

        // 2. Agregar columnas a ROLES
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'nombre')) {
                $table->string('nombre', 100)->after('id');
            }
            if (!Schema::hasColumn('roles', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('nombre');
            }
        });
    }

    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_documento', 'numero_documento', 'nombres', 'apellidos',
                'telefono', 'correo', 'direccion', 'fecha_nacimiento'
            ]);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'descripcion']);
        });
    }
};
