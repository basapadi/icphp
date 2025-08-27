<?php

namespace Database\Seeders;

use App\Models\ItemStockAdjustment;
use Illuminate\Database\Seeder;
use App\Models\ItemStock;
use Illuminate\Support\Facades\File;

class ItemAdjustmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemStockAdjustment::truncate();
        $preAds = [];
        for ($i=0; $i < 30 ; $i++) { 
            $types = [];
            foreach (config('ihandcashier.adjustment_types') as $key => $v) array_push($types, $key);
            $type = fake()->randomElement($types);
            $json = File::get(resource_path('dummies/items.json'));
            $items = collect(json_decode($json, true));
            $itemIds = $items->select('id')->pluck('id');
            $item = $items->where('id', fake()->randomElement($itemIds))->first();
            $adjustment = fake()->randomElement([1,2,3,4,5,6]);
            $stock = ItemStock::where('item_id',$item['id'])->where('unit_id',$item['satuan_id'])->first();
            $systemStock = $stock->jumlah;
            if(in_array($type, ['return_out','return_in'])) $actualStock = (double) $stock->jumlah;
            else $actualStock = (double) $stock->jumlah - (double) $adjustment;
           
            if(in_array($type, ['return_out','return_in']))$stock->jumlah = $actualStock - $adjustment;
            else $stock->jumlah = $actualStock;
            $stock->save();

            $ads = [
                'item_id' => $item['id'],
                'system_stock' => $systemStock,
                'actual_stock' => $actualStock,
                'adjustment_stock' => $type == 'return_out' ? $adjustment : $adjustment ,
                'final_stock' => $stock->jumlah,
                'adjustment_type' =>  $type,
                'unit_id' => $item['satuan_id'],
                'catatan' => 'Adjustment dummy',
                'created_by' => 2,
                'created_at' => now()
            ];

            array_push($preAds,$ads);
        }

        ItemStockAdjustment::insert($preAds);
    }
}
