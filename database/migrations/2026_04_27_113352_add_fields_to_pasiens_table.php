<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasiens', function (Blueprint $table) {
            $table->string('jenis_kelamin')->nullable()->after('tgl_lahir');
            $table->string('no_bpjs')->nullable()->after('jenis');
        });
    }

    public function down(): void
    {
        Schema::table('pasiens', function (Blueprint $table) {
            $table->dropColumn(['jenis_kelamin', 'no_bpjs']);
        });
    }
};