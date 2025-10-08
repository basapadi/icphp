<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemReceived>
 */
class ItemReceivedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pemasokIds = Contact::select('id')->where('type','pemasok')->take(10)->where('status',1)->get()->pluck('id');
        $statpem = [];
        foreach (config('ihandcashier.receive_item_status') as $key => $v) array_push($statpem, $key);
        $status = $this->faker->randomElement($statpem);
        return [
            'kode_transaksi'    => strtoupper($this->faker->unique()->bothify('TR-##############')), 
            'contact_id'        => $this->faker->randomElement($pemasokIds), 
            'tanggal_terima'    => date('Y-m-d H:i:s'), 
            'diterima_oleh'     => 'Kasir 1',
            'catatan'           => 'dummy transaction',
            'total_harga'       => 0,
            'status'            => $status,
            'created_by'        => $this->faker->randomElement([1,2])
        ];
    }
}
