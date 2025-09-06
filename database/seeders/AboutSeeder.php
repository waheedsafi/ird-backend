<?php

namespace Database\Seeders;

use App\Models\AboutStaff;
use App\Models\AboutStaffType;
use App\Models\SlideshowTrans;
use App\Models\AboutStaffTrans;
use Illuminate\Database\Seeder;
use App\Models\OfficeInformation;
use App\Enums\Types\AboutStaffEnum;
use App\Models\AboutStaffTypeTrans;
use App\Enums\Languages\LanguageEnum;
use App\Enums\Permissions\RoleEnum;
use App\Models\OfficeInformationTrans;
use App\Models\Slideshow;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private $slideshows = [
        [
            'fa' => [
                'title' => 'مولوي نور جلال جلالي
وزیر صحت عامه',
                'description' => 'ما متعهد به ارائه خدمات صحی معیاری در سراسر کشور هستیم.'
            ],
            'ps' => [
                'title' => 'مولوي نورجلال جلالي

د عامې روغتیا وزیر',
                'description' => "موږ په ټول هېواد کې، معیاري روغتیایي خدمتونو ته ژمن یو."
            ],
            'en' => [
                'title' => 'Mawlawi Noor Jalal Jalali

The Minister of Public Health',
                'description' => "We are committed to delivering quality health services throughout Afghanistan
                "
            ],
            'image' => 'images/slideshows/minister.png'
        ],
        [
            'fa' => [
                'title' => "  ",
                'description' => "ما متعهد به ارائه خدمات صحی معیاری در سراسر کشور هستیم."
            ],
            'ps' => [
                'title' => "د عامې روغتیا وزارت",
                'description' => "د دوامدارې پراختيا په موخه د افغانستان روغتيايي سیستم کې روغتيايي خدمتونو ته د لاسرسي کچې زياتوالی، د خدمتونو د کیفیت ساتل او  د اغیزمنتیا لوړوالی"
            ],
            'en' => [
                'title' => 'Ministry of Public Health',
                'description' => 'Increasing access to health services, maintaining the quality of services and improving health system performance in Afghanistan for achieving sustainable development goals.'
            ],
            'image' => 'images/slideshows/minister1.jpg'
        ],
        [
            'fa' => [
                'title' => "دیدار جناب وزیرصاحب  وزارت صحت عامه با رئیس سازمان جهانی صحت در سوئیس",
                'description' => "در تاریخ ۲۰ جولای ۲۰۲۴، محترم مولوی نور جلال جلالی، وزیر صحت عامه در جریان سفر خود به سوئیس با دکتر تدروس آدهانوم گبریسوس، مدیرکل سازمان جهانی صحت (WHO)، دیدار کرد.
هدف این دیدار افزایش هماهنگی بین وزارت صحت عامه و سازمان‌های صحی بین‌المللی بود که روی مسائلی مانند کنترل بیماری‌های واگیر و ریشه‌ کن ‌سازی فلج اطفال در افغانستان تمرکز داشت.
"
            ],
            'ps' => [
                'title' => "د لینکس پاکټ لارښود: اړین امرونه (د لینکس پاکټ لارښودونه)",
                'description' => 'که تاسو په خپل ورځني کار کې لینکس کاروئ، نو د لینکس پاکټ لارښود د دندې پرمهال یو مناسب حواله ده. دا په بشپړه توګه تازه شوی د شلمې کلیزې نسخه د 200 څخه ډیر لینکس قوماندې تشریح کوي، پشمول د فایل اداره کولو، د بسته بندۍ مدیریت، د نسخې کنټرول، د فایل فارمیټ تبادلو، او نورو لپاره نوي قوماندې.پدې لنډ لارښود کې، لیکوال ډینیل بیرټ د فعالیت له مخې ګروپ شوي خورا ګټور لینکس قوماندې چمتو کوي. که تاسو یو نوی یا تجربه لرونکی کارونکی یاست، دا عملي کتاب د خورا مهم لینکس قوماندې لپاره یو مثالی حواله ده.'
            ],
            'en' => [
                'title' => 'Meeting of the Honorable Minister of Public Health with the Director-General of the World Health Organization in Switzerland',
                'description' => "On July 20, 2024, Honorable Mawlawi Noor Jalal Jalali, Minister of Public Health, during his visit to Switzerland, met with Dr. Tedros Adhanom Ghebreyesus, Director-General of the World Health Organization (WHO).
The purpose of this meeting was to enhance coordination between the Ministry of Public Health and international health organizations, focusing on issues such as controlling infectious diseases and eradicating polio in Afghanistan."
            ],
            'image' => 'images/slideshows/who.jpg'

        ],
    ];
    public function run(): void
    {
        $this->slideshows();
        $this->staffTypes();
        $this->officeInformation();
        $this->manager();
        $this->director();
        $this->technicalSupport();
    }
    public function slideshows()
    {
        foreach ($this->slideshows as $slideshowData) {
            // Save the main slideshow record
            $slideshow = Slideshow::create([
                'image' => $slideshowData['image'],
                'user_id' => RoleEnum::super->value,
            ]);

            // Loop through each language and insert the translations
            foreach (['fa', 'ps', 'en'] as $langCode) {

                SlideshowTrans::create([
                    'slideshow_id' => $slideshow->id,
                    'language_name' => $langCode,
                    'title' => $slideshowData[$langCode]['title'],
                    'description' => $slideshowData[$langCode]['description'],
                ]);
            }
        }
    }
    public function staffTypes()
    {
        $item = AboutStaffType::factory()->create([
            'id' => AboutStaffEnum::manager,
        ]);
        AboutStaffTypeTrans::factory()->create([
            "value" => "مدیر",
            "language_name" => "fa",
            "about_staff_type_id" => $item->id
        ]);
        AboutStaffTypeTrans::factory()->create([
            "value" => "مدیر",
            "language_name" => "ps",
            "about_staff_type_id" => $item->id
        ]);
        AboutStaffTypeTrans::factory()->create([
            "value" => "Manager",
            "language_name" => "en",
            "about_staff_type_id" => $item->id
        ]);
        $item = AboutStaffType::factory()->create([
            'id' => AboutStaffEnum::director,
        ]);
        AboutStaffTypeTrans::factory()->create([
            "value" => "رئیس",
            "language_name" => "fa",
            "about_staff_type_id" => $item->id
        ]);
        AboutStaffTypeTrans::factory()->create([
            "value" => "رئیس",
            "language_name" => "ps",
            "about_staff_type_id" => $item->id
        ]);
        AboutStaffTypeTrans::factory()->create([
            "value" => "Director",
            "language_name" => "en",
            "about_staff_type_id" => $item->id
        ]);

        $item = AboutStaffType::factory()->create([
            'id' => AboutStaffEnum::technical_support,
        ]);
        AboutStaffTypeTrans::factory()->create([
            "value" => "پشتیبانی فنی",
            "language_name" => "fa",
            "about_staff_type_id" => $item->id
        ]);
        AboutStaffTypeTrans::factory()->create([
            "value" => "پشتیبانی فنی",
            "language_name" => "ps",
            "about_staff_type_id" => $item->id
        ]);
        AboutStaffTypeTrans::factory()->create([
            "value" => "Technical Support",
            "language_name" => "en",
            "about_staff_type_id" => $item->id
        ]);
    }
    public function officeInformation()
    {
        $item = OfficeInformation::factory()->create([
            "contact" => "+93202301374",
            "email" => "info.access@moph.gov.af",
        ]);
        OfficeInformationTrans::factory()->create([
            "value" => "چهار راهی صحت عامه، جاده وزیرمحمد اکبر خان، کابل، افغانستان",
            "language_name" => "fa",
            "office_information_id" => $item->id
        ]);
        OfficeInformationTrans::factory()->create([
            "value" => "د عامې روغتيا څلور لارې، د وزیرمحمد اکبر خان سرک ، کابل، افغانستان",
            "language_name" => "ps",
            "office_information_id" => $item->id
        ]);
        OfficeInformationTrans::factory()->create([
            "value" => "Sehat-e-Ama Square, Wazir Akbar khan Road, Kabul, Afghanistan",
            "language_name" => "en",
            "office_information_id" => $item->id
        ]);
    }
    public function manager()
    {
        $item = AboutStaff::factory()->create([
            "about_staff_type_id" => AboutStaffEnum::manager,
            "contact" => "+93700591273",
            "email" => "Fazalhadi980@gmail.com",
            "profile" => 'images/technical/manager.jpg',
        ]);
        AboutStaffTrans::factory()->create([
            "name" => "فضل هادی دورانی(مدیر)",
            "language_name" => "fa",
            "about_staff_id" => $item->id
        ]);
        AboutStaffTrans::factory()->create([
            "name" =>  "فضل هادی دورانی (مدیر)",
            "language_name" => "ps",
            "about_staff_id" => $item->id
        ]);
        AboutStaffTrans::factory()->create([
            "name" => "Fazel Handi Durani (Manager)",
            "language_name" => "en",
            "about_staff_id" => $item->id
        ]);
    }
    public function director()
    {
        $item = AboutStaff::factory()->create([
            "about_staff_type_id" => AboutStaffEnum::director,
            "contact" => "+93766933024",
            "email" => "Mansoorikabul@gmail.com",
            "profile" => 'images/technical/director.jpg',
        ]);
        AboutStaffTrans::factory()->create([
            "name" => "مولوی محمد داوود منصوری(رئیس)",
            "language_name" => "fa",
            "about_staff_id" => $item->id
        ]);
        AboutStaffTrans::factory()->create([
            "name" => "مولوی محمد داوود منصوری(رئیس)",
            "language_name" => "ps",
            "about_staff_id" => $item->id
        ]);
        AboutStaffTrans::factory()->create([
            "name" => "Mawlavi Mohammad Dawood Mansoori (Director)",
            "language_name" => "en",
            "about_staff_id" => $item->id
        ]);
    }
    public function technicalSupport()
    {
        // 1.
        $item = AboutStaff::factory()->create([
            "about_staff_type_id" => AboutStaffEnum::technical_support,
            "contact" => "+93785764809",
            "email" => "sayednaweedsayedy@gmail.com",
            "profile" => 'images/technical/sayed_naweed.jpg',
        ]);
        AboutStaffTrans::factory()->create([
            "name" => "سید نوید (توسعه دهنده نرم افزار)",
            "language_name" => "fa",
            "about_staff_id" => $item->id
        ]);
        AboutStaffTrans::factory()->create([
            "name" => "سید نوید (د سافټویر جوړونکی)",
            "language_name" => "ps",
            "about_staff_id" => $item->id
        ]);
        AboutStaffTrans::factory()->create([
            "name" => "Sayed Naweed (Software Developer)",
            "language_name" => "en",
            "about_staff_id" => $item->id
        ]);
        // 2.
        $item = AboutStaff::factory()->create([
            "about_staff_type_id" => AboutStaffEnum::technical_support,
            "contact" => "+93773757829",
            "email" => "jalalbakhti@gmail.com",
            "profile" => 'images/technical/jalal.jpg',
        ]);
        AboutStaffTrans::factory()->create([
            "name" => " جلال بختی (توسعه دهنده نرم افزار)",
            "language_name" => "fa",
            "about_staff_id" => $item->id
        ]);
        AboutStaffTrans::factory()->create([
            "name" => " جلال بختی (د سافټویر جوړونکی)",
            "language_name" => "ps",
            "about_staff_id" => $item->id
        ]);
        AboutStaffTrans::factory()->create([
            "name" => "Jalal Bakhti (Software Developer)",
            "language_name" => "en",
            "about_staff_id" => $item->id
        ]);
        //3
        $item = AboutStaff::factory()->create([
            "about_staff_type_id" => AboutStaffEnum::technical_support,
            "contact" => "+93767028775",
            "email" => "wahidsafi.198@gmial.com",
            "profile" => 'images/technical/waheed.jpg',
        ]);
        AboutStaffTrans::factory()->create([
            "name" => "وحیدالله صافی (توسعه دهنده نرم افزار)",
            "language_name" => "fa",
            "about_staff_id" => $item->id
        ]);
        AboutStaffTrans::factory()->create([
            "name" => "وحیدالله صافی (د سافټویر جوړونکی)",
            "language_name" => "ps",
            "about_staff_id" => $item->id
        ]);
        AboutStaffTrans::factory()->create([
            "name" => "Waheedullah Safi(Software Developer)",
            "language_name" => "en",
            "about_staff_id" => $item->id
        ]);
        //4.
        $item = AboutStaff::factory()->create([
            "about_staff_type_id" => AboutStaffEnum::technical_support,
            "contact" => "+93787059015",
            "email" => "imrankhanorya@gmail.com",
            "profile" => 'images/technical/imran.jpg',
        ]);
        AboutStaffTrans::factory()->create([
            "name" => "عمران اوریا (توسعه دهنده نرم افزار)",
            "language_name" => "fa",
            "about_staff_id" => $item->id
        ]);
        AboutStaffTrans::factory()->create([
            "name" => " عمران اوریا (د سافټویر جوړونکی)",
            "language_name" => "ps",
            "about_staff_id" => $item->id
        ]);
        AboutStaffTrans::factory()->create([
            "name" => "Imran Orya (Software Developer)",
            "language_name" => "en",
            "about_staff_id" => $item->id
        ]);
    }
}
