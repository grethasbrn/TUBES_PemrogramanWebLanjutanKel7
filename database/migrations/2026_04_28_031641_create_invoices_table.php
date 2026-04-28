<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('invoices', function (Blueprint $table) {
        $table->id();
        $table->string('kode_invoice')->unique();
        $table->unsignedBigInteger('pasien_id');
        $table->date('tanggal');
        $table->enum('status', ['Masuk','Diproses','Lunas'])->default('Masuk');
        $table->enum('bayar', ['BPJS','Mandiri']);
        $table->integer('total')->default(0);
        $table->timestamps();

        $table->foreign('pasien_id')->references('id')->on('pasiens')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
