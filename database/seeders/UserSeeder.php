<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::truncate();
        User::create([
            'username' => 'admin',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role'  => 'admin',
            'active'    => 1,
            'password'  => Hash::make('User1234')
        ]);
        User::create([
            'username' => 'kasir',
            'name' => 'Kasir User',
            'email' => 'kasir@example.com',
            'role'  => 'kasir',
            'active'    => 1,
            'password'  => Hash::make('Kasir1234')
        ]);
        User::create([
            'username' => 'keuangan',
            'name' => 'Keuangan User',
            'email' => 'keuangan@example.com',
            'role'  => 'keuangan',
            'active'    => 1,
            'password'  => Hash::make('Kasir1234')
        ]);
    }
}
