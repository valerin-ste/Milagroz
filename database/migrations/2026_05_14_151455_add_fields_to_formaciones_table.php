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
        Schema::table('formaciones', function (Blueprint $table) {
            if (!Schema::hasColumn('formaciones', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('estado');
            }
            if (!Schema::hasColumn('formaciones', 'estado_curso')) {
                $table->string('estado_curso', 50)->default('finalizado')->after('observaciones');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formaciones', function (Blueprint $table) {
            $table->dropColumn(['observaciones', 'estado_curso']);
        });
    }
};
