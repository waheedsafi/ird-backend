
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\app\organization\ViewsOrganizationController;
use App\Http\Controllers\v1\app\organization\EditesOrganizationController;
use App\Http\Controllers\v1\app\organization\ExtendOrganizationController;
use App\Http\Controllers\v1\app\organization\StoresOrganizationController;
use App\Http\Controllers\v1\app\organization\DeletesOrganizationController;
use App\Http\Controllers\v1\app\organization\PublicViewsOrganizationController;

Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,organization:api'])->group(function () {
  Route::get('/organization/start/extend/form/{id}', [ViewsOrganizationController::class, 'startExtendForm']);
  Route::post('/organization/extend/form/complete', [ExtendOrganizationController::class, 'extendOrganizationAgreement']);
  Route::post('/organization/store/signed/register/form', [StoresOrganizationController::class, 'StoreSignedRegisterForm']);
  Route::get('/organization/header-info/{id}', [ViewsOrganizationController::class, 'headerInfo']);
  Route::delete('/organizations/task/content/{id}', [DeletesOrganizationController::class, 'destroyPendingTask'])->middleware(["userHasMainPermission:" . PermissionEnum::organizations->value . ',' . 'add']);

  // Tested
  Route::post('/organizations/change/password', [EditesOrganizationController::class, 'changePassword'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_account_password->value . ',' . 'edit']);

  Route::put('/organizations/more-information', [EditesOrganizationController::class, 'UpdateMoreInformation'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_more_information->value . ',' . 'edit']);
  Route::get('/organizations/more-information/{id}', [ViewsOrganizationController::class, 'moreInformation'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_more_information->value . ',' . 'view']);

  Route::get('/organizations/details/{id}', [ViewsOrganizationController::class, 'details'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_information->value . ',' . 'view']);
  Route::post('/organizations/details', [EditesOrganizationController::class, 'updateDetails'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_information->value . ',' . 'edit']);

  Route::get('/organizations/status/{id}', [ViewsOrganizationController::class, 'status']);
});


Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  // view
  Route::get('/organizations/statistics', [ViewsOrganizationController::class, "organizationStatistics"])->middleware(["userHasMainPermission:" . PermissionEnum::organizations->value . ',' . 'view']);
  Route::get('/organizations', [ViewsOrganizationController::class, 'index'])->middleware(["userHasMainPermission:" . PermissionEnum::organizations->value . ',' . 'view']);
  Route::post('/organizations', [StoresOrganizationController::class, 'store'])->middleware(["userHasMainPermission:" . PermissionEnum::organizations->value . ',' . 'add']);

  // Uknown
  Route::get('/organizations/pending-task/{id}', [ViewsOrganizationController::class, 'pendingTask']);
  // Pending Task
});

Route::prefix('v1')->middleware(["authorized:" . 'organization:api'])->group(function () {

  Route::post('/organizations/register/form', [StoresOrganizationController::class, 'registerFormCompleted']);
  Route::get('/organizations/register/form/{id}', [ViewsOrganizationController::class, 'startRegisterForm']);
});

Route::prefix('v1')->group(function () {
  Route::get('organizations/public', [ViewsOrganizationController::class, 'publicOrganizations']);
});

Route::prefix('v1')->group(function () {

  Route::get('/organizations/latest', [PublicViewsOrganizationController::class, 'latestOrganizations']);
  Route::get('/organizations/topprojects', [PublicViewsOrganizationController::class, 'topOrganizationsByProjects']);
});
