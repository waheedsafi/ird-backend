
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\template\JobController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/jobs/{id}', [JobController::class, "edit"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_job->value . ',' . 'view']);
    Route::delete('/jobs/{id}', [JobController::class, "destroy"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_job->value . ',' . 'view']);
    Route::post('/jobs', [JobController::class, "store"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_job->value . ',' . 'add']);
    Route::put('/jobs', [JobController::class, "update"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_job->value . ',' . 'edit']);
});
Route::prefix('v1')->group(function () {
    Route::get('/jobs', [JobController::class, "index"]);
});
