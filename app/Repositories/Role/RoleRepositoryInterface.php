<?php

namespace App\Repositories\Role;

interface RoleRepositoryInterface
{
    /**
     * Retrieve Role permissions.
     * 
     *
     * @param string $role_id
     * @return @var \Illuminate\Support\Collection<int, \stdClass|null> $rolePermissions
     */
    public function rolePermissions($role_id);
    /**
     * Retrieve Formatted Role permissions.
     * 
     *
     * @param @var \Illuminate\Support\Collection<int, \stdClass|null> $formattedRolePermissions
     * @param mixed $rolePermissions
     * @param bool $makePermissionFalse
     * @return @var mixed $formattedRolePermissions
     */
    public function formatRolePermissions($rolePermissions, $makePermissionFalse);
    /**
     * Retrieve Formatted Role permissions.
     * 
     *
     * @param @var \Illuminate\Support\Collection<int, \stdClass|null> $formattedRolePermissions
     * @param string $role_id_high
     * @param string $role_id_low
     * @return @var mixed $formattedRolePermissions
     */
    public function combineRolesPermssions($role_id_high, $role_id_low);
}
