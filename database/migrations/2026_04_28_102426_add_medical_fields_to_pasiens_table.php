<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasiens', function (Blueprint $table) {
            if (!Schema::hasColumn('pasiens', 'berat_badan')) {
                $table->decimal('berat_badan', 5, 1)->nullable()->after('riwayat_penyakit');
            }
            if (!Schema::hasColumn('pasiens', 'tinggi_badan')) {
                $table->decimal('tinggi_badan', 5, 1)->nullable()->after('berat_badan');
            }
            if (!Schema::hasColumn('pasiens', 'tekanan_darah')) {
                $table->string('tekanan_darah', 20)->nullable()->after('tinggi_badan');
            }
            if (!Schema::hasColumn('pasiens', 'alergi')) {
                $table->string('alergi', 255)->nullable()->default('-')->after('tekanan_darah');
            }
            if (!Schema::hasColumn('pasiens', 'no_bpjs')) {
                $table->string('no_bpjs', 20)->nullable()->after('alergi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pasiens', function (Blueprint $table) {
            $table->dropColumn(['berat_badan', 'tinggi_badan', 'tekanan_darah', 'alergi', 'no_bpjs']);
        });
    }
};