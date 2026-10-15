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

    public function __construct(public Booking $booking)
    {
        $this->locale($booking->locale ?: config('app.fallback_locale'));
    }

    public function envelope(): Envelope
    {
        $view = BookingMailView::extras($this->booking);

        return BookingMailView::envelope(
            trans('mail.cancelled.subject', ['business' => $view['business']], $view['locale']),
        );
    }

    public function content(): Content
    {
        $view = BookingMailView::extras($this->booking);
        $locale = $view['locale'];

        return new Content(
            html: 'emails.booking-cancelled',
            with: [
                ...$view,
                'title' => trans('mail.cancelled.title', [], $locale),
                'hello' => trans('mail.cancelled.hello', ['name' => $view['client']], $locale),
                'body' => trans('mail.cancelled.body', [
                    'business' => $view['business'],
                    'when' => $view['when'],
                ], $locale),
                'thanks' => trans('mail.cancelled.thanks', [], $locale),
            ],
        );
    }
}
