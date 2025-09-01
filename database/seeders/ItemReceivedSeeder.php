<?php

namespace Database\Seeders;

use App\Models\{
    Item,
    ItemPrice,
    ItemReceived,
    ItemReceivedDetail,
    Master,
    ItemReceivedPayment,
    ItemStock
};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class ItemReceivedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $json = File::get(resource_path('dummies/items.json'));
        $items = collect(json_decode($json, true));
        ItemReceived::truncate();
        ItemReceivedDetail::truncate();
        ItemPrice::truncate();
        ItemReceivedPayment::truncate();
        ItemReceived::factory()->count(50)->create();
        ItemStock::truncate();
        $itemIds = Item::select('id')->where('status',true)->get()->pluck('id');
        // $unitIds = Master::select('id')->where('type','BASIC_UNIT')->where('status', true)->take(10)->get()->pluck('id');
        $receiveds = ItemReceived::all();
        $itemPrices = [];
        $details = [];
        $payments = [];
        $preStocks = [];
        foreach ($receiveds as $key => $r) {
            $jlh =fake()->randomElement([4,5,6,7,8]);
            $totalHarga = 0;
            for ($i=0; $i <= $jlh; $i++) {
                $itemId = fake()->randomElement($itemIds);
                $currentItem = $items->where('id',$itemId)->first();
                $harga = $currentItem['harga'];
                $banyak = fake()->randomElement([1,2,3,4,5,6,7,8,9,10]);
               
                // $unitId = fake()->randomElement($unitIds);
                $unitId = $currentItem['satuan_id'];
                $latestPrice = ItemPrice::where('item_id',$itemId)->latest('tanggal_berlaku','desc')->first();

                if(!in_array($r->status_pembayaran, ['refunded','canceled'])){
                    $preStocks[$itemId.'_'.$unitId] = ($preStocks[$itemId.'_'.$unitId] ?? 0) + $banyak;
                }
                
                
                array_push($details, [
                    'item_received_id'  => $r->id,
                    'item_id'           => $itemId,
                    'jumlah'            => $banyak,
                    'harga'             => $harga,
                    'unit_id'           => $unitId
                ]);
                if(empty($latestPrice) || $latestPrice->harga != $harga){
                    array_push($itemPrices,[
                        'item_id'   => $itemId,
                        'harga'     => $harga,
                        'tanggal_berlaku' => Carbon::parse(now())->addDays(30),
                    ]);
                }
                
                $totalHarga += ($harga*$banyak);
            }
            $r->total_harga = $totalHarga;
            $r->save();

            if(in_array($r->tipe_pembayaran, ['tempo','cash']) && in_array($r->status_pembayaran,['partially_paid','paid'])){
                $harga = $r->total_harga;
                if($r->type_pembayaran == 'tempo') $harga += $harga*(30/100);
                array_push($payments, [
                    'trx_received_item_id' => $r->id,
                    'jumlah' => $harga,
                    'metode_pembayaran' => fake()->randomElement(['cash_payment','bank_transfer']),
                    'dibayar_oleh'      => 'App system',
                    'tanggal_pembayaran' => now(),
                    'catatan'           => 'dummy trx payment',
                    'created_by'         => 2,
                    'created_at'         => now()
                ]);
            }
        }

        $stocks = [];
        foreach ($preStocks as $key => $s) {
            $keys = explode('_',$key);
            array_push($stocks,[
                'item_id' => (int)$keys[0],
                'unit_id' => (int)$keys[1],
                'jumlah'  => $s,
                'tanggal_pembaruan' => now()
            ]);

        }
        // dd($stocks);
        // dd(ItemStock::insert($stocks));
        ItemStock::insert($stocks);
        ItemPrice::insert($itemPrices);
        ItemReceivedDetail::insert($details);
        ItemReceivedPayment::insert($payments);
    }
}
