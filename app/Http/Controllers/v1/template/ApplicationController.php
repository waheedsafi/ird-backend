<?php

namespace App\Http\Controllers\v1\template;

use Exception;
use App\Models\Gender;
use App\Models\Application;
use App\Models\NidTypeTrans;
use Illuminate\Http\Request;
use App\Models\ApplicationTrans;
use App\Models\NationalityTrans;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use App\Enums\Languages\LanguageEnum;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\v1\applications\ApplicationUpdateRequest;

class ApplicationController extends Controller
{
    private $ApplicationCacheKey = 'application';
    private $countryCacheKey = 'country';
    private $provinceCacheKey = 'province';
    private $districtCacheKey = 'district';
    private $nationalityCacheKey = 'nationality';
    private $gendersCacheKey = 'genders';
    private $nidTypeCacheKey = 'nid_type';


    public function changeLocale($locale)
    {
        // Set the language in a cookie
        if ($locale === "en" || $locale === "fa" || $locale === "ps") {
            // 1. Set app language
            App::setLocale($locale);

            $cookie = cookie(
                'locale',
                $locale,
                60 * 24 * 30,
                '/',
                null,                          // null: use current domain
                true,                 // secure only in production
                true,                         // httpOnly
                false,                         // raw
                'None' // for dev, use 'None' to allow cross-origin if needed
            );
            return response()->json([
                'message' => __('app_translation.lang_change_success'),
            ], 200, [], JSON_UNESCAPED_UNICODE)->cookie($cookie);
        } else {
            // 3. Passed language not exist in system
            response()->json([
                'message' => __('app_translation.lang_change_failed'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }
    public function getTranslations($lang, $namespace)
    {
        App::setLocale($lang);
        $translations = Lang::get($namespace);
        return response()
            ->json($translations)
            ->header('Cache-Control', 'no-store'); // disable HTTP caching
    }
    public function fonts($filename)
    {
        $path = public_path('fonts/' . $filename);

        if (!File::exists($path)) {
            abort(404);
        }

        $response = response()->file($path);
        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');

        return $response;
    }

    public function applications()
    {
        $locale = App::getLocale();
        $cacheKey = $this->ApplicationCacheKey . '_' . $locale;

        $query  = Cache::remember($cacheKey, 1800, function () use ($locale) {
            return DB::table('applications as app')
                ->join('application_trans as appt', function ($join) use ($locale) {
                    $join->on('appt.application_id', '=', 'app.id')
                        ->where('appt.language_name', '=', $locale);
                })
                ->select(
                    'app.id',
                    'app.cast_to',
                    'app.value',
                    'appt.description',
                    'appt.value as name',
                )
                ->get();
        });
        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function updateApplication(ApplicationUpdateRequest $request)
    {
        $request->validated();
        // 1. Create
        $application = Application::find($request->id);
        if (!$application) {
            return response()->json([
                'message' => __('app_translation.application_not_found')
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        DB::beginTransaction();

        $application->value = $request->value;
        $application->save();
        DB::commit();

        $locale = App::getLocale();
        $cacheKey = $this->ApplicationCacheKey . '_' . $locale;
        Cache::forget($cacheKey);

        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function countries()
    {
        $locale = App::getLocale();
        $cacheKey = $this->countryCacheKey . '_' . $locale;
        $tr  = Cache::remember($cacheKey, 1800, function () use ($locale) {
            return DB::table('country_trans as ct')
                ->where('ct.language_name', $locale)
                ->select('ct.country_id as id', 'ct.value as name')
                ->get();
        });
        return response()->json($tr);
    }

    public function provinces($country_id)
    {
        $locale = App::getLocale();
        $cacheKey = $this->provinceCacheKey . '_' . $country_id . '_' . $locale;
        $tr  = Cache::remember($cacheKey, 1800, function () use ($locale, $country_id) {
            return DB::table('provinces as p')
                ->where('p.country_id', $country_id)
                ->join('province_trans as pt', function ($join) use ($locale) {
                    $join->on('pt.province_id', '=', 'p.id')
                        ->where('pt.language_name', $locale);
                })
                ->select('pt.province_id as id', 'pt.value as name')
                ->get();
        });

        return response()->json($tr);
    }

    public function districts($province_id)
    {
        $locale = App::getLocale();
        $cacheKey = $this->districtCacheKey . '_' . $province_id . '_' . $locale;
        $tr  = Cache::remember($cacheKey, 1800, function () use ($locale, $province_id) {
            return  DB::table('districts as d')
                ->where('d.province_id', $province_id)
                ->join('district_trans as dt', function ($join) use ($locale) {
                    $join->on('dt.district_id', '=', 'd.id')
                        ->where('dt.language_name', $locale);
                })
                ->select('dt.district_id as id', 'dt.value as name')
                ->get();
        });
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function genders()
    {
        $locale = App::getLocale();
        $cacheKey = $this->gendersCacheKey . '_' . $locale;
        $tr  = Cache::remember($cacheKey, 2592000, function () use ($locale) {
            return DB::table('gender_trans as gt')
                ->where('gt.language_name', $locale)
                ->select('gt.gender_id as id', "gt.name")->get();
        });
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function nationalities()
    {
        $countryId = 2;
        $locale = App::getLocale();
        $cacheKey = $this->nationalityCacheKey . '_' . $locale;

        $tr  = Cache::remember($cacheKey, 2592000, function () use ($locale, $countryId) {
            return NationalityTrans::select('nationality_id as id', 'value as name')
                ->where('language_name', $locale)
                ->orderByRaw("CASE WHEN nationality_id = ? THEN 0 ELSE 1 END", [$countryId])
                ->get();
        });

        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function nidTypes()
    {
        $locale = App::getLocale();
        $cacheKey = $this->nidTypeCacheKey . '_' . $locale;
        $tr  = Cache::remember($cacheKey, 2592000, function () use ($locale) {
            return NidTypeTrans::select('value as name', 'nid_type_id as id')
                ->where('language_name', $locale)
                ->get();
        });
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function currencies()
    {
        $locale = App::getLocale();
        $currency = DB::table('currencies as c')
            ->join('currency_trans as ct', function ($join) use ($locale) {
                $join->on('ct.currency_id', '=', 'c.id')
                    ->where('ct.language_name', $locale);
            })
            ->select('currency_id as id', 'name', 'symbol')
            ->get();

        return  response()->json(
            $currency,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
