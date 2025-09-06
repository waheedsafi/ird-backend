<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Email;
use App\Models\Contact;
use App\Models\ModelJob;
use App\Models\UserStatus;
use App\Models\ModelJobTrans;
use Illuminate\Database\Seeder;
use App\Enums\Statuses\StatusEnum;
use Illuminate\Support\Facades\DB;
use App\Enums\Permissions\RoleEnum;
use Illuminate\Support\Facades\Hash;
use App\Enums\Languages\LanguageEnum;

class JobAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $job = ModelJob::factory()->create([]);
        ModelJobTrans::factory()->create([
            "value" => "Administrator",
            "model_job_id" => $job->id,
            "language_name" => LanguageEnum::default->value,
        ]);
        ModelJobTrans::factory()->create([
            "value" => "مدیر اجرایی",
            "model_job_id" => $job->id,
            "language_name" => LanguageEnum::farsi->value,
        ]);
        ModelJobTrans::factory()->create([
            "value" => "اجرایی مدیر",
            "model_job_id" => $job->id,
            "language_name" => LanguageEnum::pashto->value,
        ]);

        // 
        $job =  ModelJob::factory()->create([]);
        ModelJobTrans::factory()->create([
            "value" => "Manager",
            "model_job_id" => $job->id,
            "language_name" => LanguageEnum::default->value,
        ]);
        ModelJobTrans::factory()->create([
            "value" => "مدیر",
            "model_job_id" => $job->id,
            "language_name" => LanguageEnum::farsi->value,
        ]);
        ModelJobTrans::factory()->create([
            "value" => "مدیر",
            "model_job_id" => $job->id,
            "language_name" => LanguageEnum::pashto->value,
        ]);

        $contact =  Contact::factory()->create([
            "value" => "+93785764809"
        ]);
        $email =  Email::factory()->create([
            "value" => "super@admin.com"
        ]);
        $debuggerEmail =  Email::factory()->create([
            "value" => "debugger@admin.com"
        ]);

        $user = User::factory()->create([
            "id" => RoleEnum::super->value,
            'full_name' => 'Super User',
            'username' => 'super@admin.com',
            'email_id' =>  $email->id,
            'password' =>  Hash::make("123123123"),
            'role_id' =>  RoleEnum::super,
            'contact_id' =>  $contact->id,
            'job_id' =>  $job->id,
            'division_id' =>  14, // IRD Division
        ]);
        UserStatus::create([
            "user_id" => $user->id,
            "is_active" => true,
            "status_id" => StatusEnum::active->value,
        ]);

        $user = User::factory()->create([
            "id" => RoleEnum::debugger->value,
            'full_name' => 'Sayed Naweed Sayedy',
            'username' => 'debugger@admin.com',
            'email_id' =>  $debuggerEmail->id,
            'password' =>  Hash::make("123123123"),
            'role_id' =>  RoleEnum::debugger,
            'job_id' =>  $job->id,
            'division_id' =>  1, // IT Division
        ]);
        UserStatus::create([
            "user_id" => $user->id,
            "is_active" => true,
            "status_id" => StatusEnum::active->value,
        ]);
        // ✅ Reset sequence to avoid future conflicts
        DB::statement("SELECT setval('users_id_seq', (SELECT MAX(id) FROM users))");
    }
}
