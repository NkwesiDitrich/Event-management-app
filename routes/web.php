<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public Routes
Route::get('/', function () {
    return redirect()->route('events.index');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [UserController::class, 'register']);
    
    Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Event Management Routes
    Route::prefix('events')->group(function () {
        // Event Creation
        Route::get('/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/store', [EventController::class, 'store'])->name('events.store');
        
        // Organizer's Events
        Route::get('/my-events', [EventController::class, 'myEvents'])->name('events.my-events');
        
        // Event Management (for organizers/admins)
        Route::middleware('can:organizer')->group(function () {
            Route::get('/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
            Route::put('/{id}', [EventController::class, 'update'])->name('events.update');
            Route::delete('/{id}', [EventController::class, 'destroy'])->name('events.destroy');
            Route::post('/{id}/publish', [EventController::class, 'publish'])->name('events.publish');
            Route::post('/{id}/cancel', [EventController::class, 'cancel'])->name('events.cancel');
            
            // Attendee Management
            Route::get('/{id}/attendees', [EventController::class, 'attendees'])->name('events.attendees');
        });
    });
    
    // Registration Routes (AJAX)
    Route::prefix('events')->group(function () {
        Route::post('/{eventId}/register', [RegistrationController::class, 'register'])->name('events.register');
        Route::post('/{eventId}/unregister', [RegistrationController::class, 'unregister'])->name('events.unregister');
    });
});

// Public Event Routes (accessible to all)
Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('events.index');
    Route::get('/{id}', [EventController::class, 'show'])->name('events.show');
});

// Admin Routes (if needed in future)
Route::middleware(['auth', 'can:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users');
    Route::get('/events', [\App\Http\Controllers\Admin\EventController::class, 'index'])->name('admin.events');
    Route::get('/stats', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
});