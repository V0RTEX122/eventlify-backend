<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\TaskController;

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

// Public Routes (no authentication required)
Route::post('register', [UserController::class, 'register']); // Register a new user
Route::post('login', [UserController::class, 'login']);       // User login

// Authenticated Routes (requires sanctum authentication)
Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | User Account Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')->group(function () {
        Route::post('logout', [UserController::class, 'logout']);                // Logout current device
        Route::post('logout/all', [UserController::class, 'logoutAllDevices']);  // Logout from all devices
        Route::get('/', [UserController::class, 'getUser']);                     // Get logged-in user's information
        Route::put('/', [UserController::class, 'updateUser']);                  // Update logged-in user's information

        /*
        |----------------------------------------------------------------------
        | User-Specific Event Management
        |----------------------------------------------------------------------
        */
        Route::get('event/{id}', [UserController::class, 'getEventForUser']);    // Get a specific event for the user
        Route::get('events', [UserController::class, 'getAllEventsForUser']);    // Get all events for the user
        Route::post('event/{eventId}/task', [TaskController::class, 'createEventTask']);    // Create event task for the user
        Route::get('event/{eventId}/task', [TaskController::class, 'getEventTasks']);    // Get all event task for the user
        Route::get('event/{eventId}/task/{taskId}', [TaskController::class, 'getEventTaskById']);    // Get a specific event task for the user
        Route::patch('event/{eventId}/task/{taskId}', [TaskController::class, 'updateEventTask']);    // Update a specific event task for the user

        /*
        |----------------------------------------------------------------------
        | User Search and Invitations
        |----------------------------------------------------------------------
        */
        Route::get('search', [UserController::class, 'searchUsers']);            // Search users
        Route::post('events/{eventId}/invitations', [InvitationController::class, 'sendInvitation']); // Send event invitation
        Route::get('invitations', [InvitationController::class, 'getUserInvitations']); // Get user's invitations
        Route::patch('invitations/{invitationId}/action', [InvitationController::class, 'handleUserInvitation']); // Accept or decline invitation
    });

    /*
    |--------------------------------------------------------------------------
    | General Event Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('events')->group(function () {
        Route::post('/', [EventController::class, 'storeEvent']);        // Create a new event
        Route::get('/', [EventController::class, 'getAllEvents']);       // List all events
        Route::get('{id}', [EventController::class, 'getEvent']);        // Get specific event details
        Route::put('{id}', [EventController::class, 'updateEvent']);     // Update a specific event
        Route::delete('{id}', [EventController::class, 'deleteEvent']);  // Delete a specific event
    });
});
