<?php

namespace App\Repositories\User;

interface UserRepositoryInterface
{
    /**
     * Retuns all user permissions.
     * 
     * @param string $role_id
     * @param array $excludedPermissionIds
     * @return mix
     */
    public function userAuthFormattedPermissions($role_id, $excludedPermissionIds = []);
}
