<?php

namespace App\Http\Controllers\v1\app\news;

use App\Models\NewsType;
use App\Models\NewsTypeTrans;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\Languages\LanguageEnum;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\v1\newsType\NewsTypeStoreRequest;

class NewsTypeController extends Controller
{
    private $cacheName = 'news_types';
    private function generateCacheKey()
    {
        $locale = App::getLocale();
        return $this->cacheName . '_' . $locale;
    }
    public function index()
    {
        $locale = App::getLocale();

        $tr = Cache::remember($this->generateCacheKey(), 1, function () use ($locale) {
            return NewsType::join("news_type_trans", function ($join) use ($locale) {
                $join->on('news_type_trans.news_type_id', '=', "news_types.id")
                    ->where('news_type_trans.language_name', '=', $locale);
            })
                ->select("news_type_trans.value AS name", 'news_types.id')
                ->get();
        });
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store(NewsTypeStoreRequest $request)
    {
        $request->validated();
        // 1. Create
        $type = NewsType::create([]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            NewsTypeTrans::create([
                "value" => $request["name_{$name}"],
                "news_type_id" => $type->id,
                "language_name" => $code,
            ]);
        }

        $locale = App::getLocale();
        $name = $request->name_english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->name_farsi;
        } else if ($locale == LanguageEnum::pashto->value) {
            $name = $request->name_pashto;
        }
        // Clear cache
        Cache::forget($this->generateCacheKey());
        return response()->json([
            'message' => __('app_translation.success'),
            'type' => [
                "id" => $type->id,
                "name" => $name,
                "created_at" => $type->created_at
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function destroy($id)
    {
        $type = NewsType::find($id);
        Cache::forget($this->generateCacheKey());

        if ($type) {
            $type->delete();
            return response()->json([
                'message' => __('app_translation.success'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else
            return response()->json([
                'message' => __('app_translation.failed'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
    }
    public function edit($id)
    {
        $type = DB::table('news_type_trans as ftt')
            ->where('ftt.news_type_id', $id)
            ->select(
                'ftt.news_type_id',
                DB::raw("MAX(CASE WHEN ftt.language_name = 'fa' THEN value END) as name_farsi"),
                DB::raw("MAX(CASE WHEN ftt.language_name = 'en' THEN value END) as name_english"),
                DB::raw("MAX(CASE WHEN ftt.language_name = 'ps' THEN value END) as name_pashto")
            )
            ->groupBy('ftt.news_type_id')
            ->first();
        return response()->json(
            [
                "id" => $type->news_type_id,
                "name_english" => $type->name_english,
                "name_farsi" => $type->name_farsi,
                "name_pashto" => $type->name_pashto,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function update(NewsTypeStoreRequest $request)
    {
        $request->validated();
        // This validation not exist in UrgencyStoreRequest
        $request->validate([
            "id" => "required"
        ]);
        // 1. Find
        $type = NewsType::find($request->id);
        if (!$type) {
            return response()->json([
                'message' => __('app_translation.news_type_not_found')
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $trans = NewsTypeTrans::where('news_type_id', $request->id)
            ->select('id', 'language_name', 'value')
            ->get();
        // Update
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran =  $trans->where('language_name', $code)->first();
            $tran->value = $request["name_{$name}"];
            $tran->save();
        }

        $locale = App::getLocale();
        $name = $request->name_english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->name_farsi;
        } else if ($locale == LanguageEnum::pashto->value) {
            $name = $request->name_pashto;
        }
        Cache::forget($this->generateCacheKey());

        return response()->json([
            'message' => __('app_translation.success'),
            'type' => [
                "id" => $type->id,
                "name" => $name,
                "created_at" => $type->created_at
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
