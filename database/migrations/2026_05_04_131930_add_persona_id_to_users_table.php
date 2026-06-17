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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'persona_id')) {
                $table->unsignedBigInteger('persona_id')->nullable()->after('id');
            } else {
                $table->unsignedBigInteger('persona_id')->nullable()->change();
            }
        });

        // Add FK only if it doesn't already exist
        $fkExists = collect(\DB::select("
            SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'users'
              AND COLUMN_NAME = 'persona_id'
              AND REFERENCED_TABLE_NAME = 'personas'
        "))->isNotEmpty();

        if (!$fkExists) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('persona_id')->references('id')->on('personas')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['persona_id']);
            $table->dropColumn('persona_id');
        });
    }
};
