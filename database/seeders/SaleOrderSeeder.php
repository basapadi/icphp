<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\{Item, SaleOrder, SaleOrderDetail, Master};

class SaleOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(resource_path("dummies/items.json"));
        $items = collect(json_decode($json, true));
        SaleOrder::truncate();
        SaleOrderDetail::truncate();
        SaleOrder::factory()->count(10)->create();
        $itemIds = Item::select("id")
            ->where("status", true)
            ->get()
            ->pluck("id")
            ->toArray();

        $pos = SaleOrder::all();
        $details = [];
        $totalHarga = 0;
        foreach ($pos as $key => $so) {
            $jlh = fake()->randomElement([4, 5, 6, 7, 8]);
            $totalHarga = 0;
            shuffle($itemIds);
            $uniqueItemIds = array_slice($itemIds, 0, $jlh);
            for ($i = 0; $i < count($uniqueItemIds); $i++) {
                $itemId = $uniqueItemIds[$i];
                $currentItem = $items->where("id", $itemId)->first();
                $harga = $currentItem["harga"] + (double) 0.10 * $currentItem["harga"];
                $banyak = fake()->randomElement([
                    1,
                    2,
                    3,
                    4,
                    5,
                    6,
                    7,
                    8,
                    9,
                    10,
                ]);
                $unitId = $currentItem["satuan_id"];
                $discountPersen = fake()->randomElement([0, 1, 2]);
                $total = (int) $banyak * (float) $harga;
                $discount = round(($discountPersen / 100) * $total, 0);
                $subtotal = $total - $discount;
                array_push($details, [
                    "sale_order_id" => $so->id,
                    "item_id" => $itemId,
                    "unit_id" => $unitId,
                    "harga" => $harga,
                    "jumlah" => $banyak,
                    "discount" => $discount,
                    "sub_total" => $subtotal,
                    // 'persentase'        => $discountPersen,
                    // 'total'             => $total
                ]);
                $totalHarga += $subtotal;
            }
            $so->total = $totalHarga;
            $so->save();
        }

        SaleOrderDetail::insert($details);
    }
}
