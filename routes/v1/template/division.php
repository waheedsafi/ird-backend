
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\template\DivisionController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/divisions/{id}', [DivisionController::class, "edit"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_division->value . ',' . 'view']);
    Route::delete('/divisions/{id}', [DivisionController::class, "destroy"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_division->value . ',' . 'view']);
    Route::post('/divisions', [DivisionController::class, "store"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_division->value . ',' . 'add']);
    Route::put('/divisions', [DivisionController::class, "update"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_division->value . ',' . 'edit']);
});
Route::prefix('v1')->group(function () {
    Route::get('/divisions', [DivisionController::class, "index"]);
});
