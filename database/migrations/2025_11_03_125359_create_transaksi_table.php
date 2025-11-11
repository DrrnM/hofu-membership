<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            
            // id_member di tabel members adalah VARCHAR, jadi kita sesuaikan:
            $table->string('member_id', 36)->nullable();

            $table->integer('total_pembelian')->default(0);
            $table->integer('poin_didapat')->default(0);
            $table->timestamps();

            // Pastikan engine InnoDB agar FK berfungsi
            $table->engine = 'InnoDB';

            // Relasi ke tabel members
            $table->foreign('member_id')
                  ->references('id_member')
                  ->on('members')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
