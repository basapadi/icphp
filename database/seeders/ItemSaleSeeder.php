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
    ItemSalePayment,
    ItemStock
};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ItemSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemSale::truncate();
        ItemSaleDetail::truncate();
        ItemSalePayment::truncate();
        ItemSale::factory()->count(150)->create();
        $itemIds = ItemPrice::distinct('item_id')->get()->pluck('item_id');
        $unitIds = Master::select('id')->where('type','UNIT')->where('status', true)->take(10)->get()->pluck('id');
        $sales = ItemSale::all();
        $details = [];
        $payments = [];
        foreach ($sales as $key => $r) {
            $jlh =fake()->randomElement([1,2,3,4,54,5,6,7,8]);
            $totalHarga = 0;
            for ($i=0; $i <= $jlh; $i++) { 
                $banyak = fake()->randomElement([1,2,3,4,5]);
                $itemId = fake()->randomElement($itemIds);
                $latestPrice = ItemPrice::where('item_id',$itemId)->latest('tanggal_berlaku','desc')->first();
                $harga = @$latestPrice->harga + ($latestPrice->harga* (15/100))??0;

                // $oldStocks = ItemReceivedDetail::where('item_id',$itemId)->whereNotIn('status_pembayaran',['refund','canceled'])->sum('jumlah');
                // dd($oldStocks);
                array_push($details, [
                    'item_sale_id'  => $r->id,
                    'item_id'           => $itemId,
                    'jumlah'            => $banyak,
                    'harga'             => $harga,
                    'unit_id'           => fake()->randomElement($unitIds)
                ]);
                $totalHarga += ($harga*$banyak);
            }
            $r->total_harga = $totalHarga;
            $r->save();

            if(in_array($r->tipe_pembayaran, ['tempo','cash']) && in_array($r->status_pembayaran,['partially_paid','paid'])){
                $harga = $r->total_harga;
                if($r->type_pembayaran == 'tempo') $harga*(30/100);
                array_push($payments, [
                    'trx_sale_item_id' => $r->id,
                    'jumlah' => $harga,
                    'metode_pembayaran' => fake()->randomElement(['cash_payment','bank_transfer']),
                    'dijual_oleh'      => 'App system',
                    'tanggal_pembayaran' => now(),
                    'catatan'           => 'dummy trx payment',
                    'created_by'         => 2,
                    'created_at'         => now()
                ]);
            }
        }
        ItemSaleDetail::insert($details);
        ItemSalePayment::insert($payments);
    }
}
