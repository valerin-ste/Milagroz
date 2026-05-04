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
            // Check if column exists (it might have been created in a failed previous attempt)
            if (!Schema::hasColumn('users', 'persona_id')) {
                $table->integer('persona_id')->nullable()->after('id');
            } else {
                $table->integer('persona_id')->nullable()->change();
            }
            
            $table->foreign('persona_id')->references('id')->on('personas')->onDelete('set null');
        });
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
