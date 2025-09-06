<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Http\Controllers\v1\template\AuditLogController;



Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/audits/events', [AuditLogController::class, "events"])->middleware(["userHasMainPermission:" . PermissionEnum::audit->value . ',' . 'view']);
    Route::get('/audits/users/type', [AuditLogController::class, "userTypes"])->middleware(["userHasMainPermission:" . PermissionEnum::audit->value . ',' . 'view']);
    Route::get('/audits/users', [AuditLogController::class, "users"])->middleware(["userHasMainPermission:" . PermissionEnum::audit->value . ',' . 'view']);
    Route::get('/audits/tables', [AuditLogController::class, "tables"])->middleware(["userHasMainPermission:" . PermissionEnum::audit->value . ',' . 'view']);

    Route::get('/audits/generate/pdf-report', [AuditLogController::class, "generatePdfReport"])->middleware(["userHasMainPermission:" . PermissionEnum::audit->value . ',' . 'view']);
    Route::get('/audits/generate/pdf-report/{id}', [AuditLogController::class, "generatePdfReportById"])->middleware(["userHasMainPermission:" . PermissionEnum::audit->value . ',' . 'view']);
    Route::get('/audits/generate/excel-report', [AuditLogController::class, "generateExcelReport"])->middleware(["userHasMainPermission:" . PermissionEnum::audit->value . ',' . 'view']);
    Route::get('/audits/generate/excel-report/{id}', [AuditLogController::class, "generateExcelReportById"])->middleware(["userHasMainPermission:" . PermissionEnum::audit->value . ',' . 'view']);
    Route::get('/audits', [AuditLogController::class, "index"])->middleware(["userHasMainPermission:" . PermissionEnum::audit->value . ',' . 'view']);
    Route::get('/audits/{id}', [AuditLogController::class, "edit"])->middleware(["userHasMainPermission:" . PermissionEnum::audit->value . ',' . 'view']);
});
