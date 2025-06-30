<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Models\UserEventRegistration;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class TicketBought extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected UserEventRegistration $userEventRegistration
    )
    {

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ticket Bought',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.tickets.booked',
            with: [
                'eventName' => $this->userEventRegistration->event->name,
                'eventDate' => $this->userEventRegistration->event->date,
                'ticketQuantity' => $this->userEventRegistration->quantity,
                'ticketType' => $this->userEventRegistration->ticketTypes->ticket_type
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromStorageDisk('public', "invoices/invoice-{$this->userEventRegistration->reference}.pdf")
            ->as('invoice.pdf')
            ->withMime('application/pdf')
            ];
    }
}
