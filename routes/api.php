<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\OnboardingLinkController;
use App\Http\Controllers\Api\PublicOnboardingController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/onboarding/{token}', [PublicOnboardingController::class, 'show']);
    Route::post('/onboarding/{token}/submit', [PublicOnboardingController::class, 'submit']);
    Route::get('/banners/latest', [BannerController::class, 'latest']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::middleware('role:admin,agent')->group(function (): void {
            Route::get('/customers', [CustomerController::class, 'index']);
            Route::post('/customers', [CustomerController::class, 'store']);
            Route::get('/customers/{customer}', [CustomerController::class, 'show']);

            Route::get('/applications', [ApplicationController::class, 'index']);
            Route::post('/applications', [ApplicationController::class, 'store']);
            Route::post('/applications/{application}/submit', [ApplicationController::class, 'submit']);

            Route::get('/documents', [DocumentController::class, 'index']);
            Route::post('/documents', [DocumentController::class, 'store']);

            Route::post('/onboarding-links', [OnboardingLinkController::class, 'store']);
            Route::get('/reports/agent-summary', [ReportController::class, 'agentSummary']);
            Route::get('/reports/product-wise', [ReportController::class, 'productWiseSummary']);
            Route::get('/reports/conversion-metrics', [ReportController::class, 'conversionMetrics']);
            Route::get('/reports/agent-performance', [ReportController::class, 'agentPerformance']);

            // Message routes
            Route::post('/messages/send', [MessageController::class, 'send']);
            Route::get('/messages/received', [MessageController::class, 'getReceivedMessages']);
            Route::get('/messages/sent', [MessageController::class, 'getSentMessages']);
            Route::get('/messages/conversation/{userId}', [MessageController::class, 'getConversation']);
            Route::post('/messages/{message}/read', [MessageController::class, 'markAsRead']);
            Route::get('/messages/unread/count', [MessageController::class, 'getUnreadCount']);
            Route::get('/messages/unread', [MessageController::class, 'getUnreadMessages']);
            Route::get('/agents', [MessageController::class, 'getAgents']);
        });

        Route::middleware('role:admin')->group(function (): void {
            Route::post('/documents/{document}/review', [DocumentController::class, 'review']);
            Route::post('/applications/{application}/verify', [ApplicationController::class, 'verify']);
            Route::post('/applications/{application}/convert', [ApplicationController::class, 'convert']);
        });
    });
});
