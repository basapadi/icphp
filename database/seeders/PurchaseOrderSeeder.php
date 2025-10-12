<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\{
    Item,
    ItemPrice,
    PurchaseOrder,
    PurchaseOrderDetail,
    Master,
};

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(resource_path('dummies/items.json'));
        $items = collect(json_decode($json, true));
        PurchaseOrder::truncate();
        PurchaseOrderDetail::truncate();
        PurchaseOrder::factory()->count(30)->create();
        $itemIds = Item::select('id')->where('status',true)->get()->pluck('id')->toArray();

        $pos = PurchaseOrder::all();
        $details = [];
        $totalHarga = 0;
        foreach ($pos as $key => $po) {
            $jlh =fake()->randomElement([4,5,6,7,8]);
            $totalHarga = 0;
            shuffle($itemIds);
            $uniqueItemIds = array_slice($itemIds, 0, $jlh);
            // dd($uniqueItemIds,$jlh);
            for ($i=0; $i < count($uniqueItemIds); $i++) {
                $itemId = $uniqueItemIds[$i];
                $currentItem = $items->where('id',$itemId)->first();
                $harga = $currentItem['harga'];
                $banyak = fake()->randomElement([1,2,3,4,5,6,7,8,9,10]);
                $unitId = $currentItem['satuan_id'];

                array_push($details, [
                    'purchase_order_id' => $po->id,
                    'item_id'           => $itemId,
                    'unit_id'           => $unitId,
                    'harga'        => $harga,
                    'jumlah'            => $banyak,
                    'sub_total'         => (int) $banyak * (double) $harga
                ]);

                $totalHarga += ($harga*$banyak);
            }
            $po->total = $totalHarga;
            $po->save();
        }

        PurchaseOrderDetail::insert($details);
    }
}
