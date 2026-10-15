<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Reminder: '.$this->booking->service->name.' tomorrow');
    }

    public function content(): Content
    {
        $starts = $this->booking->starts_at->timezone($this->booking->timezone);

        return new Content(
            markdown: 'emails.booking-reminder',
            with: [
                'booking' => $this->booking,
                'when' => $starts->format('D, M j Y H:i'),
            ],
        );
    }
}
