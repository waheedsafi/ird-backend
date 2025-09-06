
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\app\representor\RepresentorController;

Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,organization:api'])->group(function () {
  Route::get('/organizations/representor/{id}', [RepresentorController::class, 'edit'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_representative->value . ',' . 'view']);
  Route::get('/organizations/representors/{id}', [RepresentorController::class, 'index'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_representative->value . ',' . 'view']);
  Route::put('/organizations/representors', [RepresentorController::class, 'update'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_representative->value . ',' . 'edit']);
  Route::post('/organizations/representors', [RepresentorController::class, 'store'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_representative->value . ',' . 'add']);
  Route::get('/organizations/representors/name/{id}', [RepresentorController::class, 'organizationRepresentorsName'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_representative->value . ',' . 'view']);
});
