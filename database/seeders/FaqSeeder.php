<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;
use App\Models\FaqTrans;
use App\Models\FaqType;
use App\Models\FaqTypeTrans;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->faqTypes();
    }
    public function faqTypes()
    {
        $item = FaqType::factory()->create([]);
        FaqTypeTrans::factory()->create([
            "value" => "ثبت نام",
            "language_name" => "fa",
            "faq_type_id" => $item->id
        ]);
        FaqTypeTrans::factory()->create([
            "value" => "نوم لیکنه",
            "language_name" => "ps",
            "faq_type_id" => $item->id
        ]);
        FaqTypeTrans::factory()->create([
            "value" => "Registration",
            "language_name" => "en",
            "faq_type_id" => $item->id
        ]);
        $this->registerationQuestions($item->id);
        $item = FaqType::factory()->create([]);
        FaqTypeTrans::factory()->create([
            "value" => "حساب کاربری",
            "language_name" => "fa",
            "faq_type_id" => $item->id
        ]);
        FaqTypeTrans::factory()->create([
            "value" => "د کارونکي حساب",
            "language_name" => "ps",
            "faq_type_id" => $item->id
        ]);
        FaqTypeTrans::factory()->create([
            "value" => "User Account",
            "language_name" => "en",
            "faq_type_id" => $item->id
        ]);
        $this->userAccountQuestions($item->id);
    }
    public function registerationQuestions($id)
    {
        $item = Faq::factory()->create([
            "order" => 1,
            "is_active" => true,
            "faq_type_id" => $id
        ]);
        FaqTrans::factory()->create([
            "question" => "آیا ثبت نام هزینه دارد؟",
            "answer" => "خیر، ثبت نام کاملاً رایگان است. می‌توانید بدون هیچ گونه هزینه یا مخارج پنهانی ثبت نام کنید.",
            "language_name" => "fa",
            "faq_id" => $item->id
        ]);
        FaqTrans::factory()->create([
            "question" => "ایا راجسټریشن لګښتونه لري؟",
            "answer" => "نه، نوم لیکنه په بشپړه توګه وړیا ده. تاسو کولی شئ پرته له کوم فیس یا پټ لګښت څخه نوم لیکنه وکړئ.",
            "language_name" => "ps",
            "faq_id" => $item->id
        ]);
        FaqTrans::factory()->create([
            "question" => "Is registration has charges.",
            "answer" => "No, registration is completely free. You can sign up without any fees or hidden costs.",
            "language_name" => "en",
            "faq_id" => $item->id
        ]);
    }
    public function userAccountQuestions($id)
    {
        $item = Faq::factory()->create([
            "order" => 2,
            "is_active" => true,
            "faq_type_id" => $id
        ]);
        FaqTrans::factory()->create([
            "question" => "حساب کاربری من قفل شده است.",
            "answer" => "ستاسو حساب د هغه فعالیت له امله تړل شوی چې زموږ شرایط سرغړونه کوي. که تاسو فکر کوئ چې دا یوه تېروتنه ده، مهرباني وکړئ د ملاتړ سره اړیکه ونیسئ.",
            "language_name" => "fa",
            "faq_id" => $item->id
        ]);
        FaqTrans::factory()->create([
            "question" => "زما د کارونکي حساب بند دی.",
            "answer" => "حساب شما به دلیل فعالیتی که با شرایت ما مغایرت دارد، قفل شده است. اگر فکر می‌کنید این یک اشتباه است، لطفاً با پشتیبانی تماس بگیرید.",
            "language_name" => "ps",
            "faq_id" => $item->id
        ]);
        FaqTrans::factory()->create([
            "question" => "My account is lock.",
            "answer" => "Your account has been locked due to activity that is inconsistent with our policies. If you believe this is a mistake, please contact support.",
            "language_name" => "en",
            "faq_id" => $item->id
        ]);
        $item = Faq::factory()->create([
            "order" => 3,
            "is_active" => true,
            "faq_type_id" => $id
        ]);
        FaqTrans::factory()->create([
            "question" => "آیا می‌توانم پس از ایجاد حساب کاربری، آدرس ایمیل خود را به‌روزرسانی کنم؟",
            "answer" => "بله، می‌توانید با رفتن به تنظیمات حساب کاربری خود، آدرس ایمیل خود را به‌روزرسانی کنید. پس از انجام تغییر، ممکن است از شما خواسته شود ایمیل جدید را تأیید کنید تا از ایمن ماندن حساب کاربری خود اطمینان حاصل کنید.",
            "language_name" => "fa",
            "faq_id" => $item->id
        ]);
        FaqTrans::factory()->create([
            "question" =>  "ایا زه کولی شم د حساب جوړولو وروسته خپل ایمیل پته تازه کړم؟",
            "answer" => "هو، تاسو کولی شئ د خپل حساب ترتیباتو ته په تګ سره خپل بریښنالیک پته تازه کړئ. د بدلون وروسته، ممکن له تاسو څخه وغوښتل شي چې نوی بریښنالیک تایید کړئ ترڅو ډاډ ترلاسه کړئ چې ستاسو حساب خوندي پاتې کیږي.",
            "language_name" => "ps",
            "faq_id" => $item->id
        ]);
        FaqTrans::factory()->create([
            "question" => "Can I update my email address after creating an account?",
            "answer" => "Yes, you can update your email address by going to your account settings. After making the change, you may be asked to verify the new email to ensure your account stays secure.",
            "language_name" => "en",
            "faq_id" => $item->id
        ]);
    }
}
