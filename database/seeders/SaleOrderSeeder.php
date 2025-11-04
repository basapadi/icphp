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
            ->pluck("id");

        $pos = SaleOrder::all();
        $details = [];
        $totalHarga = 0;
        foreach ($pos as $key => $po) {
            $jlh = fake()->randomElement([4, 5, 6, 7, 8]);
            $totalHarga = 0;

            for ($i = 0; $i <= $jlh; $i++) {
                $itemId = fake()->randomElement($itemIds);
                $currentItem = $items->where("id", $itemId)->first();
                $harga = $currentItem["harga"];
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
                $discountPersen = fake()->randomElement([0, 1, 2, 3, 4, 5]);
                $total = (int) $banyak * (float) $harga;
                $discount = round(($discountPersen / 100) * $total, 0);
                $subtotal = $total - $discount;
                array_push($details, [
                    "sale_order_id" => $po->id,
                    "item_id" => $itemId,
                    "unit_id" => $unitId,
                    "harga" => $harga,
                    "discount" => $discount,
                    "jumlah" => $banyak,
                    "sub_total" => $subtotal,
                    // 'persentase'        => $discountPersen,
                    // 'total'             => $total
                ]);
                $totalHarga += $subtotal;
            }
            $po->total = $totalHarga;
            $po->save();
        }

        SaleOrderDetail::insert($details);
    }
}
