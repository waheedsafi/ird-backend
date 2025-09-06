<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\Types\ApplicationEnum;
use App\Enums\Types\PredefinedCommentEnum;
use App\Models\PredefinedComment;
use App\Models\PredefinedCommentTrans;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $type = PredefinedComment::create([
            "id" => PredefinedCommentEnum::organization_user_created,
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "Organization user created successfully.",
            "language_name" => "en",
            "predefined_comment_id" => $type->id
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "کاربر موسسه با موفقیت ایجاد شد.",
            "language_name" => "fa",
            "predefined_comment_id" => $type->id
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "د موسسه یوزر په بریالیتوب سره جوړ شو.",
            "language_name" => "ps",
            "predefined_comment_id" => $type->id
        ]);
        $type = PredefinedComment::create([
            "id" => PredefinedCommentEnum::waiting_for_document_upload,
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "Waiting for document upload.",
            "language_name" => "en",
            "predefined_comment_id" => $type->id
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "منتظر آپلود مدارک هستیم.",
            "language_name" => "fa",
            "predefined_comment_id" => $type->id
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "موږ د اسنادو د پورته کولو په تمه یو.",
            "language_name" => "ps",
            "predefined_comment_id" => $type->id
        ]);
        $type = PredefinedComment::create([
            "id" => PredefinedCommentEnum::document_pending_for_approval,
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "Document is pending for approval.",
            "language_name" => "en",
            "predefined_comment_id" => $type->id
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "سند در انتظار تأیید است.",
            "language_name" => "fa",
            "predefined_comment_id" => $type->id
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "سند د تصویب لپاره په تمه دی.",
            "language_name" => "ps",
            "predefined_comment_id" => $type->id
        ]);
        $type = PredefinedComment::create([
            "id" => PredefinedCommentEnum::signed_documents_are_rejected,
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "Signed Register Form Rejected.",
            "language_name" => "en",
            "predefined_comment_id" => $type->id
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "فرم ثبت امضا شده رد شد.",
            "language_name" => "fa",
            "predefined_comment_id" => $type->id
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "لاسلیک شوی د نوم لیکنې فورمه رد شوه.",
            "language_name" => "ps",
            "predefined_comment_id" => $type->id
        ]);
        $type = PredefinedComment::create([
            "id" => PredefinedCommentEnum::signed_documents_are_approved,
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "Signed Register Form Approved.",
            "language_name" => "en",
            "predefined_comment_id" => $type->id
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "فرم ثبت امضا شده تایید شد.",
            "language_name" => "fa",
            "predefined_comment_id" => $type->id
        ]);
        PredefinedCommentTrans::factory()->create([
            "value" => "لاسلیک شوی د نوم لیکنې فورمه تصویب شوه.",
            "language_name" => "ps",
            "predefined_comment_id" => $type->id
        ]);
    }
}
