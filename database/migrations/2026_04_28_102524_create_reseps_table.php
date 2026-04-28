<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasiens')->onDelete('cascade');
            $table->string('no_resep')->unique(); // contoh: RX-2026-001
            $table->string('diagnosa');
            $table->text('catatan_dokter')->nullable();
            $table->date('tanggal_kontrol')->nullable();
            $table->enum('status', ['draft', 'baru', 'validasi', 'siap', 'selesai', 'ditolak'])->default('baru');
            $table->json('obat_list'); // simpan daftar obat dalam format JSON
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseps');
    }
};