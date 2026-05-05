<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->string('no_batch')->unique()->after('id');
            $table->string('nama_obat')->after('no_batch');
            $table->string('tipe')->nullable()->after('nama_obat');
            $table->integer('jumlah')->default(0)->after('tipe');
            $table->decimal('harga', 15, 2)->default(0)->after('jumlah');
            $table->decimal('harga_bpjs', 15, 2)->default(0)->after('harga');
            $table->date('tgl_expired')->nullable()->after('harga_bpjs');
            $table->date('tgl_masuk')->nullable()->after('tgl_expired');
            $table->string('supplier')->nullable()->after('tgl_masuk');
            $table->string('status')->default('aktif')->after('supplier');
        });
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn([
                'no_batch', 'nama_obat', 'tipe', 'jumlah',
                'harga', 'harga_bpjs', 'tgl_expired', 'tgl_masuk',
                'supplier', 'status'
            ]); // ← fix: ] bukan }
        });
    }
};