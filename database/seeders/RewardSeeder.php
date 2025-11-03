<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reward;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        Reward::insert([
            [
                'nama_reward' => 'Gratis Kopi',
                'poin_diperlukan' => 200,
                'deskripsi' => 'Dapatkan 1 kopi gratis untuk 200 poin',
                'tanggal_dibuat' => now(),
            ],
            [
                'nama_reward' => 'Diskon 10%',
                'poin_diperlukan' => 100,
                'deskripsi' => 'Diskon 10% untuk pembelian berikutnya',
                'tanggal_dibuat' => now(),
            ],
        ]);
    }
}
