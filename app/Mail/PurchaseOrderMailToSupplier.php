<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class PurchaseOrderMailToSupplier extends BaseMail
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
        $content =  new Content(
            view: 'emails.po',
            with: [
                'po' => $this->po,
                'company' => company()
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
        $po = $this->po;
        $company = company();
        $html = view('pdf.po',compact('po','company'));
        $pdf = $this->generatePdf($company,$html, $po->kode,'S');
        return [
            Attachment::fromData(fn () => $pdf, $po->kode . '.pdf')->withMime('application/pdf')
        ];
    }
}
