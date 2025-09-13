<?php

namespace App\Traits;

use App\Models\CheckList;
use App\Models\CheckListTrans;


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
    protected function checkListWithExlude($task, $exclude = [], $type, &$documentCheckListIds = [])
    {
        // If nationality is Afghanistan, require work permit

        // Get all checklist IDs of this type
        $checkListIds = CheckList::where('check_list_type_id', $type)
            ->pluck('id')
            ->toArray();

        // Remove excluded checklist IDs
        $checkListIds = array_diff($checkListIds, $exclude);

        // Get checklist IDs from task documents
        if ($task) {
            $documentCheckListIds = $this->pendingTaskRepository->pendingTaskDocumentQuery($task->id)
                ->pluck('check_list_id')
                ->toArray();
        }

        // Find missing checklist IDs
        $missingCheckListIds = array_diff($checkListIds, $documentCheckListIds);

        if (count($missingCheckListIds) > 0) {
            $missingCheckListNames = CheckListTrans::whereIn('check_list_id', $missingCheckListIds)
                ->where('language_name', app()->getLocale())
                ->pluck('value');

            $errors = [];
            foreach ($missingCheckListNames as $item) {
                array_push($errors, [__('app_translation.checklist_not_found') . ' ' . $item]);
            }

            return $errors;
        }

        return null;
    }
    protected function checkListWithInclude($task, $include = [], $type, &$documentCheckListIds = [])
    {
        $checkListIds = CheckList::where('check_list_type_id', $type)
            ->pluck('id')
            ->toArray();

        // Remove excluded checklist IDs
        $checkListIds = array_intersect($checkListIds, $include);
        // Get checklist IDs from task documents
        if ($task) {
            $documentCheckListIds = $this->pendingTaskRepository->pendingTaskDocumentQuery($task->id)
                ->pluck('check_list_id')
                ->toArray();
        }

        // Find missing checklist IDs
        $missingCheckListIds = array_diff($checkListIds, $documentCheckListIds);

        if (count($missingCheckListIds) > 0) {
            $missingCheckListNames = CheckListTrans::whereIn('check_list_id', $missingCheckListIds)
                ->where('language_name', app()->getLocale())
                ->pluck('value');

            $errors = [];
            foreach ($missingCheckListNames as $item) {
                array_push($errors, [__('app_translation.checklist_not_found') . ' ' . $item]);
            }

            return $errors;
        }

        return null;
    }
}
