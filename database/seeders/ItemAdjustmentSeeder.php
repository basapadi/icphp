<?php

namespace Database\Seeders;

use App\Models\ItemStockAdjustment;
use Illuminate\Database\Seeder;

class ItemAdjustmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemStockAdjustment::truncate();
        ItemStockAdjustment::factory()->count(50)->create();
    }
}
