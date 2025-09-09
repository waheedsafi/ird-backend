<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Models\StatusType;
use Illuminate\Database\Seeder;
use App\Enums\Statuses\StatusEnum;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\StatusTypeEnum;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->statusType();
        $this->status();
    }
    public function statusType()
    {
        StatusType::create([
            'id' => StatusTypeEnum::general,
        ]);
        StatusType::create([
            'id' => StatusTypeEnum::schedule,
        ]);
    }
    public function status()
    {
        $statustype =  Status::create([
            'id' => StatusEnum::active,
            'status_type_id' => StatusTypeEnum::general,
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Active'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'فعال'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'فعال'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::block,
            'status_type_id' => StatusTypeEnum::general,
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Block'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'مسدود'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'مسدود'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::pending,
            'status_type_id' => StatusTypeEnum::general,
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Pending'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'در حال بررسی'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'په تمه'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::rejected,
            'status_type_id' => StatusTypeEnum::general,
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Rejected'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'رد شد'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'رد شوی'
        ]);
        // App
        $statustype =  Status::create([
            'id' => StatusEnum::document_upload_required,
            'status_type_id' => StatusTypeEnum::general,
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Document upload required'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'آپلود مدرک الزامی است'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'د سند اپلوډ اړین دی'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::expired,
            'status_type_id' => StatusTypeEnum::general,
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Expired'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'تمام شده'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'ختم شوی'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::extended,
            'status_type_id' => StatusTypeEnum::general,
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Extended'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'تمدید شده'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'غځول شوی'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::approved,
            'status_type_id' => StatusTypeEnum::general,
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Approved'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'تایید شده'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'منظور شوی'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::registered,
            'status_type_id' => StatusTypeEnum::general,
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Registered'

        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'ثبت شده'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'ثبت شوی'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::registration_incomplete,
            'status_type_id' => StatusTypeEnum::general,
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Registration incomplete'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'ثبت نام ناتمام است.'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'نوم لیکنه نیمګړې ده.'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::pending_for_schedule,
            'status_type_id' => StatusTypeEnum::schedule,
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Pending for schedule'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'منتظر زمانبندی است'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'د مهالویش په تمه ده.'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::has_comment,
            'status_type_id' => StatusTypeEnum::schedule,
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Has comment'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'نظرات دارد'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'تبصره لري'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::scheduled,
            'status_type_id' => StatusTypeEnum::schedule,
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Scheduled'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'زمانبندی شد'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'مهالویش شو'
        ]);
        $statustype =  Status::create([
            'id' => StatusEnum::missed,
            'status_type_id' => StatusTypeEnum::schedule,
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'en',
            'name' => 'Missed'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'fa',
            'name' => 'حاضر نشدن'
        ]);
        DB::table('status_trans')->insert([
            'status_id' => $statustype->id,
            'language_name' => 'ps',
            'name' => 'نه حاضرېدل'
        ]);
    }
}
