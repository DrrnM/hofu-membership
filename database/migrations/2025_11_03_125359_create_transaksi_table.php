<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->string('member_id', 3)->nullable();

            $table->integer('total_belanja')->default(0); 
  
            $table->dateTime('tanggal_transaksi')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';

            $table->foreign('member_id')
                ->references('member_id')
                ->on('members')
                ->onDelete('set null');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
