<?php

namespace App\Traits;

use App\Models\CheckList;
use App\Models\CheckListTrans;


trait UtilHelperTrait
{
    /**
     * Converts a string to title case.
     *
     * @param string $model
     * @return string
     */
    public static function getModelName(string $model): string
    {
        // Generate a unique key for the access token, e.g., access_token:<user_id>
        $firstSlashPos = strpos($model, '\\');
        $secondSlashPos = strpos($model, '\\', $firstSlashPos + 1);

        // Get the part after the second backslash
        $className = substr($model, $secondSlashPos + 1);
        return $className;
    }
    protected function validateCheckList($task, $exclude = [], $type, &$documentCheckListIds = [])
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
    protected function validateCheckListInclude($task, $include = [], $type, &$documentCheckListIds = [])
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
