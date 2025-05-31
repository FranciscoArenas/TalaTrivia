<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TriviaController;
use App\Http\Controllers\TriviaParticipationController;

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

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0',
        'service' => 'TalaTrivia API'
    ]);
});

// Authentication routes (public)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected auth routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
    });
});

// Protected routes that require authentication
Route::middleware('auth:sanctum')->group(function () {

    // User profile route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Admin routes - only accessible by admin users
    Route::middleware('admin')->group(function () {

        // User management (admin only)
        Route::apiResource('users', UserController::class);

        // Question management (admin only)
        Route::apiResource('questions', QuestionController::class);

        // Trivia management (admin only)
        Route::apiResource('trivias', TriviaController::class);

        // Trivia ranking (admin only)
        Route::get('/trivias/{trivia}/ranking', [TriviaController::class, 'ranking']);

        // Assign users to trivia (admin only)
        Route::post('/trivias/{trivia}/assign-users', [TriviaController::class, 'assignUsers']);

    });

    // Player routes - accessible by both admin and player users
    Route::middleware('player')->group(function () {

        // Get available trivias for current user
        Route::get('/my-trivias', [TriviaParticipationController::class, 'getAvailableTrivias']);

        // Start trivia participation
        Route::post('/trivias/{trivia}/start', [TriviaParticipationController::class, 'startTrivia']);

        // Get trivia questions
        Route::get('/trivias/{trivia}/questions', [TriviaParticipationController::class, 'getTriviaQuestions']);

        // Submit answer to trivia question
        Route::post('/trivias/{trivia}/answer', [TriviaParticipationController::class, 'submitAnswer']);

        // Finish trivia participation
        Route::post('/trivias/{trivia}/finish', [TriviaParticipationController::class, 'finishTrivia']);

        // Get user's trivia results
        Route::get('/trivias/{trivia}/results', [TriviaParticipationController::class, 'getTriviaResults']);

        // Get user's participation history
        Route::get('/my-participation-history', [TriviaParticipationController::class, 'getParticipationHistory']);

    });

    // Mixed routes - accessible by both admin and player based on their roles

    // View trivia details (both admin and player can view)
    Route::get('/trivias/{trivia}', [TriviaController::class, 'show']);

    // Get trivia ranking (both admin and player can view)
    Route::get('/trivias/{trivia}/ranking', [TriviaController::class, 'ranking']);

    // List public trivias (both admin and player can view)
    Route::get('/trivias', [TriviaController::class, 'index']);

});
