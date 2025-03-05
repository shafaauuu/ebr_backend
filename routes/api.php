<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/pre-register', [UserController::class, 'preRegister']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);
Route::post('/change-password', [UserController::class, 'changePassword']);

Route::get('/user', [UserController::class, 'getUser'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/{code}', [TaskController::class, 'show']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{code}/{status}', [TaskController::class, 'updateStatus']);
    Route::delete('/tasks/{code}', [TaskController::class, 'destroy']);
});

