<?php

namespace Database\Seeders;

use App\Models\RolePermission;
use Illuminate\Database\Seeder;
use App\Models\RolePermissionSub;
use App\Enums\Permissions\RoleEnum;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Models\SubPermission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->superPermissions();
        $this->administratorPermissions();
        $this->debuggerPermissions();
        $this->organizationPermissions();
    }
    public function superPermissions()
    {
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => PermissionEnum::users->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::USERS, $rolePer->id);

        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => PermissionEnum::configurations->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::CONFIGURATIONS, $rolePer->id);

        RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => PermissionEnum::reports->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => PermissionEnum::audit->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);

        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => PermissionEnum::about->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::ABOUT, $rolePer->id);

        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => PermissionEnum::approval->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::APPROVALS, $rolePer->id);

        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => PermissionEnum::activity->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::ACTIVITY, $rolePer->id);
        // App
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => PermissionEnum::organizations->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::ORGANIZATIONS, $rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => PermissionEnum::donors->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::DONOR, $rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => PermissionEnum::schedules->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => PermissionEnum::projects->value,
            'edit' => false,
            'delete' => false,
            'add' => false,
            'view' => true,
        ]);
        foreach (SubPermissionEnum::PROJECT as $id => $meta) {
            // This permssion belongs to IT
            RolePermissionSub::factory()->create([
                "edit" => false,
                "delete" => false,
                "add" => false,
                "view" => true,
                "is_category" => $meta['is_category'],
                "role_permission_id" =>  $rolePer->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function administratorPermissions()
    {
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::administrator,
            "permission" => PermissionEnum::users->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::USERS, $rolePer->id);

        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::administrator,
            "permission" => PermissionEnum::configurations->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::CONFIGURATIONS, $rolePer->id);

        RolePermission::factory()->create([
            "role" => RoleEnum::administrator,
            "permission" => PermissionEnum::reports->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);

        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::administrator,
            "permission" => PermissionEnum::about->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::ABOUT, $rolePer->id);

        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::administrator,
            "permission" => PermissionEnum::approval->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::APPROVALS, $rolePer->id);

        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::administrator,
            "permission" => PermissionEnum::activity->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::ACTIVITY, $rolePer->id);
        // App
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::administrator,
            "permission" => PermissionEnum::organizations->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::ORGANIZATIONS, $rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::administrator,
            "permission" => PermissionEnum::donors->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::DONOR, $rolePer->id);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::administrator,
            "permission" => PermissionEnum::schedules->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::administrator,
            "permission" => PermissionEnum::projects->value,
            'edit' => true,
            'delete' => true,
            'add' => false,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::PROJECT, $rolePer->id);
    }
    public function debuggerPermissions()
    {
        RolePermission::factory()->create([
            "role" => RoleEnum::debugger,
            "permission" => PermissionEnum::logs->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::debugger,
            "permission" => PermissionEnum::about->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        RolePermissionSub::factory()->create([
            "edit" => true,
            "delete" => true,
            "add" => true,
            "view" => true,
            "is_category" => true,
            "role_permission_id" => $rolePer->id,
            "sub_permission_id" => SubPermissionEnum::about_technical->value,
        ]);
    }
    public function organizationPermissions()
    {
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::organization,
            "permission" => PermissionEnum::projects->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::PROJECT, $rolePer->id);

        RolePermission::factory()->create([
            "role" => RoleEnum::organization,
            "permission" => PermissionEnum::reports->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::organization,
            "permission" => PermissionEnum::organizations->value,
            'edit' => false,
            'delete' => false,
            'add' => false,
            'view' => true,
            'visible' => false
        ]);
        foreach (SubPermissionEnum::ORGANIZATIONS as $id => $meta) {
            // Do not allow these permissions for organization
            if ($id == SubPermissionEnum::organizations_status->value)
                continue;
            else if ($id == SubPermissionEnum::organizations_account_password->value)
                continue;
            RolePermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "is_category" => $meta['is_category'],
                "role_permission_id" => $rolePer->id,
                "sub_permission_id" => $id,
            ]);
        }

        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => PermissionEnum::projects->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
        $this->addSubPermissions(SubPermissionEnum::PROJECT, $rolePer->id);
    }
    public function donorPermissions()
    {
        $rolePer = RolePermission::factory()->create([
            "role" => RoleEnum::donor,
            "permission" => PermissionEnum::projects->value,
            'edit' => false,
            'delete' => false,
            'add' => false,
            'view' => false,
        ]);

        foreach (SubPermissionEnum::PROJECT as $id => $meta) {
            RolePermissionSub::factory()->create([
                "edit" => false,
                "delete" => false,
                "add" => false,
                "view" => true,
                "is_category" => $meta['is_category'],
                "role_permission_id" => $rolePer->id,
                "sub_permission_id" => $id,
            ]);
        }

        RolePermission::factory()->create([
            "role" => RoleEnum::donor,
            "permission" => PermissionEnum::reports->value,
            'edit' => true,
            'delete' => true,
            'add' => true,
            'view' => true,
        ]);
    }
    private function addSubPermissions(array $group, $role_permission_id)
    {
        foreach ($group as $id => $meta) {
            // This permssion belongs to IT
            if ($id !== SubPermissionEnum::about_technical->value) {
                RolePermissionSub::factory()->create([
                    "edit" => true,
                    "delete" => true,
                    "add" => true,
                    "view" => true,
                    "is_category" => $meta['is_category'],
                    "role_permission_id" => $role_permission_id,
                    "sub_permission_id" => $id,
                ]);
            }
        }
    }
}
