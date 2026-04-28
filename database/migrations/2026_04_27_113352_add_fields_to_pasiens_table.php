<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasiens', function (Blueprint $table) {
            // Cek dan tambah kolom yang belum ada
            if (!Schema::hasColumn('pasiens', 'jenis_kelamin')) {
                $table->string('jenis_kelamin', 1)->nullable()->after('tgl_lahir'); // 'L' atau 'P'
            }
            if (!Schema::hasColumn('pasiens', 'alamat')) {
                $table->text('alamat')->nullable()->after('jenis_kelamin');
            }
            if (!Schema::hasColumn('pasiens', 'no_telepon')) {
                $table->string('no_telepon', 20)->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('pasiens', 'pekerjaan')) {
                $table->string('pekerjaan', 100)->nullable()->after('no_telepon');
            }
            if (!Schema::hasColumn('pasiens', 'jenis_kunjungan')) {
                $table->string('jenis_kunjungan')->default('Rawat Jalan')->after('pekerjaan');
            }
            if (!Schema::hasColumn('pasiens', 'keluhan')) {
                $table->text('keluhan')->nullable()->after('jenis_kunjungan');
            }
            if (!Schema::hasColumn('pasiens', 'riwayat_penyakit')) {
                $table->text('riwayat_penyakit')->nullable()->after('keluhan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pasiens', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kelamin',
                'alamat',
                'no_telepon',
                'pekerjaan',
                'jenis_kunjungan',
                'keluhan',
                'riwayat_penyakit',
            ]);
        });
    }
};