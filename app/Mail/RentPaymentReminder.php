<?php

namespace App\Mail;

use App\Models\Building;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentPaymentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public Building $building;
    public int $daysUntilDue;
    public string $nextPaymentDate;

    /**
     * Create a new message instance.
     */
    public function __construct(Building $building, int $daysUntilDue, string $nextPaymentDate)
    {
        $this->building = $building;
        $this->daysUntilDue = $daysUntilDue;
        $this->nextPaymentDate = $nextPaymentDate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Rent Payment Reminder - {$this->building->name} (Due in {$this->daysUntilDue} days)",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.rent-payment-reminder',
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
