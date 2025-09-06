<?php

namespace App\Http\Controllers\v1\template;

use App\Models\Division;
use Illuminate\Http\Request;
use App\Models\DivisionTrans;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\Languages\LanguageEnum;
use App\Http\Requests\v1\division\DivisionStoreRequest;
use Illuminate\Support\Facades\Cache;

class DivisionController extends Controller
{

    private $cacheDivision = 'division_list';

    public function __construct()
    {
        $this->cacheDivision = 'division_list';
    }
    public function index()
    {
        $locale = App::getLocale();

        $tr =  Cache::remember($this->cacheDivision, 1800, function () use ($locale) {

            return  DB::table('divisions as d')
                ->join('division_trans as dt', function ($join) use ($locale) {
                    $join->on('dt.division_id', '=', 'd.id')
                        ->where('dt.language_name', $locale);
                })
                ->select('d.id', "dt.value as name", 'd.created_at')->get();
        });
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function edit($id)
    {
        $division = DB::table('division_trans as dt')
            ->where('dt.division_id', $id)
            ->select(
                'dt.division_id',
                DB::raw("MAX(CASE WHEN dt.language_name = 'fa' THEN value END) as farsi"),
                DB::raw("MAX(CASE WHEN dt.language_name = 'en' THEN value END) as english"),
                DB::raw("MAX(CASE WHEN dt.language_name = 'ps' THEN value END) as pashto")
            )
            ->groupBy('dt.division_id')
            ->first();
        return response()->json(
            [
                "id" => $division->division_id,
                "english" => $division->english,
                "farsi" => $division->farsi,
                "pashto" => $division->pashto,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function update(DivisionStoreRequest $request)
    {
        $request->validated();
        // This validation not exist in UrgencyStoreRequest
        $request->validate([
            "id" => "required"
        ]);
        // 1. Find
        $division = Division::find($request->id);
        if (!$division) {
            return response()->json([
                'message' => __('app_translation.division_not_found')
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $trans = DivisionTrans::where('division_id', $request->id)
            ->select('id', 'language_name', 'value')
            ->get();
        // Update
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran =  $trans->where('language_name', $code)->first();
            $tran->value = $request["{$name}"];
            $tran->save();
        }

        $locale = App::getLocale();
        $name = $request->english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->farsi;
        } else if ($locale == LanguageEnum::pashto->value) {
            $name = $request->pashto;
        }

        Cache::forget($this->cacheDivision);

        return response()->json([
            'message' => __('app_translation.success'),
            'division' => [
                "id" => $division->id,
                "name" => $name,
                "created_at" => $division->created_at
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store(DivisionStoreRequest $request)
    {
        $request->validated();
        // 1. Create
        $division = Division::create([]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            DivisionTrans::create([
                "value" => $request["{$name}"],
                "division_id" => $division->id,
                "language_name" => $code,
            ]);
        }

        $locale = App::getLocale();
        $name = $request->english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->farsi;
        } else if ($locale == LanguageEnum::pashto->value) {
            $name = $request->pashto;
        }
        Cache::forget($this->cacheDivision);

        return response()->json([
            'message' => __('app_translation.success'),
            'division' => [
                "id" => $division->id,
                "name" => $name,
                "created_at" => $division->created_at
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function destroy($id)
    {
        $division = Division::find($id);
        Cache::forget($this->cacheDivision);

        if ($division) {
            $division->delete();
            return response()->json([
                'message' => __('app_translation.success'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else
            return response()->json([
                'message' => __('app_translation.failed'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
    }
}
