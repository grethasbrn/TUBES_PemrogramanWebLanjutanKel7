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
    Schema::create('dokters', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->string('spesialisasi');
        $table->string('no_telepon')->nullable();
        $table->string('email')->nullable();
        $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokters');
    }
};
