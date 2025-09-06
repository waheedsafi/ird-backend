<?php

namespace Database\Seeders;

use App\Models\ApprovalType;
use Illuminate\Database\Seeder;
use App\Models\ApprovalTypeTrans;
use App\Enums\Types\ApprovalTypeEnum;

class ApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $type = ApprovalType::create([
            "id" => ApprovalTypeEnum::approved
        ]);
        ApprovalTypeTrans::create([
            "approval_type_id" => $type->id,
            "language_name" => "en",
            "value" => "Approved"
        ]);
        ApprovalTypeTrans::create([
            "approval_type_id" => $type->id,
            "language_name" => "fa",
            "value" => "تایید شده"
        ]);
        ApprovalTypeTrans::create([
            "approval_type_id" => $type->id,
            "language_name" => "ps",
            "value" => "منظور شوی"
        ]);
        $type = ApprovalType::create([
            "id" => ApprovalTypeEnum::pending
        ]);
        ApprovalTypeTrans::create([
            "approval_type_id" => $type->id,
            "language_name" => "en",
            "value" => "Pending"
        ]);
        ApprovalTypeTrans::create([
            "approval_type_id" => $type->id,
            "language_name" => "fa",
            "value" => "در انتظار"
        ]);
        ApprovalTypeTrans::create([
            "approval_type_id" => $type->id,
            "language_name" => "ps",
            "value" => "انتظار کول"
        ]);
        $type = ApprovalType::create([
            "id" => ApprovalTypeEnum::rejected
        ]);
        ApprovalTypeTrans::create([
            "approval_type_id" => $type->id,
            "language_name" => "en",
            "value" => "Rejected"
        ]);
        ApprovalTypeTrans::create([
            "approval_type_id" => $type->id,
            "language_name" => "fa",
            "value" => "رد شده"
        ]);
        ApprovalTypeTrans::create([
            "approval_type_id" => $type->id,
            "language_name" => "ps",
            "value" => "رد شوی"
        ]);
    }
}
