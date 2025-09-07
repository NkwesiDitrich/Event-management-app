<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController as ApiEventController;
use App\Http\Controllers\Api\RegistrationController as ApiRegistrationController;
use App\Http\Controllers\Api\UserController as ApiUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API Routes
Route::prefix('v1')->group(function () {
    
    // Authentication endpoints
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Public event data
    Route::get('/events', [ApiEventController::class, 'index']);
    Route::get('/events/{id}', [ApiEventController::class, 'show']);
    Route::get('/events/{id}/attendees', [ApiEventController::class, 'attendees']);
});

// Protected API Routes (require authentication)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    
    // User management
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::put('/user/profile', [ApiUserController::class, 'updateProfile']);
    Route::put('/user/password', [ApiUserController::class, 'updatePassword']);
    
    // Event management
    Route::apiResource('events', ApiEventController::class)->except(['index', 'show']);
    Route::post('/events/{event}/publish', [ApiEventController::class, 'publish']);
    Route::post('/events/{event}/cancel', [ApiEventController::class, 'cancel']);
    
    // Registration management
    Route::post('/events/{event}/register', [ApiRegistrationController::class, 'register']);
    Route::delete('/events/{event}/unregister', [ApiRegistrationController::class, 'unregister']);
    Route::get('/my-registrations', [ApiRegistrationController::class, 'myRegistrations']);
    Route::get('/my-events', [ApiEventController::class, 'myEvents']);
    
    // Admin endpoints
    Route::middleware('can:admin')->group(function () {
        Route::apiResource('users', ApiUserController::class);
        Route::get('/stats/overview', [\App\Http\Controllers\Api\Admin\StatsController::class, 'overview']);
        Route::get('/stats/events', [\App\Http\Controllers\Api\Admin\StatsController::class, 'eventStats']);
        Route::get('/stats/users', [\App\Http\Controllers\Api\Admin\StatsController::class, 'userStats']);
    });
});

// Fallback API route
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found. Please check the documentation.',
        'version' => 'v1',
        'base_url' => url('/api/v1')
    ], 404);
});