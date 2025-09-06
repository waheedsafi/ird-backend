
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\app\director\DirectorController;


Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,organization:api'])->group(function () {
  Route::get('/organizations/director/{id}', [DirectorController::class, 'edit'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_director_information->value . ',' . 'view']);
  Route::get('/organizations/directors/{id}', [DirectorController::class, 'index'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_director_information->value . ',' . 'view']);
  Route::get('/organization/directors/name/{organization_id}', [DirectorController::class, 'organizationDirectorsName'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_director_information->value . ',' . 'view']);
  Route::put('/organizations/director', [DirectorController::class, 'update'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_director_information->value . ',' . 'edit']);
  Route::post('/organizations/director', [DirectorController::class, 'store'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_director_information->value . ',' . 'add']);
});
