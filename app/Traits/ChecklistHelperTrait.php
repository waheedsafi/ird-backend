<?php

namespace App\Traits;

use App\Models\CheckList;


trait ChecklistHelperTrait
{
    public function checkFileWithList($file, $checklist_id)
    {
        // 1. Validate check exist
        $checklist = CheckList::find($checklist_id);
        if (!$checklist) {
            return response()->json([
                'message' => __('app_translation.checklist_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $extension = $file->getClientOriginalExtension();
        $fileSize = $file->getSize();
        $allowedExtensions = explode(',', $checklist->acceptable_extensions);
        $allowedSize = $checklist->file_size * 1024; // Converted to byte
        $found = false;
        foreach ($allowedExtensions as $allowedExtension) {
            if ($allowedExtension == $extension) {
                if ($fileSize > $allowedSize) {
                    return response()->json([
                        'message' => __('app_translation.file_size_error') . " " . $allowedSize,
                    ], 422, [], JSON_UNESCAPED_UNICODE);
                }
                $found = true;
                break;
            }
        }
        if (!$found) {
            return response()->json([
                'message' => __('app_translation.allowed_file_types') . " " . $checklist->acceptable_extensions,
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        return $found;
    }
}
