<?php

namespace App\Http\Controllers\v1\template;

use App\Models\ModelJob;
use Illuminate\Http\Request;
use App\Models\ModelJobTrans;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\Languages\LanguageEnum;
use App\Http\Requests\v1\job\JobStoreRequest;
use Illuminate\Support\Facades\Cache;

class JobController extends Controller
{
    private $cacheJob = 'job_list';

    public function __construct()
    {
        $this->cacheJob = 'job_list';
    }
    public function index()
    {
        $locale = App::getLocale();

        $tr =  Cache::remember($this->cacheJob, 1800, function () use ($locale) {
            return DB::table('model_jobs as mj')
                ->join('model_job_trans as mjt', function ($join) use ($locale) {
                    $join->on('mjt.model_job_id', '=', 'mj.id')
                        ->where('mjt.language_name', $locale);
                })
                ->select('mj.id', "mjt.value as name", 'mj.created_at')
                ->get();
        });
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store(JobStoreRequest $request)
    {
        $request->validated();
        // 1. Create
        $job = ModelJob::create([]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            ModelJobTrans::create([
                "value" => $request["{$name}"],
                "model_job_id" => $job->id,
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
        // Clear cache
        Cache::forget($this->cacheJob);
        return response()->json([
            'message' => __('app_translation.success'),
            'job' => [
                "id" => $job->id,
                "name" => $name,
                "created_at" => $job->created_at
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function destroy($id)
    {
        $job = ModelJob::find($id);
        Cache::forget($this->cacheJob);

        if ($job) {
            $job->delete();
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
        $job = DB::table('model_job_trans as mjt')
            ->where('mjt.model_job_id', $id)
            ->select(
                'mjt.model_job_id',
                DB::raw("MAX(CASE WHEN mjt.language_name = 'fa' THEN value END) as farsi"),
                DB::raw("MAX(CASE WHEN mjt.language_name = 'en' THEN value END) as english"),
                DB::raw("MAX(CASE WHEN mjt.language_name = 'ps' THEN value END) as pashto")
            )
            ->groupBy('mjt.model_job_id')
            ->first();
        return response()->json(
            [
                "id" => $job->model_job_id,
                "name_english" => $job->english,
                "name_farsi" => $job->farsi,
                "name_pashto" => $job->pashto,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function update(JobStoreRequest $request)
    {
        $request->validated();
        // This validation not exist in UrgencyStoreRequest
        $request->validate([
            "id" => "required"
        ]);
        // 1. Find
        $job = ModelJob::find($request->id);
        if (!$job) {
            return response()->json([
                'message' => __('app_translation.job_not_found')
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $trans = ModelJobTrans::where('model_job_id', $request->id)
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
        Cache::forget($this->cacheJob);

        return response()->json([
            'message' => __('app_translation.success'),
            'job' => [
                "id" => $job->id,
                "name" => $name,
                "created_at" => $job->created_at
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
