<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class BookingConfirmedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Booking confirmed — '.$this->booking->service->name);
    }

    public function content(): Content
    {
        $starts = $this->booking->starts_at->timezone($this->booking->timezone);

        return new Content(
            markdown: 'emails.booking-confirmed',
            with: [
                'booking' => $this->booking,
                'when' => $starts->format('D, M j Y H:i'),
                'manageUrl' => URL::temporarySignedRoute(
                    'bookings.manage',
                    now()->addDays(30),
                    ['booking' => $this->booking->id, 'token' => $this->booking->manage_token],
                ),
            ],
        );
    }
}
