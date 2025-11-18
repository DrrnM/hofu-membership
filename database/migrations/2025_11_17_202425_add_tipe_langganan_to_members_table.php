<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->enum('tipe_langganan', ['bronze', 'silver', 'gold', 'platinum', 'diamond'])
                  ->default('bronze')
                  ->after('poin');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('tipe_langganan');
        });
    }
};