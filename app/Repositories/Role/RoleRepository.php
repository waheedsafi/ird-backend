<?php

namespace App\Repositories\Role;

use App\Enums\Permissions\SubPermissionEnum;
use Illuminate\Support\Facades\DB;

class RoleRepository implements RoleRepositoryInterface
{
    public function rolePermissions($role_id)
    {
        return DB::table('role_permissions as rp')
            ->where('rp.role', '=', $role_id)
            ->join('permissions as p', 'rp.permission', '=', 'p.id')
            ->leftJoin('role_permission_subs as rps', 'rps.role_permission_id', '=', 'rp.id')
            ->leftJoin('sub_permissions as sp', 'rps.sub_permission_id', '=', 'sp.id')
            ->whereNotIn('sp.id', [SubPermissionEnum::configurations_role->value, SubPermissionEnum::configurations_application->value]) // Do not allow this only super can have
            ->select(
                'p.id',
                'p.name as permission',
                'sp.name',
                'rp.view',
                'rp.edit',
                'rp.delete',
                'rp.add',
                'p.priority',
                "rps.sub_permission_id",
                "rps.view as sub_view",
                "rps.add as sub_add",
                "rps.edit as sub_edit",
                "rps.delete as sub_delete",
                'sp.name'
            )
            ->orderBy('p.priority')
            ->get();
    }
    public function combineRolesPermssions($role_id_high, $role_id_low)
    {
        return DB::table('role_permissions as r1')
            ->where('r1.role', $role_id_high)
            ->join('permissions as p', 'r1.permission', '=', 'p.id')
            ->leftJoin('role_permissions as r3', function ($join) use (&$role_id_low) {
                $join->on('r3.permission', '=', 'r1.permission')
                    ->where('r3.role', '=', $role_id_low);
            })
            ->leftJoin('role_permission_subs as rps1', 'rps1.role_permission_id', '=', 'r1.id')
            ->leftJoin('role_permission_subs as rps3', function ($join) use (&$role_id_low) {
                $join->on('rps3.sub_permission_id', '=', 'rps1.sub_permission_id')
                    ->whereRaw("rps3.role_permission_id = (SELECT id FROM role_permissions WHERE role = {$role_id_low} AND permission = r1.permission LIMIT 1)");
            })
            ->leftJoin('sub_permissions as sp', 'rps1.sub_permission_id', '=', 'sp.id')
            ->select(
                'p.id',
                'p.name as permission',
                'sp.id as sub_permission_id',
                'sp.name as name',

                DB::raw('COALESCE(r3.view, false) as view'),
                DB::raw('COALESCE(r3.edit, false) as edit'),
                DB::raw('COALESCE(r3.delete, false) as delete'),
                DB::raw('COALESCE(r3.add, false) as add'),

                DB::raw('COALESCE(rps3.view, false) as sub_view'),
                DB::raw('COALESCE(rps3.add, false) as sub_add'),
                DB::raw('COALESCE(rps3.edit, false) as sub_edit'),
                DB::raw('COALESCE(rps3.delete, false) as sub_delete')
            )
            ->orderBy('p.priority')
            ->get();
    }
    public function formatRolePermissions($rolePermissions, $makePermissionFalse = false)
    {
        return $rolePermissions->groupBy('id')->map(function ($group) use (&$makePermissionFalse) {
            $subPermissions = $group->filter(function ($item) {
                return $item->sub_permission_id !== null; // Filter for permissions that have sub-permissions
            });

            $permission = $group->first(); // Get the first permission for this group

            $permission->view = $makePermissionFalse ? false : (bool) $permission->view;
            $permission->edit = $makePermissionFalse ? false : (bool) $permission->edit;
            $permission->delete = $makePermissionFalse ? false : (bool) $permission->delete;
            $permission->add = $makePermissionFalse ? false : (bool) $permission->add;
            if ($subPermissions->isNotEmpty()) {

                $permission->sub = $subPermissions->map(function ($sub) use (&$makePermissionFalse) {
                    return [
                        'id' => $sub->sub_permission_id,
                        'name' => $sub->name,
                        'add' => $makePermissionFalse ? false : (bool) $sub->sub_add,
                        'delete' => $makePermissionFalse ? false : (bool) $sub->sub_delete,
                        'edit' => $makePermissionFalse ? false : (bool) $sub->sub_edit,
                        'view' => $makePermissionFalse ? false : (bool) $sub->sub_view,
                    ];
                });
            } else {
                $permission->sub = [];
            }
            // // If there are no sub-permissions, remove the unwanted fields
            unset($permission->sub_permission_id);
            unset($permission->priority);
            unset($permission->name);

            return $permission;
        })->values();
    }
}
