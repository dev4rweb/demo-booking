<?php

namespace App\Services;

use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmedMail;
use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function __construct(private TelegramService $telegram) {}

    public function bookingCreated(Booking $booking): void
    {
        Mail::to($booking->client->email)->queue(new BookingConfirmedMail($booking));

        $this->notifyOwner(
            $booking,
            "New booking: {$booking->client->name} — {$booking->service->name} with {$booking->staffMember->name} at ".$booking->starts_at->timezone($booking->timezone)->format('Y-m-d H:i')
        );
    }

    public function bookingCancelled(Booking $booking): void
    {
        Mail::to($booking->client->email)->queue(new BookingCancelledMail($booking));

        $this->notifyOwner(
            $booking,
            "Cancelled booking: {$booking->client->name} — {$booking->service->name} at ".$booking->starts_at->timezone($booking->timezone)->format('Y-m-d H:i')
        );
    }

    public function bookingReminder(Booking $booking): void
    {
        Mail::to($booking->client->email)->queue(new BookingReminderMail($booking));
    }

    private function notifyOwner(Booking $booking, string $text): void
    {
        $chatId = $booking->tenant->telegram_chat_id;

        if (! $chatId) {
            return;
        }

        $this->telegram->sendMessage($chatId, $text);
    }
}
