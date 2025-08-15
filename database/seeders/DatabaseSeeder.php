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
        User::truncate();
        User::create([
            'username' => 'admin',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role'  => 'admin',
            'active'    => 1,
            'password'  => Hash::make('Admin1234')
        ]);
        User::create([
            'username' => 'kasir',
            'name' => 'Kasir User',
            'email' => 'kasir@example.com',
            'role'  => 'kasir',
            'active'    => 1,
            'password'  => Hash::make('Kasir1234')
        ]);
        $this->call(MenuSeeder::class);
    }
}
