<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {

        User::firstOrCreate(
            ['username' => 'owner'],
            [
                'password' => Hash::make('owner123'),
            ]
        );

        User::firstOrCreate(
            ['username' => 'kasir'],
            [
                'password' => Hash::make('kasir123'),
            ]
        );
    }
}
