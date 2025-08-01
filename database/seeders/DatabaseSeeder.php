<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'zumado.jp0527@gmail.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('zumado.jp0527@gmail.com'),
            ]
        );
        User::updateOrCreate(
            ['email' => 'daise2ac@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('11111111'),
                'is_admin' => 1,
                'role' => 'admin'
            ]
        );
        User::updateOrCreate(
            ['email' => 'goodsman207@gmail.com'],
            [
                'name' => 'Atoman',
                'password' => Hash::make('goodsman207@gmail.com'),
                'is_admin' => 1,
                'role' => 'admin'
            ]
        );
        User::updateOrCreate(
            ['email' => 'okadakaido810@gmail.com'],
            [
                'name' => 'Shnata',
                'password' => Hash::make('okadakaido810@gmail.com'),
                'is_admin' => 1,
                'role' => 'admin'
            ]
        );
    }
}
