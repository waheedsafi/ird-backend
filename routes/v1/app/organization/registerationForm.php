
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\app\organization\OrganizationPdfController;

Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,organization:api'])->group(function () {
  Route::get('/organization/generate/registeration/{id}', [OrganizationPdfController::class, 'generateForm']);
});
