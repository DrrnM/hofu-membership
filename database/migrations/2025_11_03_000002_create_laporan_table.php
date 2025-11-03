<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('judul_laporan');          // Judul laporan (misal: "Penukaran Reward")
            $table->date('tanggal_laporan');          // Tanggal laporan
            $table->text('keterangan')->nullable();   // Keterangan tambahan
            $table->decimal('total_transaksi', 10, 2)->default(0); // Nilai total transaksi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
