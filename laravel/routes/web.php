<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventAttachmentController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\MembershipFeeController;
use App\Http\Controllers\Admin\TextBlockController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/zasady-cookies', [CookieConsentController::class, 'policy'])->name('cookies.policy');
Route::post('/cookies/suhlas', [CookieConsentController::class, 'store'])->middleware('throttle:30,1')->name('cookies.consent');

// Administration, login, password and profile pages all live under a secret path from
// .env (ADMIN_PATH, config/admin.php), so the usual /admin and /login return 404.
Route::prefix(config('admin.path'))->group(function () {
    // Breeze redirects here after login; the admin dashboard is the only "home".
    Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))
        ->middleware(['auth'])
        ->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::name('admin.')
        ->middleware(['auth', 'admin'])
        ->group(function () {
            Route::get('/', DashboardController::class)->name('dashboard');

            Route::resource('events', EventController::class)->except('show');
            Route::post('events/{event}/attachments', [EventAttachmentController::class, 'store'])->name('events.attachments.store');
            Route::patch('attachments/{attachment}', [EventAttachmentController::class, 'update'])->name('attachments.update');
            Route::delete('attachments/{attachment}', [EventAttachmentController::class, 'destroy'])->name('attachments.destroy');

            Route::resource('articles', ArticleController::class)->except('show');

            Route::resource('text-blocks', TextBlockController::class)->only(['index', 'edit', 'update'])->parameters(['text-blocks' => 'textBlock']);
            Route::resource('membership-fees', MembershipFeeController::class)->except('show')->parameters(['membership-fees' => 'membershipFee']);
            Route::resource('links', LinkController::class)->except('show');

            // Managing admins asks for the password again (auth.password_timeout).
            Route::middleware('password.confirm')->controller(AdminUserController::class)->group(function () {
                Route::get('users', 'index')->name('users.index');
                Route::get('users/create', 'create')->name('users.create');
                Route::post('users', 'store')->middleware('throttle:10,1')->name('users.store');
                Route::post('users/{user}/invitation', 'resendInvitation')->middleware('throttle:6,1')->name('users.invitation.resend');
                Route::delete('users/{user}/invitation', 'cancelInvitation')->name('users.invitation.cancel');
                Route::patch('users/{user}/deactivate', 'deactivate')->name('users.deactivate');
                Route::patch('users/{user}/reactivate', 'reactivate')->name('users.reactivate');
            });
        });

    require __DIR__.'/auth.php';
});
