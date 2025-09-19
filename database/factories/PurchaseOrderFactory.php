<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pemasokIds = Contact::select('id')->where('type','pemasok')->take(10)->where('status',1)->get()->pluck('id');
        $status = [];

        foreach (config('ihandcashier.purchase_order_status') as $key => $v) array_push($status, $key);
        $statusPo = $this->faker->randomElement($status);
        $approvalStatus = 'pending';
        $approvedAt = null;
        if(in_array($statusPo,['approved','sended','partial_received','received'])){
             $approvalStatus = 'approved';
             $approvedAt = now();
        }
        else $approvalStatus = $this->faker->randomElement(['pending','rejected']);
        return [
            'kode'              => strtoupper($this->faker->unique()->bothify('PO-##############')), 
            'contact_id'        => $this->faker->randomElement($pemasokIds), 
            'tanggal'           => date('Y-m-d H:i:s'), 
            'tanggal_perkiraan_datang'  => Carbon::now()->addDays(10)->format('Y-m-d H:i:s'),
            'status'            => $statusPo,
            'approval_by'       => 1,
            'approval_status'   => $approvalStatus,
            'approved_at'       => $approvedAt,
            'total'             => 0,
            'catatan'           => 'Dummy PO',
            'created_by'        => $this->faker->randomElement([1,2])
        ];
    }
}
