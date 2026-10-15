<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';

    protected $description = 'Email clients about confirmed bookings starting in about 24 hours';

    public function handle(NotificationService $notifications): int
    {
        $from = now()->addHours(23);
        $to = now()->addHours(25);

        $bookings = Booking::query()
            ->with(['client', 'service', 'staffMember', 'tenant'])
            ->where('status', Booking::STATUS_CONFIRMED)
            ->whereNull('reminder_sent_at')
            ->whereBetween('starts_at', [$from, $to])
            ->get();

        foreach ($bookings as $booking) {
            $notifications->bookingReminder($booking);
            $booking->update(['reminder_sent_at' => now()]);
        }

        $this->info('Reminders sent: '.$bookings->count());

        return self::SUCCESS;
    }
}
