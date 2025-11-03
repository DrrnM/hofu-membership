<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('nama_reward');          // Nama hadiah
            $table->text('deskripsi')->nullable();  // Deskripsi hadiah (opsional)
            $table->integer('poin_dibutuhkan');     // Jumlah poin yang dibutuhkan untuk ditukar
            $table->integer('stok')->default(0);    // Jumlah stok reward
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
