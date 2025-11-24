<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\Poin;
use Carbon\Carbon;

class PoinSeeder extends Seeder
{
    public function run(): void
    {
        $members = Member::all();

        foreach ($members as $member) {
            $existingPoin = Poin::where('member_id', $member->id_member)->first();

            if (!$existingPoin) {
                // Buat data poin baru
                Poin::create([
                    'member_id' => $member->id_member,
                    'jumlah_poin' => $member->poin,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        $this->command->info('Data poin berhasil dibuat untuk ' . $members->count() . ' member.');
    }
}