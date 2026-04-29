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
        if (!Schema::hasColumn('users', 'estado')) {
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('estado')->default(1)->after('id');
            });
        }

        if (!Schema::hasColumn('system_roles', 'estado')) {
            Schema::table('system_roles', function (Blueprint $table) {
                $table->tinyInteger('estado')->default(1)->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('estado');
        });

        Schema::table('system_roles', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
