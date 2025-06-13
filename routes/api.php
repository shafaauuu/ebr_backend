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

use App\Http\Controllers\FormC\FormCAssySyringeController;
use App\Http\Controllers\FormC\FormCBlisterController;
use App\Http\Controllers\FormC\FormCInjectionController;
use App\Http\Controllers\FormC\FormCNeedleAssyController;

use App\Http\Controllers\FormF\FormFAssySyringeController;
use App\Http\Controllers\FormG\FormGAssySyringeController;
use App\Http\Controllers\FormG\FormGBlisterController;
use App\Http\Controllers\FormG\FormGInjectionController;
use App\Http\Controllers\FormG\FormGNeedleAssyController;

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

    Route::get('/machines/by-brm/{brm}', [MachineController::class, 'getMachinesByBrm']);

    Route::get('/form-c-assy-syringe', [FormCAssySyringeController::class, 'index']);
    Route::get('/form-c-assy-syringe/{id}', [FormCAssySyringeController::class, 'show']);
    Route::post('/form-c-assy-syringe', [FormCAssySyringeController::class, 'store']);
    Route::put('/form-c-assy-syringe/{id}', [FormCAssySyringeController::class, 'update']);
    Route::delete('/form-c-assy-syringe/material/{id}', [FormCAssySyringeController::class, 'deleteMaterial']);
    Route::get('/tasks/{taskId}/materials-assy-syringe/{matId}', [FormCAssySyringeController::class, 'getMaterialsByMatId']);
    Route::get('/tasks/{taskId}/child-materials-assy-syringe/{materialCode}', [FormCAssySyringeController::class, 'getChildMaterials']);

    Route::get('/form-c-blister', [FormCBlisterController::class, 'index']);
    Route::post('/form-c-blister', [FormCBlisterController::class, 'store']);
    Route::get('/form-c-blister/{id}', [FormCBlisterController::class, 'show']);
    Route::put('/form-c-blister/{id}', [FormCBlisterController::class, 'update']);
    Route::delete('/form-c-blister/{id}', [FormCBlisterController::class, 'deleteMaterial']);
    Route::get('/tasks/{taskId}/materials-blister/{matId}', [FormCBlisterController::class, 'getMaterialsByMatId']);
    Route::get('/tasks/{taskId}/child-materials-blister/{materialCode}', [FormCBlisterController::class, 'getChildMaterials']);

    Route::get('/form-c-injection', [FormCInjectionController::class, 'index']);
    Route::post('/form-c-injection', [FormCInjectionController::class, 'store']);
    Route::get('/form-c-injection/{id}', [FormCInjectionController::class, 'show']);
    Route::put('/form-c-injection/{id}', [FormCInjectionController::class, 'update']);
    Route::delete('/form-c-injection/{id}', [FormCInjectionController::class, 'deleteMaterial']);
    Route::get('/tasks/{taskId}/materials-injection/{matId}', [FormCInjectionController::class, 'getMaterialsByMatId']);
    Route::get('/tasks/{taskId}/child-materials-injection/{materialCode}', [FormCInjectionController::class, 'getChildMaterials']);

    Route::get('/form-c-needle-assy', [FormCNeedleAssyController::class, 'index']);
    Route::post('/form-c-needle-assy', [FormCNeedleAssyController::class, 'store']);
    Route::get('/form-c-needle-assy/{id}', [FormCNeedleAssyController::class, 'show']);
    Route::put('/form-c-needle-assy/{id}', [FormCNeedleAssyController::class, 'update']);
    Route::delete('/form-c-needle-assy/{id}', [FormCNeedleAssyController::class, 'deleteMaterial']);
    Route::get('/tasks/{taskId}/materials-needle-assy/{matId}', [FormCNeedleAssyController::class, 'getMaterialsByMatId']);
    Route::get('/tasks/{taskId}/child-materials-needle-assy/{materialCode}', [FormCNeedleAssyController::class, 'getChildMaterials']);

    Route::post('/form-e-assy-syringe', [FormEAssySyringeController::class, 'store']);
    Route::get('/form-e-assy-syringe/{id}', [FormEAssySyringeController::class, 'show']);
    Route::post('/form-e-blister', [FormEBlisterController::class, 'store']);
    Route::get('/form-e-blister/{id}', [FormEBlisterController::class, 'show']);
    Route::post('/form-e-injection', [FormEInjectionController::class, 'store']);
    Route::get('/form-e-injection/{id}', [FormEInjectionController::class, 'show']);
    Route::post('/form-e-needle-assy', [FormENeedleAssyController::class, 'store']);
    Route::get('/form-e-needle-assy/{id}', [FormENeedleAssyController::class, 'show']);

    Route::get('/form-f-assy-syringe', [FormFAssySyringeController::class, 'index']);
    Route::post('/form-f-assy-syringe', [FormFAssySyringeController::class, 'store']);
    Route::get('/form-f-assy-syringe/{id}', [FormFAssySyringeController::class, 'show']);
    Route::put('/form-f-assy-syringe/{id}', [FormFAssySyringeController::class, 'update']);
    Route::delete('/form-f-assy-syringe/{id}', [FormFAssySyringeController::class, 'destroy']);

    Route::get('/form-g-assy-syringe', [FormGAssySyringeController::class, 'index']);
    Route::post('/form-g-assy-syringe', [FormGAssySyringeController::class, 'store']);
    Route::get('/form-g-assy-syringe/{id}', [FormGAssySyringeController::class, 'show']);
    Route::put('/form-g-assy-syringe/{id}', [FormGAssySyringeController::class, 'update']);
    Route::delete('/form-g-assy-syringe/{id}', [FormGAssySyringeController::class, 'destroy']);

    Route::get('/form-g-blister', [FormGBlisterController::class, 'index']);
    Route::post('/form-g-blister', [FormGBlisterController::class, 'store']);
    Route::get('/form-g-blister/{id}', [FormGBlisterController::class, 'show']);
    Route::put('/form-g-blister/{id}', [FormGBlisterController::class, 'update']);
    Route::delete('/form-g-blister/{id}', [FormGBlisterController::class, 'destroy']);

    Route::get('/form-g-injection', [FormGInjectionController::class, 'index']);
    Route::post('/form-g-injection', [FormGInjectionController::class, 'store']);
    Route::get('/form-g-injection/{id}', [FormGInjectionController::class, 'show']);
    Route::put('/form-g-injection/{id}', [FormGInjectionController::class, 'update']);
    Route::delete('/form-g-injection/{id}', [FormGInjectionController::class, 'destroy']);

    Route::get('/form-g-needle-assy', [FormGNeedleAssyController::class, 'index']);
    Route::post('/form-g-needle-assy', [FormGNeedleAssyController::class, 'store']);
    Route::get('/form-g-needle-assy/{id}', [FormGNeedleAssyController::class, 'show']);
    Route::put('/form-g-needle-assy/{id}', [FormGNeedleAssyController::class, 'update']);
    Route::delete('/form-g-needle-assy/{id}', [FormGNeedleAssyController::class, 'destroy']);
});
