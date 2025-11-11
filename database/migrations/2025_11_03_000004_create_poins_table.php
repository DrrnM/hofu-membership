<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('poins', function (Blueprint $table) {
            $table->id();
            $table->string('member_id', 36); // 🔹 sama tipe dan panjangnya
            $table->integer('jumlah_poin')->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->collation = 'utf8mb4_unicode_ci';

            
            $table->foreign('member_id')
                  ->references('id_member')
                  ->on('members')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poins');
    }
};
