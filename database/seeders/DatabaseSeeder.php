<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Member;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'owner'],
            ['password' => Hash::make('owner123')]
        );

        User::firstOrCreate(
            ['username' => 'kasir'],
            ['password' => Hash::make('kasir123')]
        );

        Member::factory()->count(20)->create();
    }
}