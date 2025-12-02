<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poins', function (Blueprint $table) {
            $table->id();
            $table->string('member_id', 3);
            $table->integer('jumlah_poin')->default(0);
            $table->timestamps();

            $table->foreign('member_id')
                  ->references('member_id')
                  ->on('members')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poins');
    }
};