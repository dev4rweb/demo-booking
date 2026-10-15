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

    public function __construct(public Booking $booking)
    {
        $this->locale($booking->locale ?: config('app.fallback_locale'));
    }

    public function envelope(): Envelope
    {
        $view = BookingMailView::extras($this->booking);

        return BookingMailView::envelope(
            trans('mail.reminder.subject', ['service' => $view['service']], $view['locale']),
        );
    }

    public function content(): Content
    {
        $view = BookingMailView::extras($this->booking);
        $locale = $view['locale'];

        return new Content(
            html: 'emails.booking-reminder',
            with: [
                ...$view,
                'title' => trans('mail.reminder.title', [], $locale),
                'hello' => trans('mail.reminder.hello', ['name' => $view['client']], $locale),
                'intro' => trans('mail.reminder.intro', ['business' => $view['business']], $locale),
                'serviceLabel' => trans('mail.reminder.service', [], $locale),
                'staffLabel' => trans('mail.reminder.staff', [], $locale),
                'whenLabel' => trans('mail.reminder.when', [], $locale),
                'thanks' => trans('mail.reminder.thanks', [], $locale),
            ],
        );
    }
}
