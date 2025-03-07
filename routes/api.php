<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function () {
    Route::post('/pre-register', 'preRegister');
    Route::post('/login', 'login')->name('login');
    Route::post('/change-password', 'changePassword');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', 'logout');
        Route::get('/user', 'getUser');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index_task']);
    Route::get('/tasks/{code}', [TaskController::class, 'show_task']);
    Route::post('/tasks', [TaskController::class, 'store_task']);
    Route::put('/tasks/{code}/{status}', [TaskController::class, 'updateStatus_task']);
    Route::delete('/tasks/{code}', [TaskController::class, 'destroy_task']);
});

