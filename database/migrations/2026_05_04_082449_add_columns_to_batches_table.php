<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            // Guard setiap kolom agar tidak crash jika sudah ada dari migration sebelumnya
            if (!Schema::hasColumn('batches', 'no_batch'))   $table->string('no_batch')->unique()->after('id');
            if (!Schema::hasColumn('batches', 'nama_obat'))  $table->string('nama_obat')->after('no_batch');
            if (!Schema::hasColumn('batches', 'tipe'))       $table->string('tipe')->nullable()->after('nama_obat');
            if (!Schema::hasColumn('batches', 'jumlah'))     $table->integer('jumlah')->default(0)->after('tipe');
            if (!Schema::hasColumn('batches', 'harga'))      $table->decimal('harga', 15, 2)->default(0)->after('jumlah');
            if (!Schema::hasColumn('batches', 'harga_bpjs')) $table->decimal('harga_bpjs', 15, 2)->default(0)->after('harga');
            if (!Schema::hasColumn('batches', 'tgl_expired'))$table->date('tgl_expired')->nullable()->after('harga_bpjs');
            if (!Schema::hasColumn('batches', 'tgl_masuk'))  $table->date('tgl_masuk')->nullable()->after('tgl_expired');
            if (!Schema::hasColumn('batches', 'supplier'))   $table->string('supplier')->nullable()->after('tgl_masuk');
            if (!Schema::hasColumn('batches', 'status'))     $table->string('status')->default('aktif')->after('supplier');
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