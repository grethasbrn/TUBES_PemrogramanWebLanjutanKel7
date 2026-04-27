<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasiens', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm')->unique();
            $table->string('nama');
            $table->string('nik', 16)->unique();
            $table->date('tgl_lahir');
            $table->enum('jenis', ['BPJS', 'Mandiri']);
            $table->string('poli_tujuan');
            $table->enum('status', ['Menunggu', 'Diperiksa', 'Selesai'])->default('Menunggu');
            $table->string('validasi')->default('Menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasiens');
    }
};