<?php

namespace App\Http\Controllers\v1\app\file;

use Illuminate\Http\Request;
use App\Traits\PathHelperTrait;
use App\Enums\Types\TaskTypeEnum;
use App\Http\Controllers\Controller;
use App\Traits\ChecklistHelperTrait;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Traits\FileHelperTrait;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;

class FileController extends Controller
{
    use ChecklistHelperTrait, PathHelperTrait, FileHelperTrait;
    protected $pendingTaskRepository;
    protected $storageRepository;

    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
        StorageRepositoryInterface $storageRepository,
    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;
        $this->storageRepository = $storageRepository;
    }
    public function checklistUploadFile(Request $request)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            $task_type = $request->task_type;;
            $organization_id = $request->organization_id;
            $checklist_id = $request->checklist_id;
            $file = $save->getFile();
            // 1. Validate checklist
            $validationResult = $this->checkFileWithList($file, $request->checklist_id);
            if ($validationResult !== true) {
                $filePath = $file->getRealPath();
                unlink($filePath);
                return $validationResult; // Return validation errors
            }
            // 2. Store document
            return $this->pendingTaskRepository->fileStore(
                $file,
                $request,
                $task_type,
                $checklist_id,
                $organization_id
            );
        }

        // If not finished, send current progress.
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            "status" => true,
        ]);
    }

    // 1. Upload files in case does not have task_id
    public function singleChecklistFileUpload(Request $request)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            $task_type = $request->task_type;
            $check_list_id = $request->checklist_id;
            $file = $save->getFile();

            // 1. Validate checklist
            $validationResult = $this->checkFileWithList($file, $request->checklist_id);
            if ($validationResult !== true) {
                $filePath = $file->getRealPath();
                unlink($filePath);
                return $validationResult; // Return validation errors
            }
            // 2. Delete all previous PendingTask for current user_id, user_type and task_type
            $this->pendingTaskRepository->destroyPendingTask($request->user(), $task_type, null);
            // 3. Store new Pendding Document Task
            return $this->pendingTaskRepository->fileStore(
                $save->getFile(),
                $request,
                $task_type,
                $check_list_id,
                null
            );
        }

        // If not finished, send current progress.
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            "status" => true,
        ]);
    }
    // 1. Upload files in case does not have task_id
    public function singleFileUpload(Request $request)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            $task_type = $request->task_type;
            $check_list_id = $request->checklist_id;
            $identifier = $request->identifier;
            $file = $save->getFile();

            // 1. Validate checklist
            $validationResult = $this->checkFileWithList($file, $request->checklist_id);
            if ($validationResult !== true) {
                $filePath = $file->getRealPath();
                unlink($filePath);
                return $validationResult; // Return validation errors
            }
            // 2. Delete all previous PendingTask for current user_id, user_type and task_type
            $this->pendingTaskRepository->destroyPendingTask($request->user(), $task_type, null);
            // 3. Store new Pendding Document Task
            return $this->pendingTaskRepository->fileStore(
                $save->getFile(),
                $request,
                $task_type,
                $check_list_id,
                $identifier
            );
        }

        // If not finished, send current progress.
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            "status" => true,
        ]);
    }
    public function singleChecklistFileUploadNoPending(Request $request)
    {
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            $task_type = $request->task_type;

            if ($task_type == TaskTypeEnum::organization_registeration->value) {
                $checklist_id = $request->checklist_id;
                $organization_id = $request->organization_id;
                $document_id = $request->document_id;
                $file = $save->getFile();
                return $this->storageRepository->directlyOrgDocumentStore($file, $checklist_id, $organization_id, $document_id);
            } else if ($task_type == TaskTypeEnum::project_registeration->value) {
                $checklist_id = $request->checklist_id;
                $organization_id = $request->organization_id;
                $document_id = $request->document_id;
                $project_id = $request->project_id;
                $file = $save->getFile();
                return $this->storageRepository->directlyProjectDocumentStore($file, $checklist_id, $organization_id, $project_id, $document_id);
            }
        }

        // If not finished, send current progress.
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            "status" => true,
        ]);
    }
}
