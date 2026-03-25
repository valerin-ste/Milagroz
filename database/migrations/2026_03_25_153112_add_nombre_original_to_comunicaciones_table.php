<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('comunicaciones', function (Blueprint $table) {
        $table->string('nombre_original')->nullable()->after('archivo');
    });
}

public function down()
{
    Schema::table('comunicaciones', function (Blueprint $table) {
        $table->dropColumn('nombre_original');
    });
}
};
