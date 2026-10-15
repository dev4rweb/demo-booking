<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\ManageBookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TelegramSettingsController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/how-to', [GuideController::class, 'index'])->name('guide');

Route::get('/b/{slug}', [PublicBookingController::class, 'show'])->name('booking.show');
Route::get('/b/{slug}/slots', [PublicBookingController::class, 'slots'])->name('booking.slots');
Route::post('/b/{slug}', [PublicBookingController::class, 'store'])->name('booking.store');
Route::get('/b/{slug}/success/{booking}', [PublicBookingController::class, 'success'])->name('booking.success');

Route::get('/bookings/manage/{booking}', [ManageBookingController::class, 'show'])
    ->middleware('signed')
    ->name('bookings.manage');
Route::get('/bookings/manage/{booking}/slots', [ManageBookingController::class, 'slots'])->name('bookings.manage.slots');
Route::post('/bookings/manage/{booking}/cancel', [ManageBookingController::class, 'cancel'])->name('bookings.manage.cancel');
Route::post('/bookings/manage/{booking}/reschedule', [ManageBookingController::class, 'reschedule'])->name('bookings.manage.reschedule');

Route::middleware(['auth'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');

    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');

    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::patch('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::patch('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');

    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/checkout', [BillingController::class, 'checkout'])->name('billing.checkout');

    Route::get('/telegram', [TelegramSettingsController::class, 'index'])->name('telegram.index');
    Route::post('/telegram/connect', [TelegramSettingsController::class, 'connect'])->name('telegram.connect');
    Route::post('/telegram/disconnect', [TelegramSettingsController::class, 'disconnect'])->name('telegram.disconnect');

    Route::get('/admin/tenants', [TenantController::class, 'index'])->name('admin.tenants');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
