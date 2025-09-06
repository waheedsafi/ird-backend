<?php

namespace App\Http\Controllers\v1\template;

use Illuminate\Http\Request;
use App\Traits\PathHelperTrait;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    use PathHelperTrait;
    public function publicFile(Request $request)
    {
        $filePath = $request->input('path');
        $path = $this->transformToPublic($filePath);

        if (!file_exists($path)) {
            return response()->json([
                'message' => __('app_translation.file_not_found'),
                'path' => $path,
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        return response()->file($path);
    }
    public function profileFile(Request $request)
    {
        $authUser = $request->user();
        $path = $this->transformToPrivate($authUser->profile);

        if (!file_exists($path)) {
            return response()->json([
                'message' => __('app_translation.file_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        return response()->file($path);
    }
    public function privateFile(Request $request)
    {
        $filePath = $request->query('path');
        $path = $this->transformToPrivate($filePath);

        if (!file_exists($path)) {
            return response()->json([
                'message' => __('app_translation.file_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        return response()->file($path);
    }
    public function tempMediadownload(Request $request)
    {
        $filePath = $request->input('path');
        $path = storage_path() . "/app/{$filePath}";
        if (!file_exists($path)) {
            return response()->json([
                'message' => __('app_translation.file_not_found'),
                'filePath' => $path,
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        return response()->file($path);
    }
}
