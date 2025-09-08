<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait PathHelperTrait
{
    /**
     * Combines filePath with backend public path (.../app/public).
     * 
     * @param string $filePath
     * @return string
     */
    public function transformToPublic($filePath)
    {
        return storage_path() . "/app/public/{$filePath}";
    }
    public function transformToPrivate($filePath)
    {
        return storage_path() . "/app/private/{$filePath}";
    }
    public function transformToTemp($filePath)
    {
        return storage_path() . "/app/temp/{$filePath}";
    }
    public function createChunkUploadFilename(UploadedFile $file)
    {
        return Str::uuid() . "." . $file->getClientOriginalExtension();
    }
    public function getTempFullPath()
    {
        return storage_path() . "/app/temp/";
    }
    public function dbTempFilePath($fileName)
    {
        return "temp/{$fileName}";
    }
    public function getOrganizationRegisterFolder($organization_id, $agreement_id, $check_list_id)
    {
        return storage_path() . "/app/private/organizations/organization_{$organization_id}/register/agreement_{$agreement_id}/checlist_{$check_list_id}/";
    }
    public function getOrganizationRegisterDBPath($organization_id, $agreement_id, $check_list_id, $fileName)
    {
        return "organizations/organization_{$organization_id}/register/agreement_{$agreement_id}/checlist_{$check_list_id}/" . $fileName;
    }
    public function getOrganizationApprovalFolder($organization_id, $agreement_id)
    {
        return storage_path() . "/app/private/organizations/organization_{$organization_id}/register/agreement_{$agreement_id}/approval/";
    }
    public function getOrganizationApprovalDBPath($organization_id, $agreement_id, $fileName)
    {
        return "organizations/organization_{$organization_id}/register/agreement_{$agreement_id}/approval/" . $fileName;
    }
    public function getProjectRegisterFolder($organization_id, $project_id, $check_list_id)
    {
        return storage_path() . "/app/private/organizations/organization_{$organization_id}/projects/project_{$project_id}/checlist_{$check_list_id}/";
    }
    public function getProjectRegisterDBPath($organization_id, $project_id, $check_list_id, $fileName)
    {
        return "organizations/organization_{$organization_id}/projects/project_{$project_id}/checlist_{$check_list_id}/" . $fileName;
    }
    public function getProjectApprovalFolder($organization_id, $project_id)
    {
        return storage_path() . "/app/private/organizations/organization_{$organization_id}/projects/project_{$project_id}/approval/";
    }
    public function getProjectApprovalDBPath($organization_id, $project_id, $fileName)
    {
        return "organizations/organization_{$organization_id}/projects/project_{$project_id}/approval/" . $fileName;
    }
    public function getScheduleRegisterFolder($schedule_id)
    {
        return storage_path() . "/app/private/schedule/{$schedule_id}/";
    }
    public function getScheduleRegisterDBPath($schedule_id, $fileName)
    {
        return "schedule/{$schedule_id}/" . $fileName;
    }
}
