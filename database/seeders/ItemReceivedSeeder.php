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

class ItemReceivedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemReceived::truncate();
        ItemReceivedDetail::truncate();
        ItemPrice::truncate();
        ItemReceivedPayment::truncate();
        ItemReceived::factory()->count(50)->create();
        ItemStock::truncate();
        $itemIds = Item::select('id')->where('status',true)->get()->pluck('id');
        $unitIds = Master::select('id')->where('type','BASIC_UNIT')->where('status', true)->take(10)->get()->pluck('id');
        $receiveds = ItemReceived::all();
        $itemPrices = [];
        $details = [];
        $payments = [];
        $preStocks = [];
        foreach ($receiveds as $key => $r) {
            $jlh =fake()->randomElement([4,5,6,7,8]);
            $totalHarga = 0;
            for ($i=0; $i <= $jlh; $i++) { 
                $harga = fake()->randomElement([50000,35000,47000,150000,100000,25000,15000]);
                $banyak = fake()->randomElement([10,20,30,40,50,60,70,80,90,100]);
                $itemId = fake()->randomElement($itemIds);
                $unitId = fake()->randomElement($unitIds);
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
                if($r->type_pembayaran == 'tempo') $harga*(30/100);
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
