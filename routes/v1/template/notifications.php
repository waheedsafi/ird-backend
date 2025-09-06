
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\template\NotificationController;

Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,organization:api,donor:api'])->group(function () {
    Route::get('/notifications', [NotificationController::class, "index"]);
    Route::delete('/notifications/{id}', [NotificationController::class, "destroy"]);
    Route::put('/notifications', [NotificationController::class, "update"]);
});
