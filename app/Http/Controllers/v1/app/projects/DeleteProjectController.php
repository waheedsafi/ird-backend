<?php

namespace App\Http\Controllers\v1\app\projects;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;

class DeleteProjectController extends Controller
{
    protected $pendingTaskRepository;

    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository
    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;
    }

    public function destroyPendingTask(Request $request, $id)
    {
        $request->validate([
            'task_type' => "required"
        ]);
        $authUser = $request->user();
        $task_type = $request->task_type;

        $this->pendingTaskRepository->destroyPendingTask(
            $authUser,
            $task_type,
            $id
        );

        return response()->json([
            "message" => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
