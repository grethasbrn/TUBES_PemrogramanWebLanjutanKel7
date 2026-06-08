<?php

// ============================================================
// FILE INI BERISI 2 HAL:
// 1. Migration baru untuk kolom stok_dikurangi
// 2. Instruksi hapus migration duplikat alasan_tolak
// ============================================================

// ── LANGKAH 1: Hapus file duplikat ──────────────────────────
// Hapus file ini dari folder database/migrations/:
//   2026_06_06_045415_add_alasan_tolak_to_reseps_table.php
//
// Pertahankan yang ini (sudah ada down() yang benar):
//   2026_06_06_073517_add_alasan_tolak_to_reseps_table.php
//
// PASTIKAN migration 073517 sudah pakai guard hasColumn:
//
//   public function up(): void {
//       Schema::table('reseps', function (Blueprint $table) {
//           if (!Schema::hasColumn('reseps', 'alasan_tolak')) {
//               $table->text('alasan_tolak')->nullable()->after('status');
//           }
//       });
//   }

// ── LANGKAH 2: Buat migration baru berikut ──────────────────
// Simpan file ini sebagai:
//   database/migrations/2026_06_08_000001_add_stok_dikurangi_to_reseps_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom stok_dikurangi untuk mencegah double-deduct stok obat.
     * InvoiceController::kurangiStok() mengecek dan men-set flag ini.
     */
    public function up(): void
    {
        Schema::table('reseps', function (Blueprint $table) {
            if (!Schema::hasColumn('reseps', 'stok_dikurangi')) {
                $table->boolean('stok_dikurangi')
                      ->default(false)
                      ->after('status')
                      ->comment('Flag agar stok hanya dikurangi sekali per resep');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reseps', function (Blueprint $table) {
            $table->dropColumn('stok_dikurangi');
        });
    }
};