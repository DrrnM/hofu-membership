<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Ubah dari enum ke string biasa tanpa default
            $table->string('tipe_langganan')->default(null)->change();
        });
        
        // Update data existing berdasarkan poin
        \DB::statement("UPDATE members SET tipe_langganan = 
            CASE 
                WHEN poin >= 600 THEN 'diamond'
                WHEN poin >= 450 THEN 'platinum' 
                WHEN poin >= 300 THEN 'gold'
                WHEN poin >= 150 THEN 'silver'
                ELSE 'bronze'
            END");
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Kembalikan ke enum jika rollback
            $table->enum('tipe_langganan', ['bronze', 'silver', 'gold', 'platinum', 'diamond'])
                  ->default('bronze')
                  ->change();
        });
    }
};