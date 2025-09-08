
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\template\StatusController;


Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::post('/statuses/user', [StatusController::class, "storeUser"])->middleware(["userHasSubPermission:" . PermissionEnum::users->value . "," . SubPermissionEnum::user_account_status->value . ',' . 'edit']);
    Route::get('/statuses/modify/user/{id}', [StatusController::class, "userIndex"])->middleware(["userHasSubPermission:" . PermissionEnum::users->value . "," . SubPermissionEnum::user_account_status->value . ',' . 'view']);
    Route::get('/statuses/user/{id}', [StatusController::class, "userStatuses"])->middleware(["userHasSubPermission:" . PermissionEnum::users->value . "," . SubPermissionEnum::user_account_status->value . ',' . 'view']);
    Route::get('/statuses/prensentation', [StatusController::class, "presentationStatus"])->middleware(["userHasMainPermission:" . PermissionEnum::schedules->value . ',' . 'add']);

    Route::get('/statuses/modify/organization/{id}', [StatusController::class, "organizationIndex"])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_status->value . ',' . 'view']);
    Route::put('/statuses/modify/organization', [StatusController::class, "storeOrganization"])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_status->value . ',' . 'edit']);
    Route::get('/statuses/organization/{id}', [StatusController::class, 'organizationStatuses'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_status->value . ',' . 'view']);

    Route::get('/statuses/modify/donor/{id}', [StatusController::class, "donorIndex"])->middleware(["userHasSubPermission:" . PermissionEnum::donors->value . "," . SubPermissionEnum::donor_status->value . ',' . 'view']);
    Route::put('/statuses/modify/donor', [StatusController::class, "storeDonor"])->middleware(["userHasSubPermission:" . PermissionEnum::donors->value . "," . SubPermissionEnum::donor_status->value . ',' . 'edit']);
    Route::get('/statuses/donor/{id}', [StatusController::class, 'donorStatuses'])->middleware(["userHasSubPermission:" . PermissionEnum::donors->value . "," . SubPermissionEnum::donor_status->value . ',' . 'view']);
});
