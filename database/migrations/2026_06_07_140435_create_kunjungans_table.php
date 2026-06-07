<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungans', function (Blueprint $table) {
            $table->id();

            // Relasi ke data master
            $table->foreignId('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('dokters')->nullOnDelete();

            // Nomor kunjungan unik per episode
            $table->string('no_kunjungan')->unique(); // KNJ-ddmmyy-001

            // Data episode kunjungan
            $table->string('poli_tujuan');
            $table->string('jenis_kunjungan')->default('Rawat Jalan');
            $table->text('keluhan')->nullable();
            $table->text('riwayat_penyakit')->nullable();

            // Vital sign per kunjungan
            $table->decimal('berat_badan', 5, 1)->nullable();
            $table->decimal('tinggi_badan', 5, 1)->nullable();
            $table->string('tekanan_darah', 20)->nullable();

            // Alur status kunjungan
            $table->enum('status', ['Menunggu', 'Diperiksa', 'Selesai'])->default('Menunggu');
            $table->enum('validasi', ['Menunggu', 'Valid', 'Tidak Valid'])->default('Menunggu');
            $table->enum('status_kirim', ['Belum', 'Terkirim'])->default('Belum');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungans');
    }
};