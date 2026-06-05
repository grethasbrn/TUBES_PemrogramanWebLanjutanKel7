<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom 'poli' ke tabel users.
     * Digunakan untuk dokter agar hanya melihat pasien sesuai polinya.
     *
     * Nilai yang valid: Umum, Anak, Penyakit Dalam, Bedah, Gigi, Kebidanan, Mata, UGD
     * Nullable karena admin & apoteker tidak memerlukan poli.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('poli')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('poli');
        });
    }
};