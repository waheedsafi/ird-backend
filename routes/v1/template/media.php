
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\template\MediaController;

Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,organization:api,donor:api'])->group(function () {
    // Route::get('/media/profile', [MediaController::class, "downloadProfile"]);
    Route::get('/media/temporary', [MediaController::class, "tempMediadownload"]);
    // Route::get('/ngo/media', [MediaController::class, "ngoMediadownload"]);

    Route::get('/media/profile', [MediaController::class, "profileFile"]);
    Route::get('/media/private', [MediaController::class, "privateFile"]);
});
Route::prefix('v1')->group(function () {
    Route::get('/media/public', [MediaController::class, "publicFile"]);
});
