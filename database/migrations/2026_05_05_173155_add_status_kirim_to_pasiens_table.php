<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pasiens', 'status_kirim')) {
            Schema::table('pasiens', function (Blueprint $table) {
                $table->string('status_kirim')->default('Belum')->after('id');
            });
        }  
    }

    public function down(): void
    {
        if (Schema::hasColumn('pasiens', 'status_kirim')) {  
            Schema::table('pasiens', function (Blueprint $table) {
                $table->dropColumn('status_kirim');
            });
        }
    }
};