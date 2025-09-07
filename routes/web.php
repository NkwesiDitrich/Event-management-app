<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\RegistrationManagementController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes (assuming Laravel Breeze/UI is used)
Auth::routes();

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Registration management routes
    Route::prefix('registrations')->name('registrations.')->group(function () {
        Route::get('/', [RegistrationManagementController::class, 'index'])->name('index');
        Route::post('/{eventId}/unregister', [RegistrationManagementController::class, 'unregister'])->name('unregister');
    });
    
    // Profile routes
    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile');
    
    // Event routes (assuming these exist)
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', 'EventController@index')->name('index');
        Route::get('/create', 'EventController@create')->name('create');
        Route::post('/', 'EventController@store')->name('store');
        Route::get('/{event}', 'EventController@show')->name('show');
        Route::get('/{event}/edit', 'EventController@edit')->name('edit');
        Route::put('/{event}', 'EventController@update')->name('update');
        Route::delete('/{event}', 'EventController@destroy')->name('destroy');
        Route::post('/{event}/register', 'EventController@register')->name('register');
        Route::post('/{event}/unregister', 'EventController@unregister')->name('unregister');
        Route::post('/{event}/publish', 'EventController@publish')->name('publish');
        Route::get('/{event}/attendees', 'EventController@attendees')->name('attendees');
        Route::get('/my-events', 'EventController@myEvents')->name('my-events');
    });
    
    // Admin routes (require admin role)
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Admin user management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::put('/{userId}/role', [AdminUserController::class, 'updateRole'])->name('role');
        });
        
        // Admin event management
        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/', [AdminEventController::class, 'index'])->name('index');
            Route::post('/{eventId}/approve', [AdminEventController::class, 'approve'])->name('approve');
            Route::post('/{eventId}/reject', [AdminEventController::class, 'reject'])->name('reject');
            Route::delete('/{eventId}', [AdminEventController::class, 'delete'])->name('delete');
        });
        
        // Admin reports (placeholder)
        Route::get('/reports', function () {
            return view('admin.reports.index');
        })->name('reports');
    });
    
    // Organizer routes (require organizer role)
    Route::middleware(['organizer'])->group(function () {
        // Organizer-specific routes can be added here
    });
});

// Fallback route for admin users management (alternative naming)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::get('/admin/events', [AdminEventController::class, 'index'])->name('admin.events');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
