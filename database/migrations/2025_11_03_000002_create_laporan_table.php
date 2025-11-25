<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id('ID_Laporan');
            $table->string('Judul_laporan')->default('Laporan Transaksi')->nullable();         
            $table->date('tanggal_laporan');           
            $table->decimal('total_transaksi', 10, 2)->default(0); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
