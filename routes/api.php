<?php

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

// Basic API info endpoint
Route::get('/', function () {
    return response()->json([
        'message' => 'Event Management API',
        'version' => 'v1',
        'status' => 'active',
        'endpoints' => [
            'GET /api/v1/info' => 'API information',
            'GET /api/user' => 'Get authenticated user (requires auth:sanctum)'
        ]
    ]);
});

// API version 1
Route::prefix('v1')->group(function () {
    
    // API info endpoint
    Route::get('/info', function () {
        return response()->json([
            'api_version' => '1.0',
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'timezone' => config('app.timezone'),
            'environment' => app()->environment()
        ]);
    });
    
    // Future API endpoints can be added here when API controllers are created
    // Example structure:
    // Route::post('/register', [AuthController::class, 'register']);
    // Route::post('/login', [AuthController::class, 'login']);
    // Route::get('/events', [ApiEventController::class, 'index']);
    
});

// Protected API Routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // Get authenticated user
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
            'authenticated' => true
        ]);
    });
    
    // Future authenticated endpoints can be added here
    
});

// Fallback API route
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found. Please check the documentation.',
        'version' => 'v1',
        'base_url' => url('/api/v1'),
        'available_endpoints' => [
            'GET /api/' => 'API root information',
            'GET /api/v1/info' => 'API version information',
            'GET /api/user' => 'Get authenticated user (requires auth:sanctum)'
        ]
    ], 404);
});
