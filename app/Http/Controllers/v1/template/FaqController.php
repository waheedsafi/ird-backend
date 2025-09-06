<?php

namespace App\Http\Controllers\v1\template;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\Languages\LanguageEnum;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\v1\faq\FaqStoreRequest;
use App\Models\Faq;
use App\Models\FaqTrans;

class FaqController extends Controller
{
    private $cacheName = 'faq_list';

    public function index()
    {
        $locale = App::getLocale();

        $tr =  Cache::remember($this->cacheName, 1800, function () use ($locale) {
            return DB::table('faqs as f')
                ->join('faq_trans as ft', function ($join) use ($locale) {
                    $join->on('ft.faq_id', '=', 'f.id')
                        ->where('ft.language_name', $locale);
                })
                ->join('faq_type_trans as ftt', function ($join) use ($locale) {
                    $join->on('ftt.faq_type_id', '=', 'f.faq_type_id')
                        ->where('ftt.language_name', $locale);
                })
                ->select(
                    'f.id',
                    "ft.question",
                    'ft.answer',
                    'f.order',
                    'ftt.value as type',
                    'f.created_at',
                )
                ->get();
        });
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store(FaqStoreRequest $request)
    {
        $request->validated();

        DB::beginTransaction();
        // 1. Create
        $faq = Faq::create([
            'faq_type_id' => $request->type_id,
            'is_active' => $request->show,
        ]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            FaqTrans::create([
                "question" => $request["question_{$name}"],
                "answer" => $request["answer_{$name}"],
                "faq_id" => $faq->id,
                "language_name" => $code,
            ]);
        }

        $locale = App::getLocale();
        $question = $request->question_english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->question_farsi;
        } else if ($locale == LanguageEnum::pashto->value) {
            $name = $request->question_pashto;
        }
        DB::commit();

        // Clear cache
        Cache::forget($this->cacheName);
        return response()->json([
            'message' => __('app_translation.success'),
            'faq' => [
                "id" => $faq->id,
                "question" => $question,
                "type" => $request->type,
                "created_at" => $faq->created_at
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function edit($id)
    {
        $faq = DB::table('faqs as f')
            ->where('f.id', $id)
            ->join('faq_trans as ft', function ($join) {
                $join->on('ft.faq_id', '=', 'f.id');
            })
            ->join('faq_type_trans as ftt', function ($join) {
                $join->on('ftt.faq_type_id', '=', 'f.faq_type_id');
            })
            ->select(
                'f.id',
                'f.is_active as show',
                'ftt.value as type',
                'ftt.faq_type_id',
                DB::raw("MAX(CASE WHEN ft.language_name = 'fa' THEN answer END) as answer_farsi"),
                DB::raw("MAX(CASE WHEN ft.language_name = 'en' THEN answer END) as answer_english"),
                DB::raw("MAX(CASE WHEN ft.language_name = 'ps' THEN answer END) as answer_pashto"),
                DB::raw("MAX(CASE WHEN ft.language_name = 'fa' THEN question END) as question_farsi"),
                DB::raw("MAX(CASE WHEN ft.language_name = 'en' THEN question END) as question_english"),
                DB::raw("MAX(CASE WHEN ft.language_name = 'ps' THEN question END) as question_pashto")
            )
            ->groupBy(
                'f.id',
                'f.is_active',
                'ftt.value',
                'ftt.faq_type_id',
            )
            ->first();
        return response()->json(
            [
                "id" => $faq->id,
                "question_english" => $faq->question_english,
                "question_farsi" => $faq->question_farsi,
                "question_pashto" => $faq->question_pashto,
                "answer_english" => $faq->answer_english,
                "answer_farsi" => $faq->answer_farsi,
                "answer_pashto" => $faq->answer_pashto,
                "show" => $faq->show,
                "type" => ['id' => $faq->faq_type_id, 'name' => $faq->type],
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function destroy($id)
    {
        $faq = Faq::find($id);
        Cache::forget($this->cacheName);

        if ($faq) {
            $faq->delete();
            return response()->json([
                'message' => __('app_translation.success'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else
            return response()->json([
                'message' => __('app_translation.failed'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
    }
    public function update(FaqStoreRequest $request)
    {
        $request->validated();
        // This validation not exist in UrgencyStoreRequest
        $request->validate([
            "id" => "required"
        ]);
        // 1. Find
        $faq = Faq::find($request->id);
        if (!$faq) {
            return response()->json([
                'message' => __('app_translation.faq_not_found')
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        DB::beginTransaction();

        $trans = FaqTrans::where('faq_id', $request->id)
            ->select(
                'id',
                'question',
                'answer',
                'language_name',
            )
            ->get();
        // Update
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran = $trans->where('language_name', $code)->first();
            $tran->question = $request["question_{$name}"];
            $tran->answer = $request["answer_{$name}"];
            $tran->save();
        }

        $faq->faq_type_id = $request->type_id;
        $faq->is_active = $request->show;
        $faq->save();

        $locale = App::getLocale();
        $question = $request->question_english;
        if ($locale == LanguageEnum::farsi->value) {
            $name = $request->question_farsi;
        } else if ($locale == LanguageEnum::pashto->value) {
            $name = $request->question_pashto;
        }
        DB::commit();

        // Clear cache
        Cache::forget($this->cacheName);
        return response()->json([
            'message' => __('app_translation.success'),
            'faq' => [
                "id" => $faq->id,
                "question" => $question,
                "type" => $request->type,
                "created_at" => $faq->created_at
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function indexPublic()
    {
        $locale =  App::getLocale();
        $faqs = DB::table('faqs')
            ->join('faq_trans', function ($join) use ($locale) {
                $join->on('faqs.id', '=', 'faq_trans.faq_id')
                    ->where('faq_trans.language_name', '=', $locale);
            })
            ->join('faq_types', 'faq_types.id', '=', 'faqs.faq_type_id')
            ->join('faq_type_trans', function ($join) use ($locale) {
                $join->on('faq_types.id', '=', 'faq_type_trans.faq_type_id')
                    ->where('faq_type_trans.language_name', '=', $locale);
            })
            ->where('faqs.is_active', true)
            ->select([
                'faq_type_trans.value as type_name',
                'faq_trans.question',
                'faq_trans.answer',
                'faqs.order',
            ])
            ->orderBy('faq_types.id')
            ->get()
            ->groupBy('type_name');  // Group by the FAQ type name

        return response()->json(
            $faqs,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
