<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public routes
Route::post('/user/login', [AuthController::class, 'userLogin']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);

// User routes
Route::prefix('user')->middleware(['auth:sanctum'])->group(function () {
//    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Admin routes
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
//    Route::get('/dashboard', [AdminController::class, 'dashboard']);

    Route::apiResource('admins', AdminController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
});
