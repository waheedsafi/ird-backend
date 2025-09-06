
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\app\agreement\AgreementController;



Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,organization:api'])->group(function () {
  Route::get('/organizations/agreements/{id}', [AgreementController::class, 'index'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_agreement->value . ',' . 'view']);
  Route::get('/organizations/agreement-documents', [AgreementController::class, 'agreementDocuments'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_agreement->value . ',' . 'view']);
  Route::get('/organizations/agreements/statuses/{id}', [AgreementController::class, 'statuses'])->middleware(["userHasSubPermission:" . PermissionEnum::organizations->value . "," . SubPermissionEnum::organizations_agreement_status->value . ',' . 'view']);
});
