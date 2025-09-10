<?php

namespace App\Http\Controllers\v1\app\news;

use App\Models\News;
use App\Models\User;
use App\Models\NewsTran;
use App\Models\NewsDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\Languages\LanguageEnum;
use App\Enums\Statuses\PriorityEnum;
use App\Http\Requests\v1\news\NewsStoreRequest;
use App\Http\Requests\v1\news\NewsUpdateRequest;
use App\Traits\FileHelperTrait;
use App\Traits\FilterTrait;

class NewsController extends Controller
{
    use FileHelperTrait, FilterTrait;
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $locale = App::getLocale();

        $query =  DB::table('news as n')
            ->join('news_trans as ntr', 'ntr.news_id', '=', 'n.id')
            ->join('news_type_trans as ntt', 'ntt.news_type_id', '=', 'n.news_type_id')
            ->join('priority_trans as pt', 'pt.priority_id', '=', 'n.priority_id')
            ->leftJoin('news_documents as nd', 'nd.news_id', '=', 'n.id')
            ->where('ntr.language_name', $locale)
            ->where('pt.language_name', $locale)
            ->where('ntt.language_name', $locale)
            ->select(
                'n.id as id',
                'n.visible',
                'n.date',
                'n.visibility_date',
                'n.news_type_id',
                'ntt.value AS news_type',
                'n.priority_id',
                'pt.value AS priority',
                'ntr.title',
                'ntr.contents',
                'nd.url AS image',  // Assuming you want the first image URL
                'n.created_at'
            );

        $this->applyDate($query, $request, 'n.created_at', 'n.created_at');
        $this->applyFilters($query, $request, [
            'news_type_id' => 'ntt.value',
            'priority_id' => 'pt.value',
            'visible' => 'n.visible',
            'visibility_date' => 'n.visibility_date',
            'date' => 'n.date',
        ]);
        $this->applySearch($query, $request, [
            'tilte' => 'ntr.title',
        ]);

        $result = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json(
            [
                "newses" => $result,
            ]
        );
    }

    public function edit(Request $request, $id)
    {
        $locale = App::getLocale();

        // Fetch the news along with its related data using eager loading
        $news = News::with(['newsDocument', 'newsType.newsTypeTran', 'priority.priorityTran'])
            ->find($id);

        if (!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }

        // Fetch translations for the news
        $translations = NewsTran::where('news_id', $id)
            ->whereIn('language_name', ['en', 'ps', 'fa'])
            ->get()
            ->keyBy('language_name');

        // Retrieve individual translations or set defaults
        $newsEnTran = $translations->get('en', (object) ['title' => '', 'contents' => '']);
        $newsPsTran = $translations->get('ps', (object) ['title' => '', 'contents' => '']);
        $newsFaTran = $translations->get('fa', (object) ['title' => '', 'contents' => '']);

        // Prepare the response
        return response()->json([
            'news' => [
                'id' => $news->id,
                'title_english' => $newsEnTran->title,
                'title_pashto' => $newsPsTran->title,
                'title_farsi' => $newsFaTran->title,
                'content_english' => $newsEnTran->contents,
                'content_pashto' => $newsPsTran->contents,
                'content_farsi' => $newsFaTran->contents,
                'type' => [
                    'id' => $news->news_type_id,
                    'value' => $news->newsType->newsTypeTran
                        ->where('language_name', $locale)
                        ->first()->value ?? 'Type not found'
                ],
                'priority' => [
                    'id' => $news->priority_id,
                    'value' => $news->priority->priorityTran
                        ->where('language_name', $locale)
                        ->first()->value ?? 'Priority not found'
                ],
                'cover_pic' => [
                    'name' => $news->newsDocument->name ?? '',
                    'path' => $news->newsDocument->url ?? '',
                ],
                'user' => User::select('id', 'username')
                    ->where('id', $news->user_id)
                    ->first()
                    ->username,
                'date' => $news->date,
                'visible' => $news->visible,
                'visibility_date' => $news->visibility_date,
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function publicNews(Request $request, $id)
    {
        $locale = App::getLocale();
        $query =  DB::table('news as n')
            ->join('users as u', 'u.id', '=', 'n.user_id')
            ->join('news_trans as ntr', 'ntr.news_id', '=', 'n.id')
            ->join('news_type_trans as ntt', 'ntt.news_type_id', '=', 'n.news_type_id')
            ->join('priority_trans as pt', 'pt.priority_id', '=', 'n.priority_id')
            ->leftJoin('news_documents as nd', 'nd.news_id', '=', 'n.id')
            ->where('ntr.language_name', $locale)
            ->where('pt.language_name', $locale)
            ->where('ntt.language_name', $locale)
            ->where('n.visible', 1)
            ->where('n.id', $id)
            ->select(
                'n.id',
                'n.date',
                'n.news_type_id',
                'ntt.value AS news_type',
                'n.priority_id',
                'pt.value AS priority',
                'ntr.title',
                'ntr.contents',
                'u.username as user',
                'nd.url AS image'  // Assuming you want the first image URL
            )
            ->first();
        return response()->json([
            "news" => $query

        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function publicNewses(Request $request)
    {
        $limit = $request->query('_limit', 10); // e.g. ?_limit=10
        $page = $request->query('_page', 1);    // e.g. ?_page=2
        $offset = ($page - 1) * $limit;

        $locale = App::getLocale();

        $query =  DB::table('news as n')
            ->join('news_trans as ntr', 'ntr.news_id', '=', 'n.id')
            ->join('news_type_trans as ntt', 'ntt.news_type_id', '=', 'n.news_type_id')
            ->join('priority_trans as pt', 'pt.priority_id', '=', 'n.priority_id')
            ->leftJoin('news_documents as nd', 'nd.news_id', '=', 'n.id')
            ->where('ntr.language_name', $locale)
            ->where('pt.language_name', $locale)
            ->where('ntt.language_name', $locale)
            ->where('n.visible', 1)
            ->select(
                'n.id as id',
                'n.visible',
                'n.date',
                'n.news_type_id',
                'ntt.value AS news_type',
                'n.priority_id',
                'pt.value AS priority',
                'ntr.title',
                'ntr.contents',
                'nd.url AS image',  // Assuming you want the first image URL
                'n.created_at'
            );

        $this->applyDate($query, $request, 'n.created_at', 'n.created_at');
        $this->applyFilters($query, $request, [
            'news_type_id' => 'ntt.value',
            'date' => 'n.date',
        ]);
        $this->applySearch($query, $request, [
            'tilte' => 'ntr.title',
        ]);
        $tr = $query->orderByDesc('n.id')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store(NewsStoreRequest $request)
    {
        $validatedData = $request->validated();
        $authUser = $request->user();

        // Begin transaction
        DB::beginTransaction();

        $news = News::create([
            "user_id" => $authUser->id,
            "visible" => true,
            "date" => $validatedData["date"],
            "visibility_date" => $request->visibility_date,
            "priority_id" => $validatedData["priority"],
            "news_type_id" => $validatedData["type"]
        ]);
        NewsTran::create([
            "news_id" => $news->id,
            "language_name" => LanguageEnum::default->value,
            "title" => $validatedData["title_english"],
            "contents" => $validatedData["content_english"],
        ]);
        NewsTran::create([
            "news_id" => $news->id,
            "language_name" => LanguageEnum::pashto->value,
            "title" => $validatedData["title_pashto"],
            "contents" => $validatedData["content_pashto"],
        ]);
        NewsTran::create([
            "news_id" => $news->id,
            "language_name" => LanguageEnum::farsi->value,
            "title" => $validatedData["title_farsi"],
            "contents" => $validatedData["content_farsi"],
        ]);

        // 3. Store documents
        $document = $this->storePublicDocument($request, "news/", 'cover_pic');
        NewsDocument::create([
            "news_id" => $news->id,
            "url" => $document['path'],
            "extension" => $document['extension'],
            "name" => $document['name'],
        ]);

        // If everything goes well, commit the transaction
        DB::commit();
        // Return a success response

        $title = $validatedData["title_english"];
        $contents = $validatedData["content_english"];
        $locale = App::getLocale();
        if ($locale === LanguageEnum::farsi->value) {
            $title = $validatedData["title_farsi"];
            $contents = $validatedData["content_farsi"];
        } else if ($locale === LanguageEnum::pashto->value) {
            $title = $validatedData["title_pashto"];
            $contents = $validatedData["content_pashto"];
        }

        return response()->json(
            [
                'message' => __('app_translation.success'),
                'news' => [
                    "id" => $news->id,
                    "user" => $authUser->username,
                    "visible" => true,
                    "visibility_date" => $request->visibility_date,
                    "title" => $title,
                    "news_type" => $request->type_name,
                    "priority" => $request->priority_name,
                    "date" => $validatedData["date"],
                    "created_at" => $news->created_at,
                    "contents" => $contents,
                    "image" => $document['path'],
                ]
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function update(NewsUpdateRequest $request)
    {
        $request->validated();
        $authUser = $request->user();

        $id = $request->id;
        // Find the news record or throw an exception if not found
        $news = News::find($id);
        if (!$news) {
            return response()->json(['message' => __('app_translation.news_not_found')], 404);
        }

        // Begin transaction
        DB::beginTransaction();
        $news->update([
            "user_id" => $authUser->id,
            "visible" => $request->visible,
            "date" => $request->date,
            "visibility_date" => $request->visibility_date,
            "priority_id" => $request->priority,
            "news_type_id" => $request->type
        ]);

        // Update translations
        NewsTran::updateOrCreate(
            ["news_id" => $news->id, "language_name" => LanguageEnum::default->value],
            ["title" => $request->title_english, "contents" => $request->content_english]
        );

        NewsTran::updateOrCreate(
            ["news_id" => $news->id, "language_name" => LanguageEnum::pashto->value],
            ["title" => $request->title_pashto, "contents" => $request->content_pashto]
        );

        NewsTran::updateOrCreate(
            ["news_id" => $news->id, "language_name" => LanguageEnum::farsi->value],
            ["title" => $request->title_farsi, "contents" => $request->content_farsi]
        );

        // Update document if a new one is uploaded
        $existingDocument = NewsDocument::where('news_id', $news->id)->first();
        if (!$existingDocument) {
            return response()->json(['message' => __('app_translation.cover_pic_not_found')], 404);
        }
        // 1. Check cover_pic is changed
        if ($existingDocument->url !== $request->cover_pic) {
            $path = storage_path('app/' . $existingDocument->url);

            if (file_exists($path)) {
                unlink($path);
            }
            // 1.2 update document
            $document = $this->storePublicDocument($request, "news/", 'cover_pic');
            $existingDocument->url = $document['path'];
            $existingDocument->extension = $document['extension'];
            $existingDocument->name = $document['name'];
            $existingDocument->save();
        }

        // Commit transaction
        DB::commit();

        // Return a success response
        return response()->json(
            [
                'message' => __('app_translation.success')
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function destroy($id)
    {
        $news = News::find($id);
        if ($news) {
            // Begin transaction
            DB::beginTransaction();
            // 1. Delete Translation
            NewsTran::where('news_id', $news->id)->delete();
            $existingDocument = NewsDocument::where('news_id', $news->id)->first();
            // Delete documents
            $path = storage_path('app/' . $existingDocument->url);

            if (file_exists($path)) {
                unlink($path);
            }
            $existingDocument->delete();
            $news->delete();

            // Commit transaction
            DB::commit();
            return response()->json([
                'message' => __('app_translation.success'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else
            return response()->json([
                'message' => __('app_translation.failed'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
    }


    public function highPriorityNews(Request $request)
    {
        $locale = App::getLocale();

        $baseQuery = DB::table('news as n')
            ->join('news_trans as ntr', function ($join) use ($locale) {
                $join->on('ntr.news_id', '=', 'n.id')
                    ->where('ntr.language_name', $locale);
            })
            ->join('priorities as pri', 'pri.id', '=', 'n.priority_id')
            ->join('news_documents as nd', 'nd.news_id', '=', 'n.id')
            ->orderBy('n.date', 'desc')
            ->select(
                'n.id',
                'ntr.title',
                'ntr.contents as description',
                'nd.url as image',
                'pri.id as priority'
            );

        $limit = 6;

        // Step 1: High
        $high = (clone $baseQuery)
            ->where('pri.id', PriorityEnum::high)
            ->limit($limit)
            ->get();

        $count = $high->count();

        if ($count < $limit) {
            // Step 2: Medium
            $medium = (clone $baseQuery)
                ->where('pri.id', PriorityEnum::medium)
                ->limit($limit - $count)
                ->get();
            $high = $high->merge($medium);
            $count = $high->count();

            if ($count < $limit) {
                // Step 3: Low
                $low = (clone $baseQuery)
                    ->where('pri.id', PriorityEnum::low)
                    ->limit($limit - $count)
                    ->get();
                $high = $high->merge($low);
            }
        }

        return response()->json(
            $high,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function latestNews(Request $request)
    {
        $locale = App::getLocale();

        $newses = DB::table('news as n')
            ->join('news_trans as ntr', function ($join) use ($locale) {
                $join->on('ntr.news_id', '=', 'n.id')
                    ->where('ntr.language_name', $locale);
            })
            ->join('priorities as pri', 'pri.id', '=', 'n.priority_id')
            ->join('news_documents as nd', 'nd.news_id', '=', 'n.id')
            ->orderBy('n.date', 'desc') // Just latest, no priority filter
            ->select(
                'n.id',
                'ntr.title',
                'ntr.contents as description',
                'nd.url as image',

            )
            ->limit(6)
            ->get();

        return response()->json(
            $newses,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }


    // search function 
    protected function applySearch($query, $request)
    {

        $searchColumn = $request->input('filters.search.column');
        $searchValue = $request->input('filters.search.value');

        if ($searchColumn && $searchValue) {
            $allowedColumns = ['title', 'contents'];

            // Ensure that the search column is allowed
            if (in_array($searchColumn, $allowedColumns)) {
                $query->where($searchColumn, 'like', '%' . $searchValue . '%');
            }
        }
    }
}
