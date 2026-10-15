<?php

use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

$locales = implode('|', config('localization.supported'));

Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])->name('telegram.webhook');

require __DIR__.'/app.php';

Route::prefix('{locale}')
    ->where(['locale' => $locales])
    ->name('locale.')
    ->group(function (): void {
        require __DIR__.'/app.php';
    });
