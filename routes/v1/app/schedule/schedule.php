
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Http\Controllers\v1\app\schedule\ScheduleController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/schedules', [ScheduleController::class, 'schedules'])->middleware(["userHasMainPermission:" . PermissionEnum::schedules->value . ',' . 'view']);
    Route::get('/schedules/prepare', [ScheduleController::class, 'prepareSchedule'])->middleware(["userHasMainPermission:" . PermissionEnum::schedules->value . ',' . 'view']);
    Route::post('/schedules/submit', [ScheduleController::class, 'submitSchedule'])->middleware(["userHasMainPermission:" . PermissionEnum::schedules->value . ',' . 'add']);

    Route::get('/schedules/{id}', [ScheduleController::class, 'edit'])->middleware(["userHasMainPermission:" . PermissionEnum::schedules->value . ',' . 'view']);
    Route::post('/schedules', [ScheduleController::class, 'store'])->middleware(["userHasMainPermission:" . PermissionEnum::schedules->value . ',' . 'add']);
    Route::put('/schedules', [ScheduleController::class, 'update'])->middleware(["userHasMainPermission:" . PermissionEnum::schedules->value . ',' . 'edit']);
    Route::get('/schedules/present/{id}', [ScheduleController::class, 'present'])->middleware(["userHasMainPermission:" . PermissionEnum::schedules->value . ',' . 'add']);
});
