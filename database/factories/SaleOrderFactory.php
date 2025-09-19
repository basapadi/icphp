<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SaleOrder>
 */
class SaleOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pelangganIds = Contact::select('id')->where('type','pelanggan')->take(10)->where('status',1)->get()->pluck('id');
        $status = [];

        foreach (config('ihandcashier.sale_order_status') as $key => $v) array_push($status, $key);
        $statusSo = $this->faker->randomElement($status);
        return [
            'kode'              => strtoupper($this->faker->unique()->bothify('SO-##############')), 
            'contact_id'        => $this->faker->randomElement($pelangganIds), 
            'tanggal'           => date('Y-m-d H:i:s'), 
            'tanggal_permintaan'  => Carbon::now()->addDays(10)->format('Y-m-d H:i:s'),
            'status'            => $statusSo,
            'status_pembayaran' => 'unpaid',
            'total'             => 0,
            'catatan'           => 'Dummy SO',
            'created_by'        => $this->faker->randomElement([1,2])
        ];
    }
}
