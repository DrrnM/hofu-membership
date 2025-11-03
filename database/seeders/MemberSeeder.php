<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Andi', 'Budi', 'Citra', 'Dewi', 'Eko', 'Farah', 'Gilang', 'Hana', 'Indra', 'Joko'];

        foreach ($names as $name) {

            do {
                $id = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            } while (Member::where('id_member', $id)->exists());

            Member::create([
                'id_member' => $id,
                'nama' => $name,
                'no_hp' => '08' . rand(1000000000, 9999999999),
                'poin' => rand(0, 500),
            ]);
        }
    }
}