<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('poins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->integer('jumlah_poin')->default(0);      // Total poin yang dimiliki member
            $table->string('keterangan')->nullable();        // Alasan perubahan poin (misal: promo, penukaran)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poins');
    }
};
