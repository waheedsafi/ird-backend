<?php

namespace App\Http\Controllers\v1\app\organization;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;
use App\Repositories\Organization\OrganizationRepositoryInterface;

class DeletesOrganizationController extends Controller
{
    protected $organizationRepository;
    protected $pendingTaskRepository;


    public function __construct(
        OrganizationRepositoryInterface $organizationRepository,
        PendingTaskRepositoryInterface $pendingTaskRepository
    ) {
        $this->organizationRepository = $organizationRepository;
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
        $locale = App::getLocale();
        $data = $this->organizationRepository->startRegisterFormInfo($id, $locale);
        if (!$data) {
            return response()->json([
                'message' => __('app_translation.organization_not_found'),
            ], 404);
        }

        return response()->json([
            "message" => __('app_translation.success'),
            'organization' => $data,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
