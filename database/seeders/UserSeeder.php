<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin Utama
        User::updateOrCreate(
            ['email' => 'admin@bmtpondokhijau.id'], 
            [
                'name' => 'Admin BMT Pondok Hijau',
                'username' => 'adminbmt', // Kolom yang ditambahkan
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ]
        );

        // 2. Akun Nasabah / Anggota
        User::updateOrCreate(
            ['email' => 'rayhan@nasabah.id'], 
            [
                'name' => 'Rayhan Hilmy Gothama',
                'username' => 'rayhanhilmy', // Kolom yang ditambahkan
                'password' => Hash::make('password123'),
                'role' => 'nasabah'
            ]
        );
    }
}