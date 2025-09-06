
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\app\organization\OrganizationTypeController;

Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,organization:api,donor:api'])->group(function () {
  Route::get('/organization-types', [OrganizationTypeController::class, 'types']);
});
