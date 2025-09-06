<?php

namespace App\Http\Controllers\v1\template;

use App\Models\Slideshow;
use Illuminate\Http\Request;
use App\Models\SlideshowTrans;
use App\Traits\FileHelperTrait;
use App\Traits\PathHelperTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\Languages\LanguageEnum;
use App\Http\Requests\v1\about\SlideshowStoreRequest;
use App\Http\Requests\v1\about\SlideshowUpdateRequest;

class SlideshowController extends Controller
{
    use FileHelperTrait, PathHelperTrait;

    public function publicIndex()
    {
        $locale = App::getLocale();
        $tr = DB::table('slideshows as s')
            ->where('s.visible', true)
            ->join('slideshow_trans as st', function ($join) use ($locale) {
                $join->on('st.slideshow_id', '=', 's.id')
                    ->where('st.language_name', $locale);
            })
            ->select('s.id', "s.visible", "s.image", 'st.title', 'st.description')->get();
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function index()
    {
        $locale = App::getLocale();
        $tr = DB::table('slideshows as s')
            ->join('slideshow_trans as st', function ($join) use ($locale) {
                $join->on('st.slideshow_id', '=', 's.id')
                    ->where('st.language_name', $locale);
            })
            ->select('s.id', "s.visible", "s.image", 'st.title', 'st.description')->get();
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store(SlideshowStoreRequest $request)
    {
        // Validate the request
        $validateData = $request->validated();
        // Begin transaction
        DB::beginTransaction();
        $document = $this->storePublicDocument($request, "images/slideshows/", 'image');

        // Store Staff data
        $slideshow = Slideshow::create([
            'visible' => $validateData['visible'],
            'image' => $document['path'],
            'user_id' => $request->user()->id,
        ]);

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            SlideshowTrans::create([
                "title" => $request["title_{$name}"],
                "description" => $request["description_{$name}"],
                "slideshow_id" => $slideshow->id,
                "language_name" => $code,
            ]);
        }

        DB::commit();
        $locale = App::getLocale();
        $title = $validateData['title_english'];
        $description = $validateData['description_english'];
        if ($locale == LanguageEnum::farsi->value) {
            $title = $validateData['title_farsi'];
            $description = $validateData['description_farsi'];
        } else if ($locale == LanguageEnum::pashto->value) {
            $title = $validateData['title_pashto'];
            $description = $validateData['description_pashto'];
        }
        return response()->json([
            'message' => __('app_translation.success'),
            'slideshow' => [
                "id" => $slideshow->id,
                "title" => $title,
                "image" => $slideshow->image,
                "description" => $description,
                "visible" => $slideshow->visible,
                "visible" => $slideshow->image,
                "saved_by" => $request->user()->username,
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function edit($id)
    {
        $slideshow = DB::table('slideshows as s')
            ->where('s.id', $id)
            ->join('users as u', 'u.id', '=', 's.user_id')
            ->join('slideshow_trans as st', 'st.slideshow_id', '=', 's.id')
            ->select(
                's.id',
                's.image',
                's.visible',
                'u.username as saved_by',
                DB::raw("MAX(CASE WHEN st.language_name = 'en' THEN st.title END) as title_english"),
                DB::raw("MAX(CASE WHEN st.language_name = 'fa' THEN st.title END) as title_farsi"),
                DB::raw("MAX(CASE WHEN st.language_name = 'ps' THEN st.title END) as title_pashto"),
                DB::raw("MAX(CASE WHEN st.language_name = 'en' THEN st.description END) as description_english"),
                DB::raw("MAX(CASE WHEN st.language_name = 'fa' THEN st.description END) as description_farsi"),
                DB::raw("MAX(CASE WHEN st.language_name = 'ps' THEN st.description END) as description_pashto")
            )
            ->groupBy('s.id', 's.image', 's.visible', 'u.username')
            ->first();

        return response()->json($slideshow, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function update(SlideshowUpdateRequest $request)
    {
        $validateData = $request->validated();

        // Begin transaction
        DB::beginTransaction();
        $slideshow = Slideshow::find($request->id);
        if (!$slideshow) {
            return response()->json([
                'message' => __('app_translation.slideshow_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $image = $slideshow->image;
        if ($image !== $request->image) {
            // Update profile
            $this->deleteDocument($this->transformToPublic($image));

            // 1.2 update document
            $document = $this->storePublicDocument($request, "images/slideshows/", 'image');
            if (!$document) {
                return response()->json([
                    'message' => __('app_translation.failed'),
                    'path' => $document,
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }
            $slideshow->image =  $document['path'];
        }

        // Update Staff details
        $slideshow->visible = $request->visible;
        $slideshow->user_id = $request->user()->id;
        $slideshow->save();

        $trans = SlideshowTrans::where('slideshow_id', $slideshow->id)
            ->select('id', 'language_name', 'title', 'description')
            ->get();
        // Update
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran =  $trans->where('language_name', $code)->first();
            $tran->title = $request["title_{$name}"];
            $tran->description = $request["description_{$name}"];
            $tran->save();
        }
        // Commit transaction
        DB::commit();

        $locale = App::getLocale();
        $title = $validateData['title_english'];
        $description = $validateData['description_english'];
        if ($locale == LanguageEnum::farsi->value) {
            $title = $validateData['title_farsi'];
            $description = $validateData['description_farsi'];
        } else if ($locale == LanguageEnum::pashto->value) {
            $title = $validateData['title_pashto'];
            $description = $validateData['description_pashto'];
        }
        return response()->json([
            'message' => __('app_translation.success'),
            'slideshow' => [
                "id" => $slideshow->id,
                "title" => $title,
                "image" => $slideshow->image,
                "description" => $description,
                "visible" => $slideshow->visible,
                "visible" => $slideshow->image,
                "saved_by" => $request->user()->username,
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
