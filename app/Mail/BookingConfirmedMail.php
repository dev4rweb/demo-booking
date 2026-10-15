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

    public function __construct(public Booking $booking)
    {
        $this->locale($booking->locale ?: config('app.fallback_locale'));
    }

    public function envelope(): Envelope
    {
        $view = BookingMailView::extras($this->booking);

        return BookingMailView::envelope(
            trans('mail.confirmed.subject', ['service' => $view['service']], $view['locale']),
        );
    }

    public function content(): Content
    {
        $view = BookingMailView::extras($this->booking);
        $locale = $view['locale'];

        return new Content(
            html: 'emails.booking-confirmed',
            with: [
                ...$view,
                'title' => trans('mail.confirmed.title', [], $locale),
                'hello' => trans('mail.confirmed.hello', ['name' => $view['client']], $locale),
                'intro' => trans('mail.confirmed.intro', ['business' => $view['business']], $locale),
                'serviceLabel' => trans('mail.confirmed.service', [], $locale),
                'staffLabel' => trans('mail.confirmed.staff', [], $locale),
                'whenLabel' => trans('mail.confirmed.when', [], $locale),
                'priceLabel' => trans('mail.confirmed.price', [], $locale),
                'manage' => trans('mail.confirmed.manage', [], $locale),
                'thanks' => trans('mail.confirmed.thanks', [], $locale),
                'manageUrl' => URL::temporarySignedRoute(
                    'bookings.manage',
                    now()->addDays(30),
                    ['booking' => $this->booking->id, 'token' => $this->booking->manage_token],
                ),
            ],
        );
    }
}
