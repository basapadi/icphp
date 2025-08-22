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
        $pemasokIds = Contact::select('id')->where('type','pemasok')->where('status',1)->get()->pluck('id');
        $statpem = [];
        $typem = [];
        $mepem = [];
        foreach (config('ihandcashier.payment_status') as $key => $v) array_push($statpem, $key);
        foreach (config('ihandcashier.payment_types') as $key => $v) array_push($typem, $key);
        foreach (config('ihandcashier.payment_methods.receive') as $key => $v) array_push($mepem, $key);
        $typeBayar = $this->faker->randomElement($typem);
        return [
            'kode_transaksi'    => strtoupper($this->faker->unique()->bothify('TR-##########')), 
            'contact_id'        => $this->faker->randomElement($pemasokIds), 
            'tanggal_terima'    => date('Y-m-d H:i:s'), 
            'diterima_oleh'     => $this->faker->firstNameMale(),
            'catatan'           => 'dummy transaction',
            'total_harga'       => 0,
            'potongan_harga'    => 0,
            'status_pembayaran' => $this->faker->randomElement($statpem),
            'tipe_pembayaran'   => $typeBayar,
            'metode_pembayaran' => $this->faker->randomElement($mepem),
            'syarat_pembayaran' => $typeBayar == 'tempo' ? '2/15 N30': '',
            'created_by'        => $this->faker->randomElement([1,2])
        ];
    }
}
