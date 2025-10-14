<?php

namespace Database\Seeders;

use App\Models\Master;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Master::where('type','TAX')->delete();
        $taxes = config('ihandcashier.taxes');
        $preInsert = [];
        foreach ($taxes as $key => $tax) {
            array_push($preInsert,[
                'id' => $tax[0],
                'type' => 'TAX',
                'kode' => $key,
                'nama' => $tax[1],
                'status' => 1,
                'attributes' => json_encode(['value' => $tax[2]])
            ]);
        }
        Master::insert($preInsert);
    }
}