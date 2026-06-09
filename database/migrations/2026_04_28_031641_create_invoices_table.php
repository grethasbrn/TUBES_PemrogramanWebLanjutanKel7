<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Invoice yang dibuat dari resep selesai
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice')->unique();       
            $table->foreignId('resep_id')->constrained('reseps')->cascadeOnDelete();
            $table->string('no_rm');
            $table->string('nama');
            $table->enum('jenis', ['Mandiri', 'BPJS']);
            $table->enum('status', ['Masuk', 'Diproses', 'Lunas'])->default('Masuk');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total_tagihan', 12, 2)->default(0);
            $table->string('no_referensi')->nullable();   
            $table->timestamp('created_at')->nullable();  
            $table->foreignId('diproses_oleh')->nullable()->constrained('users'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};