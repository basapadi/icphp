<?php

namespace Database\Seeders;
use App\Models\{
    Item,
    ItemPrice,
    ItemReceived,
    ItemReceivedDetail,
    ItemSale,
    ItemSaleDetail,
    Master,
    ItemStock
};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class ItemSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemSale::truncate();
        ItemSaleDetail::truncate();
        ItemSale::factory()->count(50)->create();
        $json = File::get(resource_path('dummies/items.json'));
        $items = collect(json_decode($json, true));

        $itemIds = ItemPrice::distinct('item_id')->get()->pluck('item_id');
        $sales = ItemSale::all();
        $details = [];
        $payments = [];
        foreach ($sales as $key => $r) {
            $jlh =fake()->randomElement([1,2,3,4,5,6,7,8]);
            $totalHarga = 0;
            for ($i=0; $i <= $jlh; $i++) { 
                $banyak = fake()->randomElement([1,2,3,4,5]);
                $itemId = fake()->randomElement($itemIds);
                $latestPrice = ItemPrice::where('item_id',$itemId)->latest('tanggal_berlaku','desc')->first();
                $harga = @$latestPrice->harga + ($latestPrice->harga* (15/100))??0;
                $currentItem = $items->where('id',$itemId)->first();
                $unitId = $currentItem['satuan_id'];
                array_push($details, [
                    'item_sale_id'  => $r->id,
                    'item_id'           => $itemId,
                    'jumlah'            => $banyak,
                    'harga'             => $harga,
                    'unit_id'           => $unitId
                ]);
                $totalHarga += ($harga*$banyak);
            }
            $r->total_harga = $totalHarga;
            $r->save();
        }
        ItemSaleDetail::insert($details);
    }
}
