
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\template\ActivityController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/activities', [ActivityController::class, "activities"])->middleware(["userHasSubPermission:" . PermissionEnum::activity->value . "," . SubPermissionEnum::activity_user_activity->value . ',' . 'view']);
});
