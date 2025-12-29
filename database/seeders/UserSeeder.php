<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin (sudah terverifikasi)
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'), // Ganti dengan password yang aman
            'role' => 'admin',
            'email_verified_at' => now(),
            'is_verified_by_admin' => true, // Admin otomatis verified
            'verified_at' => now(),
        ]);

        // Contoh user engineer (belum terverifikasi)
        User::create([
            'name' => 'John Engineer',
            'email' => 'engineer@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'engineer',
            'email_verified_at' => null,
            'is_verified_by_admin' => false, // Perlu verifikasi admin
        ]);

        // Contoh user NMS (belum terverifikasi)
        User::create([
            'name' => 'Jane NMS',
            'email' => 'nms@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'nms',
            'email_verified_at' => null,
            'is_verified_by_admin' => false, // Perlu verifikasi admin
        ]);
    }
}