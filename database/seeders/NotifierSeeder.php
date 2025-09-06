<?php

namespace Database\Seeders;

use App\Models\NotifierType;
use Illuminate\Database\Seeder;
use App\Enums\Types\NotifierEnum;
use App\Models\NotifierTypeTrans;
use App\Enums\Languages\LanguageEnum;

class NotifierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $this->notifierType();
    }

    protected function notifierType()
    {
        $type = NotifierType::create([
            "id" => NotifierEnum::confirm_adding_user->value,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => "Confirm adding user",
            'language_name' => LanguageEnum::default,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => 'تایید اضافه کردن کاربر',
            'language_name' => LanguageEnum::farsi,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => "د کارونکي اضافه کول تایید",
            'language_name' => LanguageEnum::pashto,
        ]);
        // App
        $type = NotifierType::create([
            "id" => NotifierEnum::confirm_signed_registration_form->value,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => "Confirm signed registration form",
            'language_name' => LanguageEnum::default,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => 'فرم ثبت نام امضا شده را تأیید کنید',
            'language_name' => LanguageEnum::farsi,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => "لاسلیک شوی د نوم لیکنې فورمه تایید کړئ",
            'language_name' => LanguageEnum::pashto,
        ]);
        $type = NotifierType::create([
            "id" => NotifierEnum::confirm_signed_project_form->value,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => "Confirm signed project form",
            'language_name' => LanguageEnum::default,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => "فرم امضا شده پروژه را تأیید کنید",
            'language_name' => LanguageEnum::farsi,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => "د لاسلیک شوې پروژې فورمه تایید کړئ",
            'language_name' => LanguageEnum::pashto,
        ]);
        $type = NotifierType::create([
            "id" => NotifierEnum::signed_register_form_accepted->value,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => "Signed registration form accepted",
            'language_name' => LanguageEnum::default,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => "فرم ثبت نام امضا شده پذیرفته شد",
            'language_name' => LanguageEnum::farsi,
        ]);
        NotifierTypeTrans::create([
            'notifier_type_id' => $type->id,
            'value' => "لاسلیک شوی د نوم لیکنې فورمه ومنل شوه",
            'language_name' => LanguageEnum::pashto,
        ]);
    }
}
