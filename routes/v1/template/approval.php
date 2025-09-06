
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\template\ApprovalController;


Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  Route::get('approvals/pending/users', [ApprovalController::class, 'pendingUserApproval'])->middleware(["userHasSubPermission:" . PermissionEnum::approval->value . "," . SubPermissionEnum::user_approval->value . ',' . 'view']);
  Route::get('approvals/approved/users', [ApprovalController::class, 'approvedUserApproval'])->middleware(["userHasSubPermission:" . PermissionEnum::approval->value . "," . SubPermissionEnum::user_approval->value . ',' . 'view']);
  Route::get('approvals/rejected/users', [ApprovalController::class, 'rejectedUserApproval'])->middleware(["userHasSubPermission:" . PermissionEnum::approval->value . "," . SubPermissionEnum::user_approval->value . ',' . 'view']);
  Route::get('approvals/{id}', [ApprovalController::class, 'approval'])->middleware(["userHasSubPermission:" . PermissionEnum::approval->value . "," . SubPermissionEnum::user_approval->value . ',' . 'view']);
  Route::get('approvals/request/users/{id}', [ApprovalController::class, 'requestForUser'])->middleware(["userHasMainPermission:" . PermissionEnum::users->value . ',' . 'edit']);

  Route::post('approvals', [ApprovalController::class, 'store'])->middleware(["userHasSubPermission:" . PermissionEnum::approval->value . "," . SubPermissionEnum::user_approval->value . ',' . 'edit']);

  Route::get('approvals/pending/organizations', [ApprovalController::class, 'pendingOrganizationApproval'])->middleware(["userHasSubPermission:" . PermissionEnum::approval->value . "," . SubPermissionEnum::organization_approval->value . ',' . 'view']);
  Route::get('approvals/approved/organizations', [ApprovalController::class, 'approvedOrganizationApproval'])->middleware(["userHasSubPermission:" . PermissionEnum::approval->value . "," . SubPermissionEnum::organization_approval->value . ',' . 'view']);
  Route::get('approvals/rejected/organizations', [ApprovalController::class, 'rejectedOrganizationApproval'])->middleware(["userHasSubPermission:" . PermissionEnum::approval->value . "," . SubPermissionEnum::organization_approval->value . ',' . 'view']);

  Route::get('approvals/pending/donors', [ApprovalController::class, 'pendingDonorApproval'])->middleware(["userHasSubPermission:" . PermissionEnum::approval->value . "," . SubPermissionEnum::donor_approval->value . ',' . 'view']);
  Route::get('approvals/approved/donors', [ApprovalController::class, 'approvedDonorApproval'])->middleware(["userHasSubPermission:" . PermissionEnum::approval->value . "," . SubPermissionEnum::donor_approval->value . ',' . 'view']);
  Route::get('approvals/rejected/donors', [ApprovalController::class, 'rejectedDonorApproval'])->middleware(["userHasSubPermission:" . PermissionEnum::approval->value . "," . SubPermissionEnum::donor_approval->value . ',' . 'view']);

  Route::get('approvals/pending/projects', [ApprovalController::class, 'pendingProjectsApproval'])->middleware(["userHasSubPermission:" . PermissionEnum::approval->value . "," . SubPermissionEnum::organization_approval->value . ',' . 'view']);
  Route::get('approvals/approved/projects', [ApprovalController::class, 'approvedProjectsApproval'])->middleware(["userHasSubPermission:" . PermissionEnum::approval->value . "," . SubPermissionEnum::organization_approval->value . ',' . 'view']);
  Route::get('approvals/rejected/projects', [ApprovalController::class, 'rejectedProjectsApproval'])->middleware(["userHasSubPermission:" . PermissionEnum::approval->value . "," . SubPermissionEnum::organization_approval->value . ',' . 'view']);
});
