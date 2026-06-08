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
    Schema::table('pasiens', function (Blueprint $table) {
        $table->string('poli_tujuan')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    Schema::table('pasiens', function (Blueprint $table) {
        $table->dropColumn('poli_tujuan');
    });
    }
};
