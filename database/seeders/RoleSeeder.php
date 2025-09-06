<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleTrans;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\Permissions\RoleEnum;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $item = Role::factory()->create([
            "id" => RoleEnum::super,
        ]);
        RoleTrans::factory()->create([
            "value" => "کاربر فوق العاده",
            "language_name" => "fa",
            "role_id" => $item->id
        ]);
        RoleTrans::factory()->create([
            "value" => "ډېر کاروونکی",
            "language_name" => "ps",
            "role_id" => $item->id
        ]);
        RoleTrans::factory()->create([
            "value" => "Super user",
            "language_name" => "en",
            "role_id" => $item->id
        ]);
        $item = Role::factory()->create([
            "id" => RoleEnum::administrator,
        ]);
        RoleTrans::factory()->create([
            "value" => "مدیر سیستم",
            "language_name" => "fa",
            "role_id" => $item->id
        ]);
        RoleTrans::factory()->create([
            "value" => "د سیسټم مدیر",
            "language_name" => "ps",
            "role_id" => $item->id
        ]);
        RoleTrans::factory()->create([
            "value" => "System administrator",
            "language_name" => "en",
            "role_id" => $item->id
        ]);
        $item = Role::factory()->create([
            "id" => RoleEnum::debugger,
        ]);
        RoleTrans::factory()->create([
            "value" => "دیبگر",
            "language_name" => "fa",
            "role_id" => $item->id
        ]);
        RoleTrans::factory()->create([
            "value" => "ډیبګر",
            "language_name" => "ps",
            "role_id" => $item->id
        ]);
        RoleTrans::factory()->create([
            "value" => "Debugger",
            "language_name" => "en",
            "role_id" => $item->id
        ]);
        $item = Role::factory()->create([
            "id" => RoleEnum::organization,
        ]);
        RoleTrans::factory()->create([
            "value" => "سازمان",
            "language_name" => "fa",
            "role_id" => $item->id
        ]);
        RoleTrans::factory()->create([
            "value" => "سازمان",
            "language_name" => "ps",
            "role_id" => $item->id
        ]);
        RoleTrans::factory()->create([
            "value" => "Organization",
            "language_name" => "en",
            "role_id" => $item->id
        ]);
        $item = Role::factory()->create([
            "id" => RoleEnum::donor,
        ]);
        RoleTrans::factory()->create([
            "value" => "دونر",
            "language_name" => "fa",
            "role_id" => $item->id
        ]);
        RoleTrans::factory()->create([
            "value" => "دونر",
            "language_name" => "ps",
            "role_id" => $item->id
        ]);
        RoleTrans::factory()->create([
            "value" => "Donor",
            "language_name" => "en",
            "role_id" => $item->id
        ]);
        // ✅ Reset sequence to avoid future conflicts
        DB::statement("SELECT setval('roles_id_seq', (SELECT MAX(id) FROM roles))");
    }
}
