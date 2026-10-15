<?php

use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmedMail;
use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

test('guest booking stores locale and queues a branded confirmation', function () {
    Mail::fake();
    $studio = makeStudio();
    $start = Carbon::parse('next monday 10:00', $studio['tenant']->timezone);

    $this->post('/fr/b/'.$studio['tenant']->slug, [
        'service_id' => $studio['service']->id,
        'staff_member_id' => $studio['staff']->id,
        'starts_at' => $start->toIso8601String(),
        'name' => 'Sofia Pavlenko',
        'phone' => '+380662223344',
        'email' => 'sofia.pavlenko@example.com',
    ])->assertRedirect();

    $booking = Booking::query()->first();
    expect($booking->locale)->toBe('fr');

    Mail::assertQueued(BookingConfirmedMail::class, function (BookingConfirmedMail $mail) use ($booking) {
        expect($mail->booking->is($booking))->toBeTrue()
            ->and($mail->envelope()->from->address)->toBe('noreply@dev4rweb.com');

        return true;
    });
});

test('booking emails render translated branded html', function () {
    $studio = makeStudio();
    $booking = Booking::factory()->create([
        'tenant_id' => $studio['tenant']->id,
        'service_id' => $studio['service']->id,
        'staff_member_id' => $studio['staff']->id,
        'locale' => 'fr',
    ]);
    $booking->load(['client', 'service', 'staffMember', 'tenant']);

    $html = (new BookingConfirmedMail($booking))->render();
    expect($html)
        ->toContain(trans('mail.confirmed.title', [], 'fr'))
        ->toContain($booking->service->name)
        ->toContain('noreply')
        ->not->toContain('MAIL_MAILER');

    $reminder = (new BookingReminderMail($booking))->render();
    expect($reminder)->toContain(trans('mail.reminder.title', [], 'fr'));

    $booking->locale = 'ar';
    $cancelled = (new BookingCancelledMail($booking))->render();
    expect($cancelled)
        ->toContain(trans('mail.cancelled.title', [], 'ar'))
        ->toContain('dir="rtl"');
});

test('mail from address is the bookly noreply mailbox', function () {
    expect(config('mail.from.address'))->toBe('noreply@dev4rweb.com');
});
