<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventAttachmentController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\MembershipFeeController;
use App\Http\Controllers\Admin\TextBlockController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Breeze redirects here after login; the admin dashboard is the only "home".
Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('events', EventController::class)->except('show');
        Route::post('events/{event}/attachments', [EventAttachmentController::class, 'store'])->name('events.attachments.store');
        Route::patch('attachments/{attachment}', [EventAttachmentController::class, 'update'])->name('attachments.update');
        Route::delete('attachments/{attachment}', [EventAttachmentController::class, 'destroy'])->name('attachments.destroy');

        Route::resource('text-blocks', TextBlockController::class)->only(['index', 'edit', 'update'])->parameters(['text-blocks' => 'textBlock']);
        Route::resource('membership-fees', MembershipFeeController::class)->except('show')->parameters(['membership-fees' => 'membershipFee']);
        Route::resource('links', LinkController::class)->except('show');
    });

require __DIR__.'/auth.php';
