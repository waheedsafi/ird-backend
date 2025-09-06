
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\template\UserController;


Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/users/statistics', [UserController::class, "userStatistics"])->middleware(["userHasMainPermission:" . PermissionEnum::users->value . ',' . 'view']);

    Route::put('/users', [UserController::class, 'update'])->middleware(["userHasSubPermission:" . PermissionEnum::users->value . "," . SubPermissionEnum::user_information->value . ',' . 'edit', 'checkUserAccess']);
    Route::get('/users', [UserController::class, "index"])->middleware(["userHasMainPermission:" . PermissionEnum::users->value . ',' . 'view']);
    Route::post('/users/change-password', [UserController::class, 'changePassword'])->middleware(["userHasSubPermission:" . PermissionEnum::users->value . "," . SubPermissionEnum::user_password->value . ',' . 'edit', 'checkUserAccess']);
    Route::post('/users', [UserController::class, 'store'])->middleware(["userHasMainPermission:" . PermissionEnum::users->value . ',' . 'add']);
    Route::post('/users/change-password', [UserController::class, 'changePassword'])->middleware(["userHasSubPermission:" . PermissionEnum::users->value . "," . SubPermissionEnum::user_password->value . ',' . 'edit', 'checkUserAccess']);

    // These must be here to avoid confusion
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->middleware(["userHasMainPermission:" . PermissionEnum::users->value . ',' . 'delete']);
    Route::get('/users/{id}', [UserController::class, "edit"])->middleware(["userHasMainPermission:" . PermissionEnum::users->value . ',' . 'view']);
});
