<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WaterBillPaymentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public int $unpaidCount;
    public float $totalAmount;

    /**
     * Create a new message instance.
     */
    public function __construct(int $unpaidCount, float $totalAmount)
    {
        $this->unpaidCount = $unpaidCount;
        $this->totalAmount = $totalAmount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Water Bill Payment Reminder - {$this->unpaidCount} Unpaid Bill(s)",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.water-bill-payment-reminder',
        );
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
