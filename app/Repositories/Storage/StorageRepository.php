<?php

namespace App\Repositories\Storage;

use App\Enums\Statuses\StatusEnum;
use App\Models\Document;
use App\Models\Agreement;
use App\Traits\FileHelperTrait;
use App\Traits\PathHelperTrait;
use App\Models\AgreementDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\PendingTaskDocument;
use App\Traits\ChecklistHelperTrait;

use function Laravel\Prompts\select;

class StorageRepository implements StorageRepositoryInterface
{
    use PathHelperTrait, FileHelperTrait, ChecklistHelperTrait;

    public function documentStore($agreement_id, $organization_id, $checklists, ?callable $callback)
    {
        foreach ($checklists as $checklist) {
            $baseName = basename($checklist['path']);
            $oldPath = $this->getTempFullPath() . $baseName; // Absolute path of temp file

            $newDirectory = $this->getOrganizationRegisterFolder($organization_id, $agreement_id, $checklist['check_list_id']);

            if (!is_dir($newDirectory)) {
                mkdir($newDirectory, 0775, true);
            }
            $newPath = $newDirectory . $baseName; // Keep original filename
            $dbStorePath = $this->getOrganizationRegisterDBPath($organization_id, $agreement_id, $checklist['check_list_id'], $baseName);
            // Move the file
            if (file_exists($oldPath)) {
                rename($oldPath, $newPath);
            } else {
                return response()->json([
                    'errors' => [[$checklist['actual_name'] . ": " . __('app_translation.file_not_found')]],
                ], 404);
            }

            $documentData = [
                'actual_name' => $checklist['actual_name'],
                'size' => $checklist['size'],
                'path' => $dbStorePath,
                'type' => $checklist['extension'],
                'check_list_id' => $checklist['check_list_id'],
                'agreement_id' => $agreement_id
            ];
            if ($callback) {
                $callback($documentData);
            }
        }
    }

    public function projectDocumentStore($project_id, $organization_id, $checklists, ?callable $callback)
    {
        foreach ($checklists as $checklist) {
            $baseName = basename($checklist['path']);
            $oldPath = $this->getTempFullPath() . $baseName; // Absolute path of temp file

            $newDirectory = $this->getProjectRegisterFolder($organization_id, $project_id, $checklist['check_list_id']);

            if (!is_dir($newDirectory)) {
                mkdir($newDirectory, 0775, true);
            }
            $newPath = $newDirectory . $baseName; // Keep original filename
            $dbStorePath = $this->getProjectRegisterDBPath($organization_id, $project_id, $checklist['check_list_id'], $baseName);
            // Move the file
            if (file_exists($oldPath)) {
                rename($oldPath, $newPath);
            } else {
                return response()->json([
                    'errors' => [[$checklist['actual_name'] . ": " . __('app_translation.file_not_found')]],
                ], 404);
            }

            $documentData = [
                'actual_name' => $checklist['actual_name'],
                'size' => $checklist['size'],
                'path' => $dbStorePath,
                'type' => $checklist['extension'],
                'check_list_id' => $checklist['check_list_id'],
                'project_id' => $project_id
            ];
            if ($callback) {
                $callback($documentData);
            }
        }
    }

    public function scheduleDocumentStore($schedule_id, $pending_task_id, ?callable $callback)
    {
        // Get checklist IDs
        $documents = PendingTaskDocument::join('check_lists', 'check_lists.id', 'pending_task_documents.check_list_id')
            ->where('pending_task_id', $pending_task_id)
            ->select('size', 'path', 'check_list_id', 'actual_name', 'extension')
            ->get();

        foreach ($documents as $checklist) {
            $baseName = basename($checklist['path']);
            $oldPath = $this->getTempFullPath() . $baseName; // Absolute path of temp file

            $newDirectory = $this->getScheduleRegisterFolder($schedule_id);

            if (!is_dir($newDirectory)) {
                mkdir($newDirectory, 0775, true);
            }
            $newPath = $newDirectory . $baseName; // Keep original filename
            $dbStorePath = $this->getScheduleRegisterDBPath($schedule_id, $baseName);
            // Move the file
            if (file_exists($oldPath)) {
                rename($oldPath, $newPath);
            } else {
                return response()->json([
                    'errors' => [[$checklist['actual_name'] . ": " . __('app_translation.file_not_found')]],
                ], 404);
            }

            $documentData = [
                'actual_name' => $checklist['actual_name'],
                'size' => $checklist['size'],
                'path' => $dbStorePath,
                'type' => $checklist['extension'],
                'check_list_id' => $checklist['check_list_id'],
                'schedule_id' => $schedule_id
            ];
            if ($callback) {
                $callback($documentData);
            }
        }
    }
    public function directlyOrgDocumentStore(UploadedFile $file, $checklist_id, $organization_id, $document_id)
    {
        // 1. Allow in case orgreement is not registered
        $agreement = Agreement::where('organization_id', $organization_id)
            ->where('end_date', null) // Order by end_date descending
            ->first();           // Get the first record (most recent)
        // 1. If agreement does not exists no further process.
        if (!$agreement) {
            // Hence user trying to override previous agreement document
            return response()->json([
                'message' => __('app_translation.unauthorized')
            ], 409);
        }
        // 1. Validate checklist
        $validationResult = $this->checkFileWithList($file, $checklist_id);
        if ($validationResult !== true) {
            $filePath = $file->getRealPath();
            unlink($filePath);
            return $validationResult; // Return validation errors
        }

        $oldDocument = Document::where('id', $document_id)
            ->first();
        if (!$oldDocument) {
            return response()->json([
                "message" => __('app_translation.success_prev_doc_not_f'),
            ], 404);
        }
        // 2. Move to place
        $fileActualName = $file->getClientOriginalName();
        $fileName = $this->createChunkUploadFilename($file);
        $fileSize = $file->getSize();
        $mimetype = $file->getMimeType();
        $finalPath = $this->getOrganizationRegisterFolder($organization_id, $agreement->id, $checklist_id);
        $dbStorePath = $this->getOrganizationRegisterDBPath($organization_id, $agreement->id, $checklist_id, $fileName);

        $file->move($finalPath, $fileName);
        $this->deleteDocument($this->transformToPrivate($oldDocument->path));
        $oldDocument->actual_name = $fileActualName;
        $oldDocument->path = $dbStorePath;
        $oldDocument->size = $fileSize;
        $oldDocument->type = $mimetype;
        $oldDocument->checklist_id = $checklist_id;
        $oldDocument->save();

        // 3. Delete previous file
        $data = [
            "path" => $dbStorePath,
            "document_id" => $oldDocument->id,
            "size" => $fileSize,
            "checklist_id" => $checklist_id,
            "type" => $mimetype,
            "name" => $fileActualName,
        ];
        return response()->json([
            "file" => $data,
            "message" => __('app_translation.success'),
        ], 200);
    }
    public function directlyProjectDocumentStore(UploadedFile $file, $checklist_id, $organization_id, $project_id, $document_id)
    {
        // 1. Allow in case orgreement is not registered
        $projectStatus = DB::table('project_statuses as ps')
            ->where('ps.project_id', $project_id)
            ->where('is_active', true)
            ->select('ps.status_id')
            ->first();
        // 1. If agreement does not exists no further process.
        if (
            !$projectStatus || $projectStatus->status_id == StatusEnum::active
            || $projectStatus->status_id == StatusEnum::block
            || $projectStatus->status_id == StatusEnum::pending_for_schedule
            || $projectStatus->status_id == StatusEnum::scheduled
        ) {
            return response()->json([
                'message' => __('app_translation.unauthorized')
            ], 409);
        }
        // 1. Validate checklist
        $validationResult = $this->checkFileWithList($file, $checklist_id);
        if ($validationResult !== true) {
            $filePath = $file->getRealPath();
            unlink($filePath);
            return $validationResult; // Return validation errors
        }

        $oldDocument = Document::where('id', $document_id)
            ->first();
        if (!$oldDocument) {
            return response()->json([
                "message" => __('app_translation.success_prev_doc_not_f'),
            ], 404);
        }

        // 2. Move to place
        $fileActualName = $file->getClientOriginalName();
        $fileName = $this->createChunkUploadFilename($file);
        $fileSize = $file->getSize();
        $mimetype = $file->getMimeType();
        $finalPath = $this->getProjectRegisterFolder($organization_id, $project_id, $checklist_id);
        $dbStorePath = $this->getProjectRegisterDBPath($organization_id, $project_id, $checklist_id, $fileName);

        $file->move($finalPath, $fileName);
        $this->deleteDocument($this->transformToPrivate($oldDocument->path));
        $oldDocument->actual_name = $fileActualName;
        $oldDocument->path = $dbStorePath;
        $oldDocument->size = $fileSize;
        $oldDocument->type = $mimetype;
        $oldDocument->checklist_id = $checklist_id;
        $oldDocument->save();

        // 3. Delete previous file
        $data = [
            "path" => $dbStorePath,
            "document_id" => $oldDocument->id,
            "size" => $fileSize,
            "checklist_id" => $checklist_id,
            "type" => $mimetype,
            "name" => $fileActualName,
        ];

        return response()->json([
            "file" => $data,
            "message" => __('app_translation.success'),
        ], 200);
    }
}
