<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kunjungan_id ke tabel reseps
        Schema::table('reseps', function (Blueprint $table) {
            $table->foreignId('kunjungan_id')
                  ->nullable()
                  ->after('pasien_id')
                  ->constrained('kunjungans')
                  ->nullOnDelete();
        });

        // Tambah kunjungan_id ke tabel invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('kunjungan_id')
                  ->nullable()
                  ->after('resep_id')
                  ->constrained('kunjungans')
                  ->nullOnDelete();
        });

        // Tambah alasan_tolak ke reseps jika belum ada
        if (!Schema::hasColumn('reseps', 'alasan_tolak')) {
            Schema::table('reseps', function (Blueprint $table) {
                $table->text('alasan_tolak')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        Schema::table('reseps', function (Blueprint $table) {
            $table->dropForeign(['kunjungan_id']);
            $table->dropColumn('kunjungan_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['kunjungan_id']);
            $table->dropColumn('kunjungan_id');
        });
    }
};