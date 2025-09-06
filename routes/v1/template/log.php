
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Http\Controllers\v1\template\LogController;




Route::prefix('v1')->middleware("authorized:" . 'user:api')->group(function () {
    Route::get('/logs', [LogController::class, "index"])->middleware(["userHasMainPermission:" . PermissionEnum::logs->value . ',' . 'view']);
    Route::get('/logs/{id}', [LogController::class, "edit"])->middleware(["userHasMainPermission:" . PermissionEnum::logs->value . ',' . 'view']);
});
