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

test('plain start replies with a welcome and demo links', function () {
    enableTelegramBot();
    Http::fake(['https://api.telegram.org/*' => Http::response(['ok' => true])]);

    $this->postJson('/telegram/webhook', [
        'message' => [
            'text' => '/start',
            'chat' => ['id' => 4242],
            'from' => ['language_code' => 'en'],
        ],
    ], [
        'X-Telegram-Bot-Api-Secret-Token' => 'hook-secret',
    ])->assertOk();

    Http::assertSent(function ($request) {
        $markup = json_decode((string) $request['reply_markup'], true);

        return str_contains($request->url(), 'sendMessage')
            && (string) $request['chat_id'] === '4242'
            && str_contains((string) $request['text'], 'demo online booking service')
            && str_contains((string) $request['text'], 'Connect Telegram')
            && str_contains((string) $request['text'], 'confirmations and reminders')
            && ($markup['inline_keyboard'][0][0] ?? null) === [
                'text' => 'Open demo',
                'url' => 'https://demo-booking.dev4rweb.com',
            ]
            && ($markup['inline_keyboard'][0][1] ?? null) === [
                'text' => 'dev4rweb.com',
                'url' => 'https://dev4rweb.com',
            ];
    });
});

test('plain start with a bot mention still sends the welcome', function () {
    enableTelegramBot();
    Http::fake(['https://api.telegram.org/*' => Http::response(['ok' => true])]);

    $this->postJson('/telegram/webhook', [
        'message' => [
            'text' => '/start@dev4rweb_bookly_bot',
            'chat' => ['id' => 11],
        ],
    ], [
        'X-Telegram-Bot-Api-Secret-Token' => 'hook-secret',
    ])->assertOk();

    Http::assertSent(fn ($request) => str_contains($request->url(), 'sendMessage')
        && str_contains((string) $request['text'], 'demo online booking service')
        && isset($request['reply_markup']));
});

test('plain start localizes the welcome from telegram language_code', function (string $code, string $needle) {
    enableTelegramBot();
    Http::fake(['https://api.telegram.org/*' => Http::response(['ok' => true])]);

    $payload = [
        'message' => [
            'text' => '/start',
            'chat' => ['id' => 7],
        ],
    ];

    if ($code !== '') {
        $payload['message']['from'] = ['language_code' => $code];
    }

    $this->postJson('/telegram/webhook', $payload, [
        'X-Telegram-Bot-Api-Secret-Token' => 'hook-secret',
    ])->assertOk();

    Http::assertSent(fn ($request) => str_contains($request->url(), 'sendMessage')
        && str_contains((string) $request['text'], $needle));
})->with([
    ['en', 'demo online booking service'],
    ['zh', '在线预约演示服务'],
    ['zh-hans', '在线预约演示服务'],
    ['fr', 'démo de réservation en ligne'],
    ['es', 'demo de reservas en línea'],
    ['ar', 'خدمة تجريبية للحجز'],
    ['pt', 'serviço demo de reservas'],
    ['pt-BR', 'serviço demo de reservas'],
    ['ru', 'демо-сервис онлайн-записи'],
    ['de', 'demo online booking service'],
    ['', 'demo online booking service'],
]);

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

    Http::assertSent(fn ($request) => str_contains($request->url(), 'sendMessage')
        && str_contains((string) $request['text'], 'Connected to')
        && ! isset($request['reply_markup']));
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
