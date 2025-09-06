<?php

namespace App\Repositories\Approval;

use App\Models\Approval;
use App\Traits\FilterTrait;
use App\Models\ApprovalDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Enums\Types\ApprovalTypeEnum;

class ApprovalRepository implements ApprovalRepositoryInterface
{
    use FilterTrait;
    public function getByNotifierTypeAndRequesterType($approval_type_id, $requester_type)
    {
        $locale = App::getLocale();

        $query = DB::table('approvals as a')
            ->where("a.requester_type", $requester_type)
            ->where("a.approval_type_id", $approval_type_id)
            ->join('approval_type_trans as att', function ($join) use ($locale) {
                $join->on('att.approval_type_id', '=', 'a.approval_type_id')
                    ->where('att.language_name', $locale);
            })
            ->join('notifier_type_trans as ntt', function ($join) use ($locale) {
                $join->on('ntt.notifier_type_id', '=', 'a.notifier_type_id')
                    ->where('ntt.language_name', $locale);
            });

        // Initialize requester field
        $requesterField = '';

        // Conditional join and field for requester
        switch ($requester_type) {
            case 'App\Models\Organization':
                $query->join('organization_trans as org', function ($join) use ($locale) {
                    $join->on('org.organization_id', '=', 'a.requester_id')
                        ->where('org.language_name', $locale);
                });
                $requesterField = 'org.name as requester';
                break;

            case 'App\Models\Donor':
                $query->join('donors as dnr', 'dnr.id', '=', 'a.requester_id');
                $requesterField = 'dnr.name as requester';
                break;

            case 'App\Models\User':
            default:
                $query->join('users as usr', 'usr.id', '=', 'a.requester_id');
                $requesterField = 'usr.username as requester';
                break;
        }

        // Final select with requester field included
        return $query->select(
            'a.id',
            'a.completed',
            'a.request_date',
            'a.requester_id',
            'a.responder_id',
            'a.responder_type',
            'a.notifier_type_id',
            'ntt.value as notifier_type',
            DB::raw('(
            SELECT COUNT(*)
            FROM approval_documents as ad_count
            WHERE ad_count.approval_id = a.id
        ) as document_count'),
            DB::raw($requesterField)
        );
    }


    public function storeApproval($requester_id, $requester_type, $notifier_type_id, $request_comment)
    {
        return Approval::create([
            "request_comment" => $request_comment,
            "requester_id" => $requester_id,
            "approval_type_id" => ApprovalTypeEnum::pending->value,
            "requester_type" => $requester_type,
            "notifier_type_id" => $notifier_type_id,
        ]);
    }
    public function storeApprovalDocument($approval_id, $documentData)
    {
        return ApprovalDocument::create([
            "approval_id" => $approval_id,
            'actual_name' => $documentData['actual_name'],
            'size' => $documentData['size'],
            'path' => $documentData['path'],
            'type' => $documentData['type'],
            'check_list_id' => $documentData['check_list_id'],
        ]);
    }
    public function approval($approval_id)
    {
        $locale = App::getLocale();
        return DB::table('approvals as a')
            ->where("a.id", $approval_id)
            ->leftJoin('users as u', function ($join) {
                $join->on('u.id', '=', 'a.responder_id');
            })
            ->join('notifier_type_trans as ntt', function ($join) use ($locale) {
                $join->on('ntt.notifier_type_id', '=', 'a.notifier_type_id')
                    ->where('ntt.language_name', $locale);
            })
            ->leftJoin('approval_documents as ad', 'ad.approval_id', '=', 'a.id')
            ->leftJoin('check_list_trans as ct', function ($join) use ($locale) {
                $join->on('ct.check_list_id', '=', 'ad.check_list_id')
                    ->where('ct.language_name', $locale);
            });
    }
    public function userApproval($approval_id)
    {
        $approval = $this->approval($approval_id)
            ->join('users as usr', function ($join) {
                $join->on('usr.id', '=', 'a.requester_id');
            })
            ->select(
                'a.id',
                'a.completed',
                'a.requester_id',
                'usr.username as requester_name',
                'a.request_date',
                "a.request_comment",
                'a.responder_id',
                'u.username as responder',
                'a.respond_date',
                "a.respond_comment",
                'a.notifier_type_id',
                'ntt.value as notifier_type',
                'ad.id as approval_id',
                'ad.path',
                'ad.actual_name as name',
                'ad.type as extension',
                'ad.size',
                'ct.value as checklist_name'
            )
            ->get();

        $approvalsWithDocuments = $approval->groupBy('id')->map(function ($approvalGroup) {
            $approval = $approvalGroup->first();

            $documents = $approvalGroup
                ->filter(fn($item) => $item->approval_id !== null)  // <-- filter here
                ->map(function ($item) {
                    return [
                        'id' => $item->approval_id,
                        'path' => $item->path,
                        'name' => $item->name,
                        'extension' => $item->extension,
                        'size' => $item->size,
                        'checklist_name' => $item->checklist_name,
                    ];
                });

            $approval->approval_documents = $documents;
            unset($approval->approval_id, $approval->checklist_name, $approval->path, $approval->name, $approval->extension, $approval->size);  // Clean up extra fields

            return $approval;
        })->values();

        return $approvalsWithDocuments->first();
    }
    public function organizationApproval($approval_id)
    {
        $locale = App::getLocale();
        $approval = DB::table('approvals as a')
            ->where("a.id", $approval_id)
            ->leftJoin('users as u', function ($join) {
                $join->on('u.id', '=', 'a.responder_id');
            })
            ->join('organization_trans as nt', function ($join) use ($locale) {
                $join->on('nt.organization_id', '=', 'a.requester_id')
                    ->where('nt.language_name', $locale);
            })
            ->join('agreements as ag', function ($join) use ($locale) {
                $join->on('ag.organization_id', '=', 'a.requester_id')
                    ->latest('ag.end_date');
            })
            ->join('notifier_type_trans as ntt', function ($join) use ($locale) {
                $join->on('ntt.notifier_type_id', '=', 'a.notifier_type_id')
                    ->where('ntt.language_name', $locale);
            })
            ->join('approval_documents as ad', 'ad.approval_id', '=', 'a.id')
            ->join('check_list_trans as ct', function ($join) use ($locale) {
                $join->on('ct.check_list_id', '=', 'ad.check_list_id')
                    ->where('ct.language_name', $locale);
            })
            ->select(
                'a.id',
                'a.completed',
                'a.requester_id',
                'nt.name as requester_name',
                'a.request_date',
                'ag.start_date',
                'ag.end_date',
                "a.request_comment",
                'a.responder_id',
                'u.username as responder',
                'a.respond_date',
                "a.respond_comment",
                'a.notifier_type_id',
                'ntt.value as notifier_type',
                'ad.id as approval_id',
                'ad.path',
                'ad.actual_name as name',
                'ad.type as extension',
                'ad.size',
                'ct.value as checklist_name'
            )
            ->get();

        $approvalsWithDocuments = $approval->groupBy('id')->map(function ($approvalGroup) {
            $approval = $approvalGroup->first();
            $documents = $approvalGroup->map(function ($item) {
                return [
                    'id' => $item->approval_id,
                    'path' => $item->path,
                    'name' => $item->name,
                    'extension' => $item->extension,
                    'size' => $item->size,
                    'checklist_name' => $item->checklist_name,
                ];
            });

            $approval->approval_documents = $documents;
            unset($approval->approval_id, $approval->checklist_name, $approval->path, $approval->name, $approval->extension, $approval->size);  // Clean up extra fields

            return $approval;
        })->values();

        if (count($approvalsWithDocuments) != 0)
            return $approvalsWithDocuments->first();
        return null;
    }
}
