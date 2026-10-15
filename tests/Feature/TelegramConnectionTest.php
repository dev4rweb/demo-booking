<?php

use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Http;

function enableTelegramBot(): void
{
    config([
        'services.telegram.bot_token' => 'test-bot-token',
        'services.telegram.bot_username' => 'BooklyDemoBot',
        'services.telegram.webhook_secret' => 'hook-secret',
    ]);
}

test('owner telegram page hides connect when the bot is not configured', function () {
    $studio = makeStudio();

    $this->actingAs($studio['owner'])
        ->get(route('telegram.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Settings/Telegram')
            ->where('enabled', false)
            ->where('deepLink', null)
            ->where('connected', false)
        );
});

test('owner telegram page offers a deep link when the bot is configured', function () {
    enableTelegramBot();
    $studio = makeStudio();

    $this->actingAs($studio['owner'])
        ->get(route('telegram.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('enabled', true)
            ->where('scope', 'business')
            ->where('deepLink', fn ($link) => is_string($link) && str_starts_with($link, 'https://t.me/BooklyDemoBot?start='))
        );

    expect($studio['tenant']->fresh()->telegram_connect_token)->not->toBeEmpty();
});

test('staff can connect their own telegram chat', function () {
    enableTelegramBot();
    $studio = makeStudio();

    $this->actingAs($studio['staffUser'])
        ->get(route('telegram.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('enabled', true)
            ->where('scope', 'staff')
            ->where('deepLink', fn ($link) => is_string($link) && str_contains($link, 't.me/BooklyDemoBot'))
        );

    expect($studio['staff']->fresh()->telegram_connect_token)->not->toBeEmpty();
});

test('webhook rejects a wrong secret', function () {
    enableTelegramBot();

    $this->postJson('/telegram/webhook', ['message' => ['text' => '/start abc', 'chat' => ['id' => 1]]], [
        'X-Telegram-Bot-Api-Secret-Token' => 'wrong',
    ])->assertUnauthorized();
});

test('webhook links a one-time code to the business chat', function () {
    enableTelegramBot();
    Http::fake(['https://api.telegram.org/*' => Http::response(['ok' => true])]);

    $studio = makeStudio();
    $studio['tenant']->update(['telegram_connect_token' => 'biztoken123']);

    $this->postJson('/telegram/webhook', [
        'message' => [
            'text' => '/start biztoken123',
            'chat' => ['id' => 991122],
        ],
    ], [
        'X-Telegram-Bot-Api-Secret-Token' => 'hook-secret',
    ])->assertOk();

    $studio['tenant']->refresh();
    expect($studio['tenant']->telegram_chat_id)->toBe('991122')
        ->and($studio['tenant']->telegram_connect_token)->toBeNull();
});

test('webhook links a one-time code to a staff member', function () {
    enableTelegramBot();
    Http::fake(['https://api.telegram.org/*' => Http::response(['ok' => true])]);

    $studio = makeStudio();
    $studio['staff']->update(['telegram_connect_token' => 'stafftoken123']);

    $this->postJson('/telegram/webhook', [
        'message' => [
            'text' => '/start stafftoken123',
            'chat' => ['id' => 334455],
        ],
    ], [
        'X-Telegram-Bot-Api-Secret-Token' => 'hook-secret',
    ])->assertOk();

    expect($studio['staff']->fresh()->telegram_chat_id)->toBe('334455');
});

test('owner can disconnect telegram', function () {
    $studio = makeStudio();
    $studio['tenant']->update(['telegram_chat_id' => '123', 'telegram_connect_token' => 'keep']);

    $this->actingAs($studio['owner'])
        ->post(route('telegram.disconnect'))
        ->assertRedirect();

    $studio['tenant']->refresh();
    expect($studio['tenant']->telegram_chat_id)->toBeNull()
        ->and($studio['tenant']->telegram_connect_token)->toBeNull();
});

test('new and cancelled bookings notify every linked telegram chat', function () {
    enableTelegramBot();
    Http::fake(['https://api.telegram.org/*' => Http::response(['ok' => true])]);

    $studio = makeStudio();
    $studio['tenant']->update(['telegram_chat_id' => '10']);
    $studio['staff']->update(['telegram_chat_id' => '20']);

    $booking = Booking::factory()->create([
        'tenant_id' => $studio['tenant']->id,
        'service_id' => $studio['service']->id,
        'staff_member_id' => $studio['staff']->id,
        'locale' => 'en',
    ]);
    $booking->load(['client', 'service', 'staffMember', 'tenant']);

    $notifications = app(NotificationService::class);
    $notifications->bookingCreated($booking);
    $notifications->bookingCancelled($booking);

    Http::assertSentCount(4);
});

test('webhook token can be used only once', function () {
    enableTelegramBot();
    Http::fake(['https://api.telegram.org/*' => Http::response(['ok' => true])]);

    $studio = makeStudio();
    $studio['tenant']->update(['telegram_connect_token' => 'biztoken123']);

    $payload = [
        'message' => [
            'text' => '/start biztoken123',
            'chat' => ['id' => 991122],
        ],
    ];
    $headers = ['X-Telegram-Bot-Api-Secret-Token' => 'hook-secret'];

    $this->postJson('/telegram/webhook', $payload, $headers)->assertOk();
    $this->postJson('/telegram/webhook', [
        'message' => [
            'text' => '/start biztoken123',
            'chat' => ['id' => 777],
        ],
    ], $headers)->assertOk();

    expect($studio['tenant']->fresh()->telegram_chat_id)->toBe('991122');
});

test('telegram set-webhook command registers the url', function () {
    enableTelegramBot();
    Http::fake(['https://api.telegram.org/*' => Http::response(['ok' => true, 'result' => true])]);

    $this->artisan('telegram:set-webhook')->assertSuccessful();

    Http::assertSent(fn ($request) => str_contains($request->url(), 'setWebhook')
        && $request['url'] === 'http://localhost/telegram/webhook'
        && $request['secret_token'] === 'hook-secret');
});
