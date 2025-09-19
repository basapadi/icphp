<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemSale>
 */
class ItemSaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pemasokIds = Contact::select('id')->where('type','pelanggan')->take(10)->where('status',1)->get()->pluck('id');
        $statpem = [];
        $typem = [];
        $mepem = [];
        foreach (config('ihandcashier.payment_status') as $key => $v) array_push($statpem, $key);
        foreach (config('ihandcashier.payment_types') as $key => $v) array_push($typem, $key);
        foreach (config('ihandcashier.payment_methods.sale') as $key => $v) array_push($mepem, $key);
        $typeBayar = $this->faker->randomElement($typem);
        $statusPembayaran = $this->faker->randomElement($statpem);
        return [
            'kode_transaksi'    => strtoupper($this->faker->unique()->bothify('TS-##############')), 
            'contact_id'        => $this->faker->randomElement($pemasokIds), 
            'tanggal_jual'    => date('Y-m-d H:i:s'), 
            'dijual_oleh'     => 'Kasir 2',
            'catatan'           => 'dummy sale',
            'total_harga'       => 0,
            'potongan_harga'    => 0,
            'status_pembayaran' => $typeBayar == 'cash'?'paid':$statusPembayaran,
            'tipe_pembayaran'   => $typeBayar,
            'metode_pembayaran' => $this->faker->randomElement($mepem),
            'syarat_pembayaran' => $typeBayar == 'tempo' ? '2/15 N25': '',
            'created_by'        => $this->faker->randomElement([1,2])
        ];
    }
}
