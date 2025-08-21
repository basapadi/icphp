<?php

namespace Database\Seeders;

use App\Models\ItemReceived;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemReceivedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemReceived::truncate();
        ItemReceived::factory()->count(10)->create();
    }
}
