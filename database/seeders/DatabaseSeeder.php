<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Superadmin
        User::create([
            'name' => 'Superadmin SAR',
            'email' => 'superadmin@tengganglopi.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'superadmin',
        ]);

        // Default Petugas
        User::create([
            'name' => 'Petugas Lapangan',
            'email' => 'petugas@tengganglopi.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'petugas',
        ]);
    }
}
