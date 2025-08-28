<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Item::truncate();
        // Item::factory()->count(10)->create();
        $json = File::get(resource_path('dummies/items.json'));
        $items = json_decode($json, true);
        $preInsert = [];
        foreach ($items as $item) {
            array_push($preInsert,[
                'id' => $item['id'],
                'kode_barang' => $item['kode_barang'],
                'barcode' => $item['barcode'],
                'nama' => $item['nama'],
                'gambar' => fake()->imageUrl(640, 480, 'products', true, 'Barang'),
                'kategori' => $item['kategori'],
                'status' => 1,
            ]);
        }

        Item::insert($preInsert);
    }
}
