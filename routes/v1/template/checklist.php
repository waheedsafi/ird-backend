<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\template\CheckListController;

Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,organization:api'])->group(function () {
  Route::get('organization/checklist/types', [CheckListController::class, 'checklistTypes']);
  Route::get('organization/register/checklist', [CheckListController::class, 'organizationRegister']);
  Route::get('organization/register/abroad/director-checklist', [CheckListController::class, 'organizationRegisterAbroadDirector']);
  Route::get('organization/extend/checklist', [CheckListController::class, 'organizationExtend']);
  Route::get('organization/extend/abroad/director-checklist', [CheckListController::class, 'organizationExtendAbroadDirector']);
  Route::get('organization-checklist/{id}', [CheckListController::class, 'checklist']);
  Route::get('organization/common-checklist/{id}', [CheckListController::class, 'commonChecklist']);
  Route::get('organization/register/signed/form/checklist', [CheckListController::class, 'missingRegisterSignedForm']);
  Route::get('checklists/projects/signed/mou', [CheckListController::class, 'missingMouSignedForm'])->middleware(["userHasMainPermission:" . PermissionEnum::projects->value . ',' . 'view']);

  Route::get('checklists/project-registeration', [CheckListController::class, 'projectRegisteration'])->middleware(["userHasMainPermission:" . PermissionEnum::projects->value . ',' . 'view']);
  // Tested
  Route::get('checklists/deputy-doc', [CheckListController::class, 'deputyDocChecklist']);
  Route::get('checklists/representative', [CheckListController::class, 'representativeChecklist']);
});

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  Route::get('checklists/types', [CheckListController::class, 'checklistTypes'])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_checklist->value . ',' . 'view']);
  Route::get('checklists/{id}', [CheckListController::class, "edit"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_checklist->value . ',' . 'view']);
  Route::get('checklists', [CheckListController::class, 'index'])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_checklist->value . ',' . 'view']);
  Route::post('checklists', [CheckListController::class, 'store'])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_checklist->value . ',' . 'add']);
  Route::delete('checklists/{id}', [CheckListController::class, 'destroy'])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_checklist->value . ',' . 'delete']);
  Route::put('checklists', [CheckListController::class, 'update'])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_checklist->value . ',' . 'edit']);
});
