<?php

namespace Database\Seeders;

use App\Models\{
    Item,
    ItemPrice,
    ItemReceived,
    ItemReceivedDetail,
    Master,
    ItemReceivedPayment
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
        $itemIds = Item::select('id')->where('status',true)->get()->pluck('id');
        $unitIds = Master::select('id')->where('type','UNIT')->where('status', true)->get()->pluck('id');
        $receiveds = ItemReceived::all();
        $itemPrices = [];
        $details = [];
        $payments = [];
        foreach ($receiveds as $key => $r) {
            $jlh =fake()->randomElement([4,5,6,7,8]);
            $totalHarga = 0;
            for ($i=0; $i <= $jlh; $i++) { 
                $harga = fake()->randomElement([50000,35000,47000,150000,100000,25000,15000]);
                $banyak = fake()->randomElement([1,2,3,4,5,6,7,8,9,10]);
                $itemId = fake()->randomElement($itemIds);
                $latestPrice = ItemPrice::where('item_id',$itemId)->latest('tanggal_berlaku','desc')->first();
                
                array_push($details, [
                    'item_received_id'  => $r->id,
                    'item_id'           => $itemId,
                    'jumlah'            => $banyak,
                    'harga'             => $harga,
                    'unit_id'           => fake()->randomElement($unitIds)
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
        ItemPrice::insert($itemPrices);
        ItemReceivedDetail::insert($details);
        ItemReceivedPayment::insert($payments);
    }
}
