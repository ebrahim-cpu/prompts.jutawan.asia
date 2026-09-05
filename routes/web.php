<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\UserAccessLogController;
use App\Http\Controllers\Admin\PromptController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VisitorLogController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Middleware\AdminMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes — Public & User Catalog
|--------------------------------------------------------------------------
| Public storefront routes for browsing prompts, viewing detail modals,
| reviewing subscription plans, and accessing user dashboards.
*/

// Homepage storefront catalog with search and filters
Route::get('/', [HomeController::class, 'index'])->name('home');

// Prompt detail view
Route::get('/prompts/{prompt}', [HomeController::class, 'show'])->name('prompts.show');

// Subscription plans and pricing comparison table
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');

// Authenticated user dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated User Profile & Billing Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Profile settings and avatar management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Self-service User Access Logs (Admin sees all; User sees own)
    Route::get('/user-access-logs', [UserAccessLogController::class, 'index'])->name('user_access_logs.index');
    Route::delete('/user-access-logs/clear', [UserAccessLogController::class, 'clear'])->name('user_access_logs.clear');

    // Stripe Checkout Initiation & Callback
    Route::post('/pricing/checkout', [PricingController::class, 'checkout'])->name('pricing.checkout');
    Route::get('/pricing/success', [PricingController::class, 'success'])->name('pricing.success');

    // Exit Impersonation Mode
    Route::post('/impersonate/leave', [UserController::class, 'leaveImpersonation'])->name('impersonate.leave');
});

/*
|--------------------------------------------------------------------------
| Administrator Management Routes (/admin/*)
|--------------------------------------------------------------------------
| Guarded by AdminMiddleware: strictly requires authenticated user with role='admin'.
*/
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    
    // Prompts Management & Catalog Exports
    Route::get('prompts/export', [PromptController::class, 'export'])->name('prompts.export');
    Route::patch('prompts/{prompt}/toggle-status', [PromptController::class, 'toggleStatus'])->name('prompts.toggle_status');
    Route::resource('prompts', PromptController::class);

    // Taxonomies (Categories & Tags)
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('tags', TagController::class)->except(['create', 'show', 'edit']);

    // User Administration & Impersonation
    Route::post('users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Anonymous Visitor Traffic Logs
    Route::get('visitors', [VisitorLogController::class, 'index'])->name('visitors.index');
    Route::delete('visitors/clear', [VisitorLogController::class, 'clear'])->name('visitors.clear');

    // Interactive Analytics & Chart.js Data Feeds
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/data', [ReportController::class, 'apiData'])->name('reports.data');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Breeze & Socialite)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
