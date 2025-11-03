<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'owner',
            'password' => Hash::make('owner123'),
        ]);

        User::create([
            'username' => 'kasir',
            'password' => Hash::make('kasir123'),
        ]);
    }
}