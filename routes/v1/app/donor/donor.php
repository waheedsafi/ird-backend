
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Http\Controllers\v1\app\donor\DonorController;



Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,organization:api,donor:api'])->group(function () {
  Route::get('/donors/names/list', [DonorController::class, 'nameWithId']);

  Route::get('/donors-name', [DonorController::class, 'list'])->middleware(["userHasMainPermission:" . PermissionEnum::donors->value . ',' . 'view']);
});

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  Route::get('/donors', [DonorController::class, 'index'])->middleware(["userHasMainPermission:" . PermissionEnum::donors->value . ',' . 'view']);
  Route::post('/donors', [DonorController::class, 'store'])->middleware(["userHasMainPermission:" . PermissionEnum::donors->value . ',' . 'add']);
  Route::get('/donors/statistics', [DonorController::class, "statistics"])->middleware(["userHasMainPermission:" . PermissionEnum::donors->value . ',' . 'view']);

  Route::get('/donors/{id}', [DonorController::class, 'edit'])->middleware(["userHasMainPermission:" . PermissionEnum::donors->value . ',' . 'view']);
  Route::put('/donors', [DonorController::class, 'update'])->middleware(["userHasMainPermission:" . PermissionEnum::donors->value . ',' . 'edit']);
  Route::post('/donors/change/password', [DonorController::class, 'changePassword']);
});
