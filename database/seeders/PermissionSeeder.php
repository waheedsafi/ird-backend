<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use App\Enums\Permissions\PermissionEnum;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Icons
        $users = 'icons/users-group.svg';
        $reports = 'icons/chart.svg';
        $configurations = 'icons/configurations.svg';
        $logs = 'icons/logs.svg';
        $audit = 'icons/audits.svg';
        $approval = 'icons/approval.svg';
        $activity = 'icons/activity.svg';
        $about = 'icons/about.svg';
        // App
        $projects = 'icons/projects.svg';
        $organization = 'icons/organization.svg';
        $donor = 'icons/donor.svg';
        $schedules = 'icons/calendar.svg';

        Permission::factory()->create([
            "id" => PermissionEnum::users->value,
            "icon" => $users,
            "name" => 'users',
            "priority" => 1,
        ]);
        Permission::factory()->create([
            "id" => PermissionEnum::about->value,
            "icon" => $about,
            "name" => 'about',
            "group_by" => 'management',
            "priority" => 6,
        ]);
        Permission::factory()->create([
            "id" => PermissionEnum::reports->value,
            "icon" => $reports,
            "name" => 'reports',
            "priority" => 7,
        ]);
        Permission::factory()->create([
            "id" => PermissionEnum::logs->value,
            "icon" => $logs,
            "name" => 'logs',
            "priority" => 8,
        ]);
        Permission::factory()->create([
            "id" => PermissionEnum::audit->value,
            "icon" => $audit,
            "name" => 'audit',
            "priority" => 9,
        ]);
        Permission::factory()->create([
            "id" => PermissionEnum::configurations->value,
            "icon" => $configurations,
            "name" => 'configurations',
            "priority" => 10,
        ]);
        Permission::factory()->create([
            "id" => PermissionEnum::approval->value,
            "icon" => $approval,
            "name" => 'approval',
            "priority" => 11,
        ]);
        Permission::factory()->create([
            "id" => PermissionEnum::activity->value,
            "icon" => $activity,
            "name" => 'activity',
            "priority" => 12,
        ]);
        // App
        Permission::factory()->create([
            "id" => PermissionEnum::organizations->value,
            "icon" => $organization,
            "name" => 'organizations',
            "priority" => 2,
        ]);
        Permission::factory()->create([
            "id" => PermissionEnum::projects->value,
            "icon" => $projects,
            "name" => 'projects',
            "priority" => 3,
        ]);
        Permission::factory()->create([
            "id" => PermissionEnum::donors->value,
            "icon" => $donor,
            "name" => 'donors',
            "priority" => 4,
        ]);
        Permission::factory()->create([
            "id" => PermissionEnum::schedules->value,
            "icon" => $schedules,
            "name" => 'schedules',
            "priority" => 5,
        ]);
    }
}
