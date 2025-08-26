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

        // User::truncate();
        // User::create([
        //     'username' => 'admin',
        //     'name' => 'Admin User',
        //     'email' => 'admin@example.com',
        //     'role'  => 'admin',
        //     'active'    => 1,
        //     'password'  => Hash::make('Admin1234')
        // ]);
        // User::create([
        //     'username' => 'kasir',
        //     'name' => 'Kasir User',
        //     'email' => 'kasir@example.com',
        //     'role'  => 'kasir',
        //     'active'    => 1,
        //     'password'  => Hash::make('Kasir1234')
        // ]);
        //  User::create([
        //     'username' => 'keuangan',
        //     'name' => 'Keuangan User',
        //     'email' => 'keuangan@example.com',
        //     'role'  => 'keuangan',
        //     'active'    => 1,
        //     'password'  => Hash::make('Kasir1234')
        // ]);
        // $this->call(MenuSeeder::class);
        // $this->call(RoleMenuSeeder::class);
        // $this->call(UnitSeeder::class); //seeder satuan
        // $this->call(ContactSeeder::class); //seeder kontak
        $this->call(ItemSeeder::class); //seeder barang
        $this->call(ItemReceivedSeeder::class); //seeder penerimaan barang
        $this->call(ItemSaleSeeder::class); //seeder penjualan barang
        $this->call(ItemAdjustmentSeeder::class); //seeder penyesuaian jumlah barang
    }
}
