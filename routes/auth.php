<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
| Accessible only by unauthenticated guests.
*/
Route::middleware('guest')->group(function () {
    // Google OAuth 2.0 Social Login
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])
        ->name('auth.google');

    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');

    // Debugging helpers for troubleshooting OAuth callback issues
    Route::get('auth/google/success', [GoogleController::class, 'debugSuccess'])
        ->name('auth.google.debug_success');

    Route::get('auth/google/fail', [GoogleController::class, 'debugFail'])
        ->name('auth.google.debug_fail');

    // Standard Registration
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    // Standard Login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Password Recovery
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated Account Verification & Session Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Email Verification Notice & Verification Links
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Password Confirmation
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Password Update
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Session Termination (Logout)
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
