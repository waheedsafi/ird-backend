<?php

namespace App\Http\Controllers\v1\template;

use App\Http\Controllers\Controller;
use App\Models\Priority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class PriorityController extends Controller
{
    public function index()
    {
        $locale = App::getLocale();
        $tr = Priority::join("priority_trans", function ($join) use ($locale) {
            // Join Translate table with the related model (e.g., 'destinations') based on translable_id
            $join->on('priority_trans.priority_id', '=', "priorities.id")
                ->where('priority_trans.language_name', '=', $locale);
        })
            ->select("priority_trans.value AS name", 'priorities.id',)
            ->get();
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
