<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id_transaksi();
            $table->string('id_member', 36)->nullable();

            $table->integer('total_pembelian')->default(0);
            $table->integer('jumlah_poin')->default(0);
            $table->timestamps();

            $table->engine = 'InnoDB';

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
