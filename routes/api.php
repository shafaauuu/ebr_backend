<?php

use App\Http\Controllers\BRMController;

use App\Http\Controllers\FormA\FormAAssySyringeController;
use App\Http\Controllers\FormA\FormABlisterController;
use App\Http\Controllers\FormA\FormAInjectionController;
use App\Http\Controllers\FormA\FormANeedleAssyController;

use App\Http\Controllers\FormB\FormBAssySyringeController;
use App\Http\Controllers\FormB\FormBBlisterController;
use App\Http\Controllers\FormB\FormBInjectionController;
use App\Http\Controllers\FormB\FormBNeedleAssyController;
use App\Http\Controllers\FormE\FormEAssySyringeController;
use App\Http\Controllers\FormE\FormEBlisterController;
use App\Http\Controllers\FormE\FormEInjectionController;
use App\Http\Controllers\FormE\FormENeedleAssyController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MaterialController;

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

Route::get('/brms', [BRMController::class, 'index']);
Route::get('/brms/{brmNo}', [BRMController::class, 'show']);
Route::get('/brms/{brmNo}/materials', [BRMController::class, 'getMaterials']);
Route::get('/brms/{brmNo}/category', [BRMController::class, 'getCategoryByBRM']);

Route::get('/materials/search', [MaterialController::class, 'search']);

Route::post('/logs/store', [LogController::class, 'store']);
Route::get('/logs', [LogController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/form-a-assy-syringe', [FormAAssySyringeController::class, 'store']);
    Route::get('/form-a-assy-syringe/{id}', [FormAAssySyringeController::class, 'show']);
    Route::post('/form-a-blister', [FormABlisterController::class, 'store']);
    Route::get('/form-a-blister/{id}', [FormABlisterController::class, 'show']);
    Route::post('/form-a-injection', [FormAInjectionController::class, 'store']);
    Route::get('/form-a-injection/{id}', [FormAInjectionController::class, 'show']);
    Route::post('/form-a-needle-assy', [FormANeedleAssyController::class, 'store']);
    Route::get('/form-a-needle-assy/{id}', [FormANeedleAssyController::class, 'show']);

    Route::post('/form-b-assy-syringe', [FormBAssySyringeController::class, 'storeQualification']);
    Route::get('/form-b-assy-syringe/{id}', [FormBAssySyringeController::class, 'show']);
    Route::post('/form-b-blister', [FormBBlisterController::class, 'storeQualification']);
    Route::get('/form-b-blister/{id}', [FormBBlisterController::class, 'show']);
    Route::post('/form-b-injection', [FormBInjectionController::class, 'storeQualification']);
    Route::get('/form-b-injection/{id}', [FormBInjectionController::class, 'show']);
    Route::post('/form-b-needle-assy', [FormBNeedleAssyController::class, 'storeQualification']);
    Route::get('/form-b-needle-assy/{id}', [FormBNeedleAssyController::class, 'show']);

    Route::post('/form-e-assy-syringe', [FormEAssySyringeController::class, 'store']);
    Route::get('/form-e-assy-syringe/{id}', [FormEAssySyringeController::class, 'show']);
    Route::post('/form-e-blister', [FormEBlisterController::class, 'store']);
    Route::get('/form-e-blister/{id}', [FormEBlisterController::class, 'show']);
    Route::post('/form-e-injection', [FormEInjectionController::class, 'store']);
    Route::get('/form-e-injection/{id}', [FormEInjectionController::class, 'show']);
    Route::post('/form-e-needle-assy', [FormENeedleAssyController::class, 'store']);
    Route::get('/form-e-needle-assy/{id}', [FormENeedleAssyController::class, 'show']);
});

Route::get('/machines/by-brm/{brm_no}', [MachineController::class, 'getMachinesByBrm']);
