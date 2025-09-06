<?php

namespace App\Repositories\Storage;

use Illuminate\Http\UploadedFile;

interface StorageRepositoryInterface
{
    /**
     * Creates a approval.
     * 
     *
     * @param string agreement_id
     * @param string organization_id
     * @param string checklists
     * @param callable callback
     * @return boolean
     */
    public function documentStore($agreement_id, $organization_id, $checklists, ?callable $callback);
    public function organizationDocumentApprovalStore($agreement_id, $organization_id, $checklists, ?callable $callback);
    public function projectDocumentStore($project_id, $organization_id, $checklists, ?callable $callback);
    public function projectDocumentApprovalStore($project_id, $organization_id, $checklists, ?callable $callback);
    public function scheduleDocumentStore($schedule_id, $checklists, ?callable $callback);
    public function directlyOrgDocumentStore(UploadedFile $file, $checklist_id, $organization_id, $document_id);
    public function directlyProjectDocumentStore(UploadedFile $file, $checklist_id, $organization_id, $project_id, $document_id);
}
