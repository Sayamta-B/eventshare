<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\EventController;
use App\Http\Controllers\api\UploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return response()->json([
        'message' => 'API working'
    ]);
});

Route::middleware(['auth:sanctum', 'role:admin,organizer'])->group(function(){
    Route::get('/events', [EventController::class, 'index']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/organizers', [AuthController::class, 'index']);
    Route::delete('/organizers/{id}', [AuthController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'role:organizer'])->group(function(){
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::get('/events/{id}', [EventController::class, 'edit']);

    Route::put('/organizers/{id}', [AuthController::class, 'update']);
});

Route::get('public/events/{slug}', [EventController::class, 'showPublic']);

Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');


Route::post('/events/{slug}/uploads', [UploadController::class, 'upload']);

Route::middleware(['auth:sanctum', 'role:organizer'])->group(function () {
    Route::get('/events/{slug}/uploads', [UploadController::class, 'index']);
    Route::put('/events/{slug}/uploads/{id}', [UploadController::class, 'update']);
    Route::delete('/events/{slug}/uploads/{id}', [UploadController::class, 'destroy']);
});