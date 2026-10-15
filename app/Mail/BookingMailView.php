<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;

class BookingMailView
{
    public static function envelope(string $subject): Envelope
    {
        return new Envelope(
            from: new Address(
                (string) config('mail.from.address', 'noreply@dev4rweb.com'),
                (string) config('mail.from.name', 'Bookly'),
            ),
            subject: $subject,
        );
    }

    /**
     * @return array<string, string>
     */
    public static function extras(Booking $booking): array
    {
        $supported = config('localization.supported', ['en']);
        $locale = in_array($booking->locale, $supported, true) ? $booking->locale : 'en';
        $rtl = config('localization.rtl', ['ar']);
        $when = $booking->starts_at->timezone($booking->timezone)->format('D, M j Y H:i');

        return [
            'locale' => $locale,
            'dir' => in_array($locale, $rtl, true) ? 'rtl' : 'ltr',
            'when' => $when.' ('.$booking->timezone.')',
            'business' => $booking->tenant->name,
            'service' => $booking->service->name,
            'staff' => $booking->staffMember->name,
            'client' => $booking->client->name,
            'price' => '$'.number_format($booking->service->price_cents / 100, 2).' USD',
        ];
    }
}
