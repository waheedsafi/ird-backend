<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\DivisionTrans;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $this->destinations();
    }
    public function destinations(): void
    {
        $divisions = [
            [
                "en" => "Directorate of Information Technology",
                "fa" => "ریاست تکنالوژی معلوماتی",
                "ps" => "د معلوماتي ټکنالوژۍ ریاست",
            ],
            [
                "en" => "General Directorate of Office, Documentation, and Communication",
                "fa" => "ریاست عمومی دفتر٬ اسناد و ارتباط",
                "ps" => "د ارتباطاتو، اسنادو او دفتر لوی ریاست",
            ],
            [
                "en" => "Directorate of Information, Public Relations, and Spokesperson",
                "fa" => "ریاست اطلاعات٬ ارتباط عامه و سخنگو",
                "ps" => "د ارتباطاتو، عامه اړیکو او ویاندویۍ ریاست",
            ],
            [
                "en" => "Directorate of preaching and Guidance",
                "fa" => "ریاست دعوت و ارشاد",
                "ps" => "د ارشاد او دعوت ریاست",
            ],
            [
                "en" => "Directorate of Internal Audit",
                "fa" => "ریاست تفتیش داخلی",
                "ps" => "د داخلي پلتڼې ریاست",
            ],
            [
                "en" => "General Directorate of Supervision and Inspection",
                "fa" => "ریاست عمومی نظارت و بازرسی",
                "ps" => "د نظارت او ارزیابۍ لوی ریاست",
            ],
            [
                "en" => "Directorate of Evaluation, Analysis, and Data Interpretation",
                "fa" => "ریاست ارزیابی ٬ تحلیل و تجزیه ارقام",
                "ps" => "د ارقامو د تحلیل تجزیي او ارزیابۍ ریاست",
            ],
            [
                "en" => "Directorate of Medicine and Food Inspection",
                "fa" => "ریاست نظارت و بازرسی از ادویه و مواد غذایی",
                "ps" => "د خوړو او درملو د نظارت او ارزیابۍ ریاست",
            ],
            [
                "en" => "Directorate of Health Service Delivery Inspection",
                "fa" => "ریاست نظارت و بازرسی ازعرضه خدمات صحی",
                "ps" => "د روغتیايي خدمتونو څخه د نظارت او ارزیابۍ ریاست",
            ],
            [
                "en" => "Directorate of Health Facility Assessment",
                "fa" => "ریاست بررسی از تاسیسات صحی",
                "ps" => "د روغتیايي تاسیساتو د څېړنې ریاست",
            ],
            [
                "en" => "Directorate of International Relations, Coordination, and Aid Management",
                "fa" => "ریاست روابط بین المللی٬ هماهنگی وانسجام کمکها",
                "ps" => "ریاست روابط بین المللی٬ هماهنگی وانسجام کمکها",
            ],
            [
                "en" => "General Directorate of the Medical Council",
                "fa" => "ریاست عمومی شورای طبی",
                "ps" => "د طبي شورا لوی ریاست",
            ],
            [
                "en" => "Directorate of Medical Ethics and Standards Promotion",
                "fa" => "ریاست اخلاق طبابت و ترویج استندرد ها",
                "ps" => "د معیارونو د پلي کولو او طبي اخلاقو ریاست",
            ],
            [
                "en" => "Directorate of Regulation for Nurses, Midwives, and Other Medical Personnel",
                "fa" => "ریاست تنظیم امور نرسها٬قابله ها وسایر پرسونل طبی",
                "ps" => "د نرسانو، قابله ګانو او ورته نورو طبي کارکوونکو د چارو د ترتیب ریاست",
            ],
            [
                "en" => "Directorate of Licensing and Registration for Doctors and Health Personnel",
                "fa" => "ریاست ثبت و صدور جواز فعالیت امور دوکتوران و سایر پرسونل صحی",
                "ps" => "د روغتیايي کارکوونکو او ورته نور طبي پرسونل د فعالیت جوازونو د ثبت او صدور ریاست",
            ],
            [
                "en" => "Directorate of Provincial Health Coordination",
                "fa" => "ریاست هماهنگی صحت ولایات",
                "ps" => "د ولایتونو د روغتیا همغږۍ ریاست",
            ],
            [
                "en" => "General Directorate of Curative Medicine",
                "fa" => "ریاست عمومی طب معالجوی",
                "ps" => "د معالجوي طب لوی ریاست",
            ],
            [
                "en" => "Directorate of Diagnostic Services",
                "fa" => "ریاست خدمات تشخیصیه",
                "ps" => "د تشخیصیه خدماتو ریاست",
            ],
            [
                "en" => "Directorate of National Addiction Treatment Program",
                "fa" => "ریاست برنامه ملی تداوی معتادین",
                "ps" => "د روږدو درملنې ملي برنامې ریاست",
            ],
            [
                "en" => "General Directorate of Preventive Medicine and Disease Control",
                "fa" => "ریاست عمومی طب وقایه و کنترول امراض",
                "ps" => "د ناروغیو د مخنیوي او کنټرول لوی ریاست",
            ],
            [
                "en" => "Directorate of Primary Health Care (PHC)",
                "fa" => "ریاست مراقبتهای صحی اولیه PHC",
                "ps" => "د روغتیا لومړنیو پاملرنو ریاست PHC",
            ],
            [
                "en" => "Directorate of Environmental Health",
                "fa" => "ریاست صحت محیطی",
                "ps" => "د چاپیریال روغتیا ریاست",
            ],
            [
                "en" => "Directorate of Infectious Disease Control",
                "fa" => "ریاست کنترول امراض ساری",
                "ps" => "د ساري ناروغیو د کنټرول ریاست",
            ],
            [
                "en" => "Directorate of Mobile Health Services",
                "fa" => "ریاست مراقبت های صحی سیار",
                "ps" => "د ګرځنده روغتیايي خدمتونو ریاست",
            ],
            [
                "en" => "Directorate of Public Nutrition",
                "fa" => "ریاست تغذی عامه",
                "ps" => "د عامه تغذیې ریاست",
            ],
            [
                "en" => "Directorate of Maternal, Newborn, and Child Health",
                "fa" => "ریاست صحت باروری مادر٬ نوزاد و طفل",
                "ps" => "د کوچنیانو، نویو زیږېدلو او بارورۍ روغتیا ریاست",
            ],
            [
                "en" => "Directorate of Forensic Medicine",
                "fa" => "ریاست طب عدلی",
                "ps" => "د عدلي طب ریاست",
            ],
            [
                "en" => "Department of Emergency Management",
                "fa" => "آمریت رسیدگی به حوادث غیرمترقبه",
                "ps" => "ناڅاپي پېښو ته د رسېدنې آمریت",
            ],
            [
                "en" => "Directorate of Private Sector Coordination",
                "fa" => "ریاست تنظیم هماهنگی سکتور خصوصی",
                "ps" => "د خصوصي سکتور د همغږۍ او تنظیم ریاست",
            ],
            [
                "en" => "General Directorate of the National Public Health Institute",
                "fa" => "ریاست عمومی انیستیتوت ملی صحت عامه",
                "ps" => "د عامې روغتیا ملي انسټېټیوټ لوی ریاست",
            ],
            [
                "en" => "Directorate of Public Health Education and Management",
                "fa" => "ریاست آموزش صحت عامه و مدیریت",
                "ps" => "د عامه روغتیايي زده کړو او مدیریت ریاست",
            ],
            [
                "en" => "Directorate of Public Health Research and Clinical Studies",
                "fa" => "ریاست تحقیقات صحت عامه و مطالعات کلینیکی",
                "ps" => "د کلینیکي مطالعاتو او عامې روغتیا د څېړنو ریاست",
            ],
            [
                "en" => "General Directorate of Policy and Planning",
                "fa" => "ریاست عمومی پالیسی و پلان",
                "ps" => "د پلان او پالیسۍ لوی ریاست",
            ],
            [
                "en" => "Directorate of Planning and Strategic Planning",
                "fa" => "ریاست برنامه ریزی و پلانگذاری",
                "ps" => "د برنامه ریزۍ او پلانګزارۍ ریاست",
            ],
            [
                "en" => "Directorate of Health Economics and Funding",
                "fa" => "ریاست اقتصاد و تمویل صحت",
                "ps" => "د روغتیا د تمویل او اقتصاد ریاست",
            ],
            [
                "en" => "Executive Directorate of the National Accreditation Authority for Health Facilities",
                "fa" => "ریاست اجرائیوی اداره ملی اعتبار دهی مراکز صحی",
                "ps" => "د روغتیايي مرکزونو د اعتبار ورکولو ملي ادارې اجرائیوي ریاست",
            ],
            [
                "en" => "Directorate of Public-Private Partnership",
                "fa" => "ریاست مشارکت عامه و خصوصی",
                "ps" => "د خصوصي او عامه مشارکت ریاست",
            ],
            [
                "en" => "Directorate of Protection of Children and Maternal Health Rights",
                "fa" => "ریاست حمایت از حقوق صحی اطفال و مادران",
                "ps" => "د کوچنیانو او مېندو له روغتیايي حقوقو څخه د تمویل ریاست",
            ],
            [
                "en" => "Directorate of Legal Affairs and Legislation",
                "fa" => "ریاست امور حقوقی و تقنین",
                "ps" => "د تقنین او حقوقي چارو ریاست",
            ],
            [
                "en" => "General Directorate of Pharmaceutical and Health Products Regulation",
                "fa" => "ریاست عمومی تنظیم ادویه و محصولات صحی",
                "ps" => "د درملو او روغتیايي محصولاتو د ترتیب لوی ریاست",
            ],
            [
                "en" => "Directorate of Licensing for Pharmaceutical Facilities and Activities",
                "fa" => "ریاست جوازدهی به تاسیسات و فعالیت های دوایی",
                "ps" => "تاسیساتو ته د جوازونو د ورکړې او درملیزو فعالیتونو ریاست",
            ],
            [
                "en" => "Directorate of Drug and Health Product Evaluation and Registration",
                "fa" => "ریاست ارزیابی و ثبت ادویه و محصولات صحی",
                "ps" => "د درملو او روغتیايي محصولاتو د ثبت او څېړنې ریاست",
            ],
            [
                "en" => "Directorate of Pharmaceutical and Health Product Import and Export Regulation",
                "fa" => "ریاست تنطیم صادرات و واردات ادویه ومحصولات صحی",
                "ps" => "د روغتیايي محصولاتو او درملو د صادرولو او وارداتو د تنظیم ریاست",
            ],
            [
                "en" => "General Directorate of Food Safety",
                "fa" => "ریاست عمومی مصؤنیت غذایی",
                "ps" => "د خوړو د ساتلو لوی ریاست",
            ],
            [
                "en" => "Directorate of Food Licensing and Registration",
                "fa" => "ریاست جوازدهی و ثبت مواد غذایی",
                "ps" => "د خوراکي توکو د ثبت او جوازونو ورکولو ریاست",
            ],
            [
                "en" => "Directorate of Food Surveillance, Risk Analysis, and Standards Development",
                "fa" => "ریاست تحلیل خطر سرویلانس مواد غذایی وتدوین استندردها",
                "ps" => "د سرویلانس خطرونو او خوراکي توکو د څېړنو او د معیارونو پلي کولو ریاست",
            ],
            [
                "en" => "Directorate of Document Analysis and Activity Regulation",
                "fa" => "ریاست تحلیل اسناد و تنظیم فعالیت ها",
                "ps" => "د فعالیتونو د تنظیم او د اسنادو د څېړلو ریاست",
            ],
            [
                "en" => "Directorate of Food, Drug, and Health Product Quality Control (Laboratory)",
                "fa" => "ریاست کنترول کیفیت غذا ٬ ادویه و محصولات صحی (لابراتوار)",
                "ps" => "د روغتیا لابراتواري محصولاتو،درملو او خوراکي توکو د کیفیت کنټرول ریاست",
            ],
            [
                "en" => "Directorate of Pharmaceutical Services",
                "fa" => "ریاست خدمات دوایی",
                "ps" => "د درملي خدمتونو ریاست",
            ],
            [
                "en" => "Directorate of Overseas Health Coordination Centers",
                "fa" => "ریاست هماهنگ کننده مراکز صحی خارج از کشور",
                "ps" => "له هېواده بهر روغتیايي مرکزونو د همغږۍ ریاست",
            ],
            [
                "en" => "Directorate of Overseas Health Affairs – Karachi",
                "fa" => "ریاست امور صحی خارج مرز کراچی",
                "ps" => "له هېواده بهر د کراچۍ د روغتیايي چارو ریاست",
            ],
            [
                "en" => "Directorate of Overseas Health Affairs – Peshawar",
                "fa" => "ریاست امورصحی خارج مرز پشاور",
                "ps" => "له هېواده بهر پشاور د روغتیايي چارو ریاست",
            ],
            [
                "en" => "Directorate of Overseas Health Affairs – Quetta",
                "fa" => "ریاست امورصحی خارج مرز کوته",
                "ps" => "له هېواده بهر کوټه د روغتیايي چارو ریاست",
            ],
            [
                "en" => "Directorate of Finance and Accounting",
                "fa" => "ریاست امور مالی و حسابی",
                "ps" => "د مالي او حسابي چارو ریاست",
            ],
            [
                "en" => "Directorate of Procurement",
                "fa" => "ریاست تدارکات",
                "ps" => "د تدارکاتو ریاست",
            ],
            [
                "en" => "Directorate of Administration",
                "fa" => "ریاست اداری",
                "ps" => "اداري ریاست",
            ],
            [
                "en" => "General Directorate of Human Resources",
                "fa" => "ریاست عمومی منابع بشری",
                "ps" => "د بشري سرچینو لوی ریاست",
            ],
            [
                "en" => "Directorate of Capacity Building",
                "fa" => "ریاست ارتقای ظرفیت",
                "ps" => "د ظرفیت لوړلو ریاست",
            ],
            [
                "en" => "Directorate of Prof. Ghazanfar Institute of Health Sciences",
                "fa" => "ریاست انیستیتوت علوم صحی پوهاند غضنفر",
                "ps" => "د پوهاند غنضنفر روغتیايي علومو انسټېټیوټ ریاست",
            ],
            [
                "en" => "Directorate of Private Health Sciences Institutes",
                "fa" => "ریاست انیستیتوت های علوم صحی خصوصی",
                "ps" => "د خصوصي روغتیايي علومو انسټېټیوټونو ریاست",
            ],
            [
                "en" => "General Directorate of Specialty",
                "fa" => "ریاست عمومی اکمال تخصص",
                "ps" => "د اکمال تخصص لوی ریاست",
            ],
            [
                "en" => "Directorate of Operations",
                "fa" => "ریاست عملیاتی",
                "ps" => "عملیاتي ریاست",
            ],
            [
                "en" => "Directorate of Academic Coordination",
                "fa" => "ریاست امور انسجام اکادمیک",
                "ps" => "د اکاډمیکو چارو د انسجام ریاست",
            ],
        ];
        foreach ($divisions as $division) {
            $item = Division::factory()->create([]);
            DivisionTrans::factory()->create([
                'division_id' => $item->id,
                'value' => $division['en'],
                'language_name' => 'en',
            ]);
            DivisionTrans::factory()->create([
                'division_id' => $item->id,
                'value' => $division['fa'],
                'language_name' => 'fa',
            ]);
            DivisionTrans::factory()->create([
                'division_id' => $item->id,
                'value' => $division['ps'],
                'language_name' => 'ps',
            ]);
        }
    }
}
