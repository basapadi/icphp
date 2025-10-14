<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    PurchaseInvoice,
    PurchaseInvoiceDetail,
    PurchaseInvoiceItemReceived
};

class PurchaseInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PurchaseInvoice::truncate();
        PurchaseInvoiceDetail::truncate();
        PurchaseInvoiceItemReceived::truncate();
    }
}