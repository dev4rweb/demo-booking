<?php

namespace App\Services;

use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmedMail;
use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function __construct(
        private TelegramService $telegram,
        private TelegramLinkService $links,
    ) {}

    public function bookingCreated(Booking $booking): void
    {
        Mail::to($booking->client->email)->queue(new BookingConfirmedMail($booking));

        $this->notifyLinkedChats($booking, 'app.telegram.notify_new');
    }

    public function bookingCancelled(Booking $booking): void
    {
        Mail::to($booking->client->email)->queue(new BookingCancelledMail($booking));

        $this->notifyLinkedChats($booking, 'app.telegram.notify_cancelled');
    }

    public function bookingReminder(Booking $booking): void
    {
        Mail::to($booking->client->email)->queue(new BookingReminderMail($booking));
    }

    private function notifyLinkedChats(Booking $booking, string $key): void
    {
        $locale = $booking->locale ?: 'en';
        $when = $booking->starts_at->timezone($booking->timezone)->format('Y-m-d H:i');
        $text = trans($key, [
            'client' => $booking->client->name,
            'service' => $booking->service->name,
            'staff' => $booking->staffMember->name,
            'when' => $when,
        ], $locale);

        foreach ($this->links->chatsFor($booking) as $chatId) {
            $this->telegram->sendMessage($chatId, $text);
        }
    }
}
