<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('poins', function (Blueprint $table) {
            // Tambah kolom transaksi_id
            if (!Schema::hasColumn('poins', 'transaksi_id')) {
                $table->unsignedBigInteger('transaksi_id')->nullable()->after('member_id');
            }
            
            // Tambah foreign key
            $table->foreign('transaksi_id')
                  ->references('id_transaksi')
                  ->on('transaksi')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('poins', function (Blueprint $table) {
            $table->dropForeign(['transaksi_id']);
            $table->dropColumn('transaksi_id');
        });
    }
};