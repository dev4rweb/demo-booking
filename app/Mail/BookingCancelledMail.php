<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelledMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Booking cancelled — '.$this->booking->tenant->name);
    }

    public function content(): Content
    {
        $starts = $this->booking->starts_at->timezone($this->booking->timezone);

        return new Content(
            markdown: 'emails.booking-cancelled',
            with: [
                'booking' => $this->booking,
                'when' => $starts->format('D, M j Y H:i'),
            ],
        );
    }
}
