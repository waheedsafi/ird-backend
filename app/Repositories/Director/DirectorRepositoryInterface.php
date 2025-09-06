<?php

namespace App\Repositories\Director;

interface DirectorRepositoryInterface
{
    /**
     * Store organization Director.
     * 
     *
     * @param mix validatedData
     * @param string organization_id
     * @param string agreement_id
     * @param array DocumentsId
     * @param boolean is_active
     * @return App\Models\Director
     */
    public function storeorganizationDirector($validatedData, $organization_id, $agreement_id, $DocumentsId, $is_active, $userable_id, $userable_type);
}
