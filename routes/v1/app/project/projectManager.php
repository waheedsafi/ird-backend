
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Http\Controllers\v1\app\projects\ProjectManagerController;

Route::prefix('v1')->middleware(["multiAuthorized:" . 'organization:api'])->group(function () {
    Route::get('/project-managers/names/{id}', [ProjectManagerController::class, 'names'])->middleware(["userHasMainPermission:" . PermissionEnum::projects->value . ',' . 'view']);

    // Missing
    Route::get('/project-managers/unique/names/{id}', [ProjectManagerController::class, 'uniqueNames'])->middleware(["userHasMainPermission:" . PermissionEnum::projects->value . ',' . 'view']);
});
