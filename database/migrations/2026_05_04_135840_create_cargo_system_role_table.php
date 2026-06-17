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
        if (!Schema::hasTable('cargo_system_role')) {
            Schema::create('cargo_system_role', function (Blueprint $table) {
                $table->id();
                // Matching the types of the existing tables
                $table->unsignedBigInteger('cargo_id');
                $table->unsignedBigInteger('system_role_id');
                $table->timestamps();

                // Foreign keys
                $table->foreign('cargo_id')->references('id')->on('roles')->onDelete('cascade');
                $table->foreign('system_role_id')->references('id')->on('system_roles')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cargo_system_role');
    }
};
