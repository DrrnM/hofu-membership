<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reward', function (Blueprint $table) {
            $table->id('id_reward');
            $table->string('nama_reward', 100);
            $table->integer('poin_diperlukan');
            $table->text('deskripsi')->nullable();
            $table->timestamp('tanggal_dibuat')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward');
    }
};
