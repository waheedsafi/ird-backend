
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\app\projects\EditProjectController;
use App\Http\Controllers\v1\app\projects\ViewProjectController;
use App\Http\Controllers\v1\app\projects\StoreProjectController;
use App\Http\Controllers\v1\app\projects\DeleteProjectController;

Route::prefix('v1')->middleware(["multiAuthorized:" . 'organization:api,user:api'])->group(function () {
    Route::post('/projects', [StoreProjectController::class, 'store'])->middleware(["userHasMainPermission:" . PermissionEnum::projects->value . ',' . 'add']);

    Route::get('/projects/register-form/{id}', [ViewProjectController::class, 'startRegisterForm'])->middleware(["userHasMainPermission:" . PermissionEnum::projects->value . ',' . 'add']);
    Route::get('/projects/header-info/{id}', [ViewProjectController::class, 'headerInfo'])->middleware(["userHasSubPermission:" . PermissionEnum::projects->value . "," . SubPermissionEnum::project_detail->value . ',' . 'view']);

    Route::get('/projects/details/{id}', [ViewProjectController::class, 'details'])->middleware(["userHasSubPermission:" . PermissionEnum::projects->value . "," . SubPermissionEnum::project_detail->value . ',' . 'view']);
    Route::put('/projects/details', [EditProjectController::class, 'details'])->middleware(["userHasSubPermission:" . PermissionEnum::projects->value . "," . SubPermissionEnum::project_detail->value . ',' . 'edit']);

    Route::get('/projects/budget/{id}', [ViewProjectController::class, 'budget'])->middleware(["userHasSubPermission:" . PermissionEnum::projects->value . "," . SubPermissionEnum::project_center_budget->value . ',' . 'view']);
    // Missing
    Route::put('/projects/budget', [EditProjectController::class, 'budget'])->middleware(["userHasSubPermission:" . PermissionEnum::projects->value . "," . SubPermissionEnum::project_center_budget->value . ',' . 'edit']);

    Route::get('/projects/structure/{id}', [ViewProjectController::class, 'structure'])->middleware(["userHasSubPermission:" . PermissionEnum::projects->value . "," . SubPermissionEnum::project_organization_structure->value . ',' . 'view']);
    // Missing
    Route::put('/projects/structure', [EditProjectController::class, 'structure'])->middleware(["userHasSubPermission:" . PermissionEnum::projects->value . "," . SubPermissionEnum::project_organization_structure->value . ',' . 'edit']);


    Route::post('/projects/pending-task/{id}', [DeleteProjectController::class, 'destroyPendingTask'])->middleware(["userHasMainPermission:" . PermissionEnum::projects->value . ',' . 'delete']);
    Route::get('/projects/statistics', [ViewProjectController::class, "projectStatistics"])->middleware(["userHasMainPermission:" . PermissionEnum::projects->value . ',' . 'view']);

    Route::get('/projects/checklists/{id}', [ViewProjectController::class, 'checklists'])->middleware(["userHasSubPermission:" . PermissionEnum::projects->value . "," . SubPermissionEnum::project_checklist->value . ',' . 'view']);

    Route::post('/projects/signed/mou', [StoreProjectController::class, 'StoreSignedMou'])->middleware(["userHasMainPermission:" . PermissionEnum::projects->value . ',' . 'add']);
});

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/projects', [ViewProjectController::class, 'index'])->middleware(["userHasMainPermission:" . PermissionEnum::projects->value . ',' . 'view']);

    Route::get('/projects/with/name', [ViewProjectController::class, "projectsWithName"]);
});
Route::prefix('v1')->middleware(["authorized:" . 'organization:api'])->group(function () {
    Route::get('/projects-org', [ViewProjectController::class, 'orgIndex'])->middleware(["userHasMainPermission:" . PermissionEnum::projects->value . ',' . 'view']);
});
