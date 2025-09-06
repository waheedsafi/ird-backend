<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Gender;
use App\Models\OrganizationType;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\NidType;
use App\Models\Currency;
use App\Models\Language;
use App\Models\NewsType;
use App\Models\Priority;
use App\Models\GenderTrans;
use App\Models\CurrencyTran;
use App\Models\OrganizationTypeTrans;
use App\Models\NidTypeTrans;
use App\Models\NewsTypeTrans;
use App\Models\PriorityTrans;
use Illuminate\Database\Seeder;
use App\Enums\Types\OrganizationTypeEnum;
use App\Enums\Statuses\PriorityEnum;
use App\Models\Organization;

/*
1. If you add new Role steps are:
    1. Add to following:
        - RoleEnum
        - RoleSeeder
        - RolePermissionSeeder (Define which permissions role can access)
        - Optional: Set Role on User go to JobAndUserSeeder Then UserPermissionSeeder


2. If you add new Permission steps are:
    1. Add to following:
        - PermissionEnum
        - SubPermissionEnum (In case has Sub Permissions)
        - PermissionSeeder
        - SubPermissionSeeder Then SubPermissionEnum (I has any sub permissions) 
        - RolePermissionSeeder (Define Which Role can access the permission)
        - Optional: Set Permission on User go to JobAndUserSeeder Then UserPermissionSeeder

        
3. If you add new Sub Permission steps are:
    1. Add to following:
        - SubPermissionEnum
        - SubPermissionSeeder
        - RolePermissionSeeder (Define Which Role can access the permission)
        - Optional: Set Permission on User go to JobAndUserSeeder Then UserPermissionSeeder
*/

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->languages();
        $this->gender();
        $this->call(CountrySeeder::class);
        $this->call(DivisionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(StatusSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(SubPermissionSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(JobAndUserSeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(CheckListSeeder::class);
        $this->call(AboutSeeder::class);
        $this->call(ApprovalSeeder::class);
        $this->call(NotifierSeeder::class);
        $this->call(ApplicationSeeder::class);
        $this->call(CommentSeeder::class);

        // App
        $this->currency();
        $this->organizationTypes();
        $this->newsTypes();
        $this->priorityTypes();
        $this->nidTypes();
    }

    public function languages(): void
    {
        Language::factory()->create([
            "name" => "en"
        ]);
        Language::factory()->create([
            "name" => "ps"
        ]);
        Language::factory()->create([
            "name" => "fa"
        ]);
    }
    protected function gender()
    {
        $item = Gender::factory()->create([]);
        GenderTrans::factory()->create([
            "name" => "مرد",
            "language_name" => "fa",
            "gender_id" => $item->id
        ]);
        GenderTrans::factory()->create([
            "name" => "نارینه",
            "language_name" => "ps",
            "gender_id" => $item->id
        ]);
        GenderTrans::factory()->create([
            "name" => "Male",
            "language_name" => "en",
            "gender_id" => $item->id
        ]);
        $item = Gender::factory()->create([]);
        GenderTrans::factory()->create([
            "name" => "زن",
            "language_name" => "fa",
            "gender_id" => $item->id
        ]);
        GenderTrans::factory()->create([
            "name" => "ښځینه",
            "language_name" => "ps",
            "gender_id" => $item->id
        ]);
        GenderTrans::factory()->create([
            "name" => "Famale",
            "language_name" => "en",
            "gender_id" => $item->id
        ]);
    }
    public function currency()
    {
        $currencies = [
            [
                'abbr' => 'AFN',
                'symbol' => '؋',
                'translations' => [
                    'en' => 'Afghani',
                    'ps' => 'افغانی',
                    'fa' => 'افغانی',
                ],
            ],
            [
                'abbr' => 'USD',
                'symbol' => '$',
                'translations' => [
                    'en' => 'US Dollar',
                    'ps' => 'ډالر',
                    'fa' => 'دالر',
                ],
            ],
            [
                'abbr' => 'EUR',
                'symbol' => '€',
                'translations' => [
                    'en' => 'Euro',
                    'ps' => 'یورو',
                    'fa' => 'یورو',
                ],
            ],
            [
                'abbr' => 'GBP',
                'symbol' => '£',
                'translations' => [
                    'en' => 'Pound',
                    'ps' => 'پوند',
                    'fa' => 'پوند',
                ],
            ],
        ];

        foreach ($currencies as $currency) {
            $curr = Currency::create([
                'abbr' => $currency['abbr'],
                'symbol' => $currency['symbol'],
            ]);

            foreach ($currency['translations'] as $lang => $value) {
                CurrencyTran::create([
                    'currency_id' => $curr->id,
                    'name' => $value,
                    'language_name' => $lang,
                ]);
            }
        }
    }
    public function organizationTypes()
    {
        $international = OrganizationType::factory()->create([
            'id' => OrganizationTypeEnum::International,
        ]);
        OrganizationTypeTrans::factory()->create([
            "value" => "بین المللی",
            "language_name" => "fa",
            "organization_type_id" => $international->id
        ]);
        OrganizationTypeTrans::factory()->create([
            "value" => "نړیوال",
            "language_name" => "ps",
            "organization_type_id" => $international->id
        ]);
        OrganizationTypeTrans::factory()->create([
            "value" => "International",
            "language_name" => "en",
            "organization_type_id" => $international->id
        ]);

        $intergovernmental = OrganizationType::factory()->create([
            'id' => OrganizationTypeEnum::Intergovernmental,

        ]);
        OrganizationTypeTrans::factory()->create([
            "value" => "بین الدولتی",
            "language_name" => "fa",
            "organization_type_id" => $intergovernmental->id
        ]);

        OrganizationTypeTrans::factory()->create([
            "value" => "بین الدولتی",
            "language_name" => "ps",
            "organization_type_id" => $intergovernmental->id
        ]);
        OrganizationTypeTrans::factory()->create([
            "value" => "Intergovernmental",
            "language_name" => "en",
            "organization_type_id" => $intergovernmental->id
        ]);

        $domestic = OrganizationType::factory()->create([
            'id' => OrganizationTypeEnum::Domestic,

        ]);
        OrganizationTypeTrans::factory()->create([
            "value" => "داخلی",
            "language_name" => "fa",
            "organization_type_id" => $domestic->id
        ]);
        OrganizationTypeTrans::factory()->create([
            "value" => "کورني",
            "language_name" => "ps",
            "organization_type_id" => $domestic->id
        ]);
        OrganizationTypeTrans::factory()->create([
            "value" => "Domestic",
            "language_name" => "en",
            "organization_type_id" => $domestic->id
        ]);
    }
    public function newsTypes()
    {
        $newsType = NewsType::create([]);
        NewsTypeTrans::create([
            "value" => "اخبار صحی",
            "language_name" => "fa",
            "news_type_id" => $newsType->id
        ]);
        NewsTypeTrans::create([
            "value" => "روغتیا خبرونه",
            "language_name" => "ps",
            "news_type_id" => $newsType->id
        ]);
        NewsTypeTrans::create([
            "value" => "Health News",
            "language_name" => "en",
            "news_type_id" => $newsType->id
        ]);

        $newsType = NewsType::create([]);
        NewsTypeTrans::create([
            "value" => "اخبار جهان",
            "language_name" => "fa",
            "news_type_id" => $newsType->id
        ]);
        NewsTypeTrans::create([
            "value" => "نړیوال خبرونه",
            "language_name" => "ps",
            "news_type_id" => $newsType->id
        ]);
        NewsTypeTrans::create([
            "value" => "International News",
            "language_name" => "en",
            "news_type_id" => $newsType->id
        ]);
    }
    public function priorityTypes()
    {
        $priority = Priority::create([
            'id' => PriorityEnum::high->value
        ]);
        PriorityTrans::create([
            "value" => "اولویت بالا",
            "language_name" => "fa",
            "priority_id" => $priority->id
        ]);
        PriorityTrans::create([
            "value" => "لوړ لومړیتوب",
            "language_name" => "ps",
            "priority_id" => $priority->id
        ]);
        PriorityTrans::create([
            "value" => "High Priority",
            "language_name" => "en",
            "priority_id" => $priority->id
        ]);
        $priority = Priority::create([
            'id' => PriorityEnum::medium->value
        ]);
        PriorityTrans::create([
            "value" => "اولویت متوسط",
            "language_name" => "fa",
            "priority_id" => $priority->id
        ]);
        PriorityTrans::create([
            "value" => "منځنی لومړیتوب",
            "language_name" => "ps",
            "priority_id" => $priority->id
        ]);
        PriorityTrans::create([
            "value" => "Medium Priority",
            "language_name" => "en",
            "priority_id" => $priority->id
        ]);
        $priority = Priority::create([
            'id' => PriorityEnum::low->value
        ]);
        PriorityTrans::create([
            "value" => "اولویت پایین",
            "language_name" => "fa",
            "priority_id" => $priority->id
        ]);
        PriorityTrans::create([
            "value" => "ټیټ لومړیتوب",
            "language_name" => "ps",
            "priority_id" => $priority->id
        ]);
        PriorityTrans::create([
            "value" => "Low Priority",
            "language_name" => "en",
            "priority_id" => $priority->id
        ]);
    }
    public function nidTypes()
    {
        $nid = NidType::create([]);
        NidTypeTrans::create([
            "value" => "پاسپورت",
            "language_name" => "fa",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "پاسپورټ",
            "language_name" => "ps",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "Passport",
            "language_name" => "en",
            "nid_type_id" => $nid->id
        ]);
        $nid = NidType::create([]);
        NidTypeTrans::create([
            "value" => "تذکره",
            "language_name" => "fa",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "تذکره",
            "language_name" => "ps",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "ID card",
            "language_name" => "en",
            "nid_type_id" => $nid->id
        ]);
    }
}
