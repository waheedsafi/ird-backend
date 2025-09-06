
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\template\RoleController;


Route::prefix('v1')->middleware(["authorized:" . 'user:api', 'allowSuperOnly'])->group(function () {
    Route::get('/roles/by-role/{id}', [RoleController::class, "indexByRole"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_role->value . ',' . 'view']);
    Route::get('/roles/assignments', [RoleController::class, "rolesAssignments"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_role->value . ',' . 'view']);
    Route::get('/roles/{id}', [RoleController::class, "edit"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_role->value . ',' . 'view']);
    Route::get('/roles/new/role-permissions', [RoleController::class, "newRolePermissions"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_role->value . ',' . 'view']);
    Route::delete('/roles/{id}', [RoleController::class, "destroy"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_role->value . ',' . 'view']);
    Route::post('/roles', [RoleController::class, "store"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_role->value . ',' . 'add']);
    Route::put('/roles', [RoleController::class, "update"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_role->value . ',' . 'edit']);
    Route::get('/roles', [RoleController::class, "index"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_role->value . ',' . 'view']);
});
Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('roles/by/user', [RoleController::class, 'indexByUser']);
});
