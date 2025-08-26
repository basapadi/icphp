<?php

namespace Database\Factories;

use App\Models\ItemStock;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ItemStockAdjustmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = [];
        foreach (config('ihandcashier.adjustment_types') as $key => $v) array_push($types, $key);
        $json = File::get(resource_path('dummies/items.json'));
        $items = collect(json_decode($json, true));
        $itemIds = $items->select('id')->pluck('id');
        $item = $items->where('id', $this->faker->randomElement($itemIds))->first();
        $adjustment = $this->faker->randomElement([1,2,3,4,5,6]);
        $stock = ItemStock::where('item_id',$item['id'])->first();
        $actualStock = (double) $stock->jumlah - (double) $adjustment;
        $stock->jumlah +=$adjustment;
        $stock->save();
        return [
            'item_id' => $item['id'],
            'system_stock' => $stock->jumlah,
            'actual_stock' => $actualStock,
            'adjustment_stock' => $adjustment,
            'adjustment_type' =>  $this->faker->randomElement($types),
            'unit_id' => $item['satuan_id'],
            'catatan' => 'Adjustment dummy',
            'created_by' => 2,
            'created_at' => now()
        ];
    }
}
