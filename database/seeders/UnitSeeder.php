<?php

namespace Database\Seeders;

use App\Models\Master;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conf_units = config('ihandcashier.units');
        $conf_basic_units = config('ihandcashier.basic_units');
        $preInsert = [];
        DB::beginTransaction();
        Master::where('type','BASIC_UNIT')->delete();
        Master::where('type','UNIT')->delete();
        foreach ($conf_basic_units as $key => $bu) {
            array_push($preInsert,[
                'id' => $bu[0],
                'type' => 'BASIC_UNIT',
                'kode' => $key,
                'nama' => $bu[1],
                'status' => 1,
                'attributes' => null
            ]);
        }
        foreach ($conf_units as $k => $cu) {
            array_push($preInsert,[
                'id' => $cu[0],
                'type' => 'UNIT',
                'kode' => $k,
                'nama' => $cu[1],
                'status' => 1,
                'attributes' => json_encode([
                    'to' => collect($preInsert)->select('id','kode','nama')->where('kode',$cu[3])->first(),
                    'conversion' => $cu[2]
                ])
            ]);
        }

        Master::insert($preInsert);
        DB::commit();
    }
}
