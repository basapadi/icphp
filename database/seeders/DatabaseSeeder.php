<?php

namespace Database\Seeders;

use App\Models\Trash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(UserSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(RoleMenuSeeder::class);
        $this->call(UnitSeeder::class); //seeder satuan
        $this->call(ContactSeeder::class); //seeder kontak
        $this->call(ItemSeeder::class); //seeder barang
        $this->call(ItemReceivedSeeder::class); //seeder penerimaan barang
        $this->call(ItemSaleSeeder::class); //seeder penjualan barang
        $this->call(ItemAdjustmentSeeder::class); //seeder penyesuaian jumlah barang
        $this->call(PurchaseOrderSeeder::class); //seeder PO
        $this->call(SaleOrderSeeder::class); //seeder SO
        Trash::truncate();
    }
}
