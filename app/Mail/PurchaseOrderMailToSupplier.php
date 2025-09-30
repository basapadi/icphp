<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use stdClass;

class PurchaseOrderMailToSupplier extends Mailable
{
    use Queueable, SerializesModels;

    public $po;
    /**
     * Create a new message instance.
     */
    public function __construct($po)
    {
        $this->po = $po;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Purchase Order #' . $this->po->kode,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $company = Setting::where('name', 'toko')->where('status', true)->first()->data ?? (object) [];
        $content =  new Content(
            view: 'emails.po',
            with: [
                'po' => $this->po,
                'company' => $company
            ]
        );
        return $content;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
