<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\AgentDashboardController;
use App\Http\Controllers\Web\AgentManagementController;
use App\Http\Controllers\Web\CustomerDashboardController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\CustomerManagementController;
use App\Http\Controllers\Web\ApplicationManagementController;
use App\Http\Controllers\Web\DocumentManagementController;
use App\Http\Controllers\Web\WebReportController;
use App\Http\Controllers\Web\AuditLogController;
use App\Http\Controllers\Web\UserManagementController;
use App\Http\Controllers\Web\CategoryManagementController;
use App\Http\Controllers\Web\ProductManagementController;
use App\Http\Controllers\Web\MessageManagementController;
use App\Http\Controllers\Web\GeoController;
use App\Http\Controllers\Web\OcrController;
use App\Http\Controllers\Web\VideoController;
use App\Http\Controllers\Web\AgentVideoController;
use App\Http\Controllers\Web\CustomerVideoController;
use App\Http\Controllers\Web\BannerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->role === 'agent') {
            return redirect()->route('agent.dashboard');
        } else {
            return redirect()->route('customer.dashboard');
        }
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Agent Dashboard Routes
Route::middleware(['auth', 'role:agent'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/videos', [AgentVideoController::class, 'index'])->name('videos.index');
    Route::post('/videos/{video}/share', [AgentVideoController::class, 'share'])->name('videos.share');
});

// Customer Dashboard Routes
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/videos', [CustomerVideoController::class, 'index'])->name('videos.index');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Geo AJAX endpoints for searchable cascading dropdowns
    Route::get('/geo/countries',  [GeoController::class, 'countries'])->name('geo.countries');
    Route::get('/geo/states',     [GeoController::class, 'states'])->name('geo.states');
    Route::get('/geo/cities',     [GeoController::class, 'cities'])->name('geo.cities');
    Route::get('/geo/find',       [GeoController::class, 'findByName'])->name('geo.find');

    // OCR – Aadhaar address extraction
    Route::post('/ocr/aadhaar', [OcrController::class, 'extractAadhaar'])->name('ocr.aadhaar');

    // Agents
    Route::get('/agents', [AgentManagementController::class, 'index'])->name('agents.index');
    Route::get('/agents/create', [AgentManagementController::class, 'create'])->name('agents.create');
    Route::post('/agents', [AgentManagementController::class, 'store'])->name('agents.store');
    Route::get('/agents/{agent}', [AgentManagementController::class, 'show'])->name('agents.show');
    Route::get('/agents/{agent}/edit', [AgentManagementController::class, 'edit'])->name('agents.edit');
    Route::put('/agents/{agent}', [AgentManagementController::class, 'update'])->name('agents.update');
    Route::patch('/agents/{agent}/status', [AgentManagementController::class, 'updateStatus'])->name('agents.update-status');

    Route::delete('/agents/{agent}', [AgentManagementController::class, 'destroy'])->name('agents.destroy');

    // Agent Payouts
    Route::post('/agents/{agent}/payouts', [\App\Http\Controllers\Web\AgentPayoutController::class, 'store'])->name('agents.payouts.store');
    Route::get('/agents/{agent}/payouts/summary', [\App\Http\Controllers\Web\AgentPayoutController::class, 'summary'])->name('agents.payouts.summary');
    Route::get('/payouts/{payout}/download', [\App\Http\Controllers\Web\AgentPayoutController::class, 'downloadSlip'])->name('payouts.download');
    Route::put('/payouts/{payout}', [\App\Http\Controllers\Web\AgentPayoutController::class, 'update'])->name('payouts.update');
    Route::delete('/payouts/{payout}', [\App\Http\Controllers\Web\AgentPayoutController::class, 'destroy'])->name('payouts.destroy');

    // Customers
    Route::get('/customers', [CustomerManagementController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerManagementController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerManagementController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerManagementController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerManagementController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerManagementController::class, 'update'])->name('customers.update');
    Route::patch('/customers/{customer}/rating', [CustomerManagementController::class, 'updateRating'])->name('customers.rating');

    // Applications
    Route::get('/applications', [ApplicationManagementController::class, 'index'])->name('applications.index');
    Route::get('/applications/create', [ApplicationManagementController::class, 'create'])->name('applications.create');
    Route::post('/applications', [ApplicationManagementController::class, 'store'])->name('applications.store');
    Route::get('/applications/{application}', [ApplicationManagementController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/verify', [ApplicationManagementController::class, 'verify'])->name('applications.verify');
    Route::post('/applications/{application}/convert', [ApplicationManagementController::class, 'convert'])->name('applications.convert');

    // Documents
    Route::get('/documents', [DocumentManagementController::class, 'index'])->name('documents.index');
    Route::get('/documents/{document}/review', [DocumentManagementController::class, 'review'])->name('documents.review');
    Route::post('/documents/{document}/approve', [DocumentManagementController::class, 'approve'])->name('documents.approve');
    Route::post('/documents/{document}/reject', [DocumentManagementController::class, 'reject'])->name('documents.reject');

    // Reports
    Route::get('/reports', [WebReportController::class, 'index'])->name('reports');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs');

    // Categories
    Route::get('/categories', [CategoryManagementController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryManagementController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryManagementController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryManagementController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryManagementController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryManagementController::class, 'destroy'])->name('categories.destroy');

    // Products
    Route::get('/products', [ProductManagementController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductManagementController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductManagementController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductManagementController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductManagementController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductManagementController::class, 'destroy'])->name('products.destroy');

    // Users
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.delete');

    // Messages
    Route::get('/messages', [MessageManagementController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [MessageManagementController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/reply', [MessageManagementController::class, 'reply'])->name('messages.reply');

    // Banners
    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::get('/banners/create', [BannerController::class, 'create'])->name('banners.create');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/{banner}/edit', [BannerController::class, 'edit'])->name('banners.edit');
    Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');

    // Settings – Videos
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
        Route::get('/videos/create', [VideoController::class, 'create'])->name('videos.create');
        Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
        Route::get('/videos/{video}/edit', [VideoController::class, 'edit'])->name('videos.edit');
        Route::put('/videos/{video}', [VideoController::class, 'update'])->name('videos.update');
        Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy');
        Route::post('/videos/{video}/share', [VideoController::class, 'share'])->name('videos.share');
    });
});
