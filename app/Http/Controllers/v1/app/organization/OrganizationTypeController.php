<?php

namespace App\Http\Controllers\v1\app\organization;

use App\Http\Controllers\Controller;
use App\Models\OrganizationType;
use Illuminate\Support\Facades\App;

class OrganizationTypeController extends Controller
{
    public function types()
    {
        $locale = App::getLocale();
        $tr = OrganizationType::join('organization_type_trans', 'organization_types.id', '=', 'organization_type_trans.organization_type_id')
            ->where('organization_type_trans.language_name', $locale)
            ->select('organization_type_trans.value as name', 'organization_types.id')
            ->orderBy('organization_types.id', 'desc')
            ->get();

        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
