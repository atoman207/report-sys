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
            ['email' => 'daise2ac@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('11111111'),
                'is_admin' => 1,
                'role' => 'admin'
            ]
        );
        User::updateOrCreate(
            ['email' => 'kindman207@gmail.com'],
            [
                'name' => 'Atoman',
                'password' => Hash::make('kindman207@gmail.com'),
                'is_admin' => 1,
                'role' => 'admin'
            ]
        );
            User::updateOrCreate(
                ['email' => 'daise2ac@ibaraki.email.ne.jp'],
                [
                    'name' => 'daise2ac',
                    'password' => Hash::make('daise2ac@ibaraki.email.ne.jp'),
                    'is_admin' => 1,
                    'role' => 'admin'
                ]
            );
            User::updateOrCreate(
                ['email' => 'd2d_hachiouji@icloud.com'],
                [
                    'name' => 'd2d_hachiouji',
                    'password' => Hash::make('d2d_hachiouji@icloud.com'),
                    'is_admin' => 1,
                    'role' => 'admin'
                ]
            );
            User::updateOrCreate(
                ['email' => 'daise2denko@themis.ocn.ne.jp'],
                [
                    'name' => 'daise2denko',
                    'password' => Hash::make('daise2denko@themis.ocn.ne.jp'),
                    'is_admin' => 1,
                    'role' => 'admin'
                ]
            );
    }
}
