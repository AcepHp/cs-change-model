<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User Produksi
        User::create([
            'name' => 'Produksi User',
            'npk' => '0001',
            'email' => 'produksi@example.com',
            'password' => Hash::make('password'), // default password
            'role' => 'produksi',
        ]);

        // User Quality
        User::create([
            'name' => 'Quality User',
            'npk' => '0002',
            'email' => 'quality@example.com',
            'password' => Hash::make('password'),
            'role' => 'quality',
        ]);
    }
}
