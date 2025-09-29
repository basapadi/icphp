<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::truncate();

        $preInsert = [
            [
                'name' => 'toko',
                'label' => 'Toko',
                'status' => true,
                'data' => json_encode([
                    'namaToko' => 'HMP Basapadi I',
                    'telepon' => '+62812123123123',
                    'email' => 'hmpbasapadi@gmail.com',
                    'alamat' => 'Jl. Gereja, Siabal Abal II, Kec. Sipahutar, Kabupaten Tapanuli Utara, Sumatera Utara 22471',
                    'pemilik' => 'Bachtiar Panjaitan',
                    'logo' => '',

                ])
            ],
            [
                'name' => 'mailing',
                'label' => 'Mailing',
                'status' => true,
                'data' => json_encode([
                    'driver' => 'smtp',
                    'host' => 'smtp.gmail.com',
                    'port' => '587',
                    'encryption' => 'tls',
                    'username' => 'bepedebug@gmail.com',
                    'password' => '',
                    'fromAddress' => 'hmpbasapadi@gmail.com',
                    'fromName' => 'IhandCashier App',

                ])
            ]
        ];

        Setting::insert($preInsert);

    }
}
