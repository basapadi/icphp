<?php

namespace App\Jobs;

use App\Mail\PurchaseOrderMailToSupplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\{
    PurchaseOrder
};

class SendPoEmail implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Dispatchable;
    public $tries = 3; 
    public $backoff = 10;
    protected $po;

    public function __construct($po)
    {
        $this->po = $po;
    }

    public function handle()
    {
        $po = PurchaseOrder::find($this->po->id);
        try {
            Mail::to($this->po->contact->email)->send(new PurchaseOrderMailToSupplier($this->po));
            $po->status = 'sended';
            $po->save();
        }catch (\Exception $e) {
            throw $e;
        }
    }
}