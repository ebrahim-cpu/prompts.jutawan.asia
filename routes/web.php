<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\PromptController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisitorLogController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\UserAccessLogController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Access Logs (Admin sees all; User sees own)
    Route::get('/user-access-logs', [UserAccessLogController::class, 'index'])->name('user_access_logs.index');
    Route::delete('/user-access-logs/clear', [UserAccessLogController::class, 'clear'])->name('user_access_logs.clear');

    // Premium Upgrade
    Route::post('/pricing/checkout', [PricingController::class, 'checkout'])->name('pricing.checkout');
    Route::get('/pricing/success', [PricingController::class, 'success'])->name('pricing.success');
});

// Admin Routes
Route::middleware(['auth', App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('prompts/export', [PromptController::class, 'export'])->name('prompts.export');
    Route::patch('prompts/{prompt}/toggle-status', [PromptController::class, 'toggleStatus'])->name('prompts.toggle_status');
    Route::resource('prompts', PromptController::class);
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('tags', TagController::class)->except(['create', 'show', 'edit']);

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Visitor Logs
    Route::get('visitors', [VisitorLogController::class, 'index'])->name('visitors.index');
    Route::delete('visitors/clear', [VisitorLogController::class, 'clear'])->name('visitors.clear');

    // Interactive Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/data', [ReportController::class, 'apiData'])->name('reports.data');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
});

require __DIR__.'/auth.php';
