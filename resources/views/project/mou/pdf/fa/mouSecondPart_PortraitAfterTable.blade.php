<br>
{!! '<tocentry content="اهداف پروژه" level="3" />' !!}
<h5>
    اهداف پروژه: </h5>
{{ $goals }}
<br>
{!! '<tocentry content=" مقاصد پروژه:" level="3" />' !!}
<h5>

    مقاصد پروژه: </h5>
{{ $objectives }}
<br>
{!! '<tocentry content=" نتایج متوقع:" level="3" />' !!}
<h5>
    نتایج متوقع: </h5>
{{ $expected_outcomes }}
<br>
{!! '<tocentry content="تأثيرات متوقعه:" level="3" />' !!}
<h5>
    تأثيرات متوقعه: </h5>
{{ $expected_impact }}
<br>
</p>

{{-- Article Ten --}}
<p class="content-text" id="mou-section-3">

<div class="article-title">

    <h3>
        مادۀه دهم</h3>

</div>
{!! '<tocentry content="طرح عمومی پروژه" level="3" />' !!}
<h4>
    طرح عمومی پروژه </h4>

{{-- {{ $backgroud_experince }} --}}

<table width="100%" style="border-collapse: collapse; font-size: 13px; margin-top: 20px;">
    <!-- Subject -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black; width: 35%;">موضوع پروژه
        </td>
        <td style="padding: 6px; border: 1px solid black">{{ $subject }}</td>
    </tr>

    <!-- Goals -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">اهداف پروژه</td>
        <td style="padding: 6px; border: 1px solid black">{{ $goals }}</td>
    </tr>

    <!-- Objectives -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">مقاصد پروژه</td>
        <td style="padding: 6px; border: 1px solid black">{{ $objectives }}</td>
    </tr>

    <!-- Main Activities -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">فعالیت هاي اساسی پروژه</td>
        <td style="padding: 6px; border: 1px solid black">{!! nl2br(e($activities)) !!}</td>
    </tr>

    <!-- Implementing Organization -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">موسسه تطبیق کننده</td>
        <td style="padding: 6px; border: 1px solid black">{{ $implementing_org }}</td>
    </tr>

    <!-- Funded by -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">نهاد تمويل کننده</td>
        <td style="padding: 6px; border: 1px solid black">{{ $funder }}</td>
    </tr>

    <!-- Total Budget -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">مقدارمجموعي بودجه</td>
        <td style="padding: 6px; border: 1px solid black">{{ $budget }}</td>
    </tr>

    <!-- Duration (Nested with border) -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; vertical-align: top; border: 1px solid black">
            مدت پروژه</td>
        <td style="padding: 0px; border: 1px solid black">
            <table width="100%" style="border-collapse: collapse; font-size: 13px; border: 1px solid black">
                <tr>
                    <td style="padding: 6px; border: 1px solid black width: 30%;"><strong>تاریخ آغاز:</strong></td>
                    <td style="padding: 6px; border: 1px solid black">{{ $start_date }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px; border: 1px solid black;"><strong>تاریخ ختم:</strong></td>
                    <td style="padding: 6px; border: 1px solid black;">{{ $end_date }}</td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- MOU Date -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">تاریخ امضآ تفاهم نام
        </td>
        <td style="padding: 6px; border: 1px solid black">{{ $mou_date }}</td>
    </tr>

    <!-- Location -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">موقعیت پروژه</td>
        <td style="padding: 6px; border: 1px solid black">{{ $location }}</td>
    </tr>

    <!-- Provinces covered -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">ولایات مربوطه</td>
        <td style="padding: 6px; border: 1px solid black">{{ $provinces }}</td>
    </tr>

    <!-- Areas covered -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">موقعیت ها٬ قریه ها و ولسوالی های
            تحت پوشش</td>
        <td style="padding: 6px; border: 1px solid black">{{ $areas }}</td>
    </tr>

    <!-- Beneficiaries -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">تعداد مستفدين مستقیم</td>
        <td style="padding: 6px; border: 1px solid black">{{ $direct_beneficiaries }}</td>
    </tr>
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">تعداد مستفدين غیر مستقیم </td>
        <td style="padding: 6px; border: 1px solid black">{{ $indirect_beneficiaries }}</td>
    </tr>

    <!-- Project Staff (Nested with border) -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; vertical-align: top; border: 1px solid black">
            تعداد كارمندان پروژه با تفکیک ولایات </td>
        <td style="padding: 1px; border: 1px solid black">
            <table width="100%" style="border-collapse: collapse; font-size: 13px; border: 1px solid black">
                <tr>
                    <td style="padding: 6px; border: 1px solid black width: 40%;"><strong>ساختار اداری
                            :</strong></td>
                    <td style="padding: 6px; border: 1px solid black">{{ $org_structure }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px; border: 1px solid black"><strong>صحی (نوعیت):</strong></td>
                    <td style="padding: 6px; border: 1px solid black">{{ $health_staff }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px; border: 1px solid black"><strong>اداری و مالی:</strong></td>
                    <td style="padding: 6px; border: 1px solid black">{{ $admin_staff }}</td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Action Plan -->
    <tr>
        <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">پلان عملیاتی </td>
        <td style="padding: 6px; border: 1px solid black">{!! nl2br(e($action_plan)) !!}</td>
    </tr>
</table>

<br>
{!! '<tocentry content="معلومات تماس:" level="3" />' !!}
<h5>
    معلومات تماس:</h5><br>
{{-- {{ $provision_health_service }} --}}

<table width="100%" style="border-collapse: collapse; font-size: 13px; margin-top: 20px;">
    <!-- Subject -->
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="border: 1px solid black; padding: 8px;">مسؤول موسسه در افغانستان </th>
            <th style="border: 1px solid black; padding: 8px;">مسؤول پروژه فعلی</th>
        </tr>
    </thead>
    <tr>
        <td style=" padding: 6px; border: 1px solid black; ">
            {{ $ngo_director_contact }}
        </td>
        <td style="padding: 6px; border: 1px solid black">{{ $project_focal_point_contact }}</td>
    </tr>

    <!-- Goals -->
    <tr>
        <td style="padding: 6px; border: 1px solid black">
            {{ $ngo_director_contact }}</td>
        <td style="padding: 6px; border: 1px solid black">{{ $project_focal_point_contact }}</td>
    </tr>
</table>
</p>

<p class="content-text" id="mou-section-3">

<div class="article-title">

    <h3>
        مادۀ یازدهم</h3>

</div>
{!! '<tocentry content="تعهدات و مسئولیت های موسسه در قبال اجرای پروژه: " level="3" />' !!}
<h4>
    تعهدات و مسئولیت های موسسه در قبال اجرای پروژه: </h4>

1: موسسه مکلف است تا طرح مفصل/پلان کاری پروژه را قبل از آغاز اجرای فعالیت های آن به کمیته موظف که توسط وزارت صحت
عامه رهبری می شود تقدیم کند.
<br>
2: موسسه مسؤلیت کامل اجراء و تطبیق درست فعالیت هاي پروگرام را كه در تفاهم نامه ذکر است، ميداشته باشد. موسسه با اجراء
كار به شکل شفاف و حسابده موافق بوده و با استفاده از منابع كه برای تطبیق فعالیت ها لازمي میباشد متعهد است. علاوتا
موسسه متعهد است تا منابع لازم و کافی مالی٬ بشری و غیره را برای تطبیق فعالیت های پروژه و بدست آوردن اهداف و مقاصد
خویش فراهم نماید.
<br>

3: موسسه مکلف است كه تمام فعالیت هاي خويش را مطابق به پاليسي ها و استراتیژی هاي وزارت صحت عامه، قوانین و معیارات
کشور آغاز و تطبيق نمايد.
<br>

4: موسسه مسؤلیت دارد كه به اساس معیارات سیستم معلومات صحی وزارت صحت عامه، گزارش فعالیت های صحی ومالی پروژه خويش را
تهیه داشته و بصورت ربعوار، شش ماه و سالانه به دیپارتمنت هاي مربوطه وزارت صحت عامه و ریاست صحت عامه ولايت (ررررر)
ارسال نماید.
<br>

5: موسسه موافق است تا وزارت صحت عامه ازتمام فعاليت های پروژه و پلان کاری موسسه متذكره در جريان قرار داد، نظارت و
ارزيابی نمايد.
<br>

6: موسسه موافق است که تمام فعالیت های خویش را به صورت رایگان برای مستفدین انجام دهد.
<br>

7: موسسه تمام فعالیت خويش را با هماهنگی ریاست صحت عامه ولايت (رررر) انجام داده٬ در مجالس مربوطه هماهنگی اشتراک نموده
و همچنان از همكاري ها و راهنمايي هاي آن ریاست در صورت ضرورت استفاده خواهد نمود.
<br>

8: به اساس پالیسی های وزارت صحت عامه، نتایج پروژه که توسط موسسه (رررر) انجام میشود در ختم پروژه به وزارت صحت عامه و
ریاست صحت عامه ولایت (رررر) راپور داده خواهد شد.
<br>

9: موسسه مکلفیت دارد تا اسناد داکتران ، نرس ها ، قابله ها و پرسونل صحی را که در بخش صحت با موسسه همکاری دارند بعد از
تایید ریاست صحت عامه ولایات، ریاست بررسی از تطبیق قوانین صحی و ریاست عمومی منابع بشری وزارت صحت عامه عرضه خدمات صحی
نماید در صورت عدم تایید اسناد متذکره مسوولیت متوجه موسسه می باشد.
<br>

10: موسسه مسولیت دارد که در جریان نظارت و ارزیابی از پروژه های ان موسسه امکانات رفت و برگشت و دیگر ضروریات را برای
هییت بررسی کننده وزارت صحت عامه مساعد نماید.

</p>

{{-- article twelve  --}}
<p class="content-text" id="mou-section-3">

<div class="article-title">

    <h3>
        مادۀ دوازدهم</h3>

</div>
{!! '<tocentry content="تعهدات و مسئولیت های وزارت صحت عامه در قبال موسسه و پروژه:" level="3" />' !!}
<h4>
    تعهدات و مسئولیت های وزارت صحت عامه در قبال موسسه و پروژه: </h4>

1: وزارت صحت عامه در مواقع نیاز عنوانی نهاد درخواست کننده تاييد خواهد كرد كه ( ) به عنوان يك نهاد شناخته شده با هماهنگي
وزارت صحت عامه فعالیت مینماید.
<br>
2: ریاست صحت عامه ولايت مربوطه حق نظارت و ارزيابي از كيفيت پروژه و فعاليت هاي موسسه را در جريان تفاهم نامه دارد.
<br>

3: وزارت صحت عامه بعد از امضا تفاهم نامه و اسناد مرتبط، ریاست صحت عامه ولايت ررررر را در جريان قرار خواهد داد تا با
موسسه متذکره در قسمت تطبيق درست فعاليت هاي پروژه همكاري همه جانبه نمايد.
<br>

4: در صورت عدم مطابقت فعاليت موسسه مذكور با مواد تصریح شده فوق، وزارت صحت عامه صلاحیت فسخ اين تفاهم نامه را در هر مقطع
زمانی دارد.

</p>

{{-- article thirteen --}}
<p class="content-text" id="mou-section-3">

<div class="article-title">

    <h3>
        مادۀ سیزدهم</h3>

</div>
{!! '<tocentry content="  تعهدات و مسئولیت های جانبین:" level="3" />' !!}
<h4>
    تعهدات و مسئولیت های جانبین: </h4>
1: وزارت صحت عامه و موسسه تطبیق کننده اصول مساوات ، شفافیت و عدم تبعیض را در مطابقت به نفوذ قومی، جنسی، مذهبي و دیگر
فكتور ها رعایت خواهند کرد.
<br>
<p style="color: red">
    2: در صورتیکه موسسه نتواند تعهدات خویش راعملی نماید وزارت صحت عامه فسخ یک جانبه تفاهم نامه و موضوع را ازجانب عدلی و
    قضایی تعقیب خواهد کرد.
</p>
وزارت صحت عامه و موسسه موافقت می نمایند تا برای بدست آوردن اهداف خویش و برای تطبیق فعالیت های این تفاهم نامه تشریک مساعی
نموده و رابطه نزدیک کاری می داشته باشند.
<br>
3: هر دو جانب تعهد به همکاری جهت انجام فعالیت های با کیفیت عالی برای تطبیق پالیسی و پروگرام های که هدف آن بهبود کیفیت
عرضه خدمات صحی میباشد می نمایند.
<br>
4: هردو طرف معلومات تخنیکی را بدون کدام محدودیت از طریق مجالس، مباحثات رسمی و هماهنگی شریک خواهند ساخت.
<br>

5: هر دو جانب بالای تشکیل یک کمیته موظف که توسط نماینده وزارت صحت عامه رهبری می شود توافق نموده تا بالای پلان عملیاتی
پروژه موافقت نموده و از تطبیق فعالیت های پروژه و پلان های آینده آن نظارت و بازدید کنند.
<br>
6: هر دو جانب بالای حفظ و نگهداشت معلومات٬ ارقام و اسناد بدست آمده از تطبیق فعالیت های ذکر شده در این تفاهم نامه توافق
می نمایند.
<br>
7: معلومات ارایه شده از یک جانب به جانب دیگر طی این تفاهم نامه بصورت محرم نگهداری شده مگر اینکه این معلومات عام بوده و
یا در مورد آن از قبل دانسته شود. تمام اهتمامات از هر دو جانب در جهت نگهداری معلومات گرفته خواهد شد و تنها برای مقصدی که
ارایه شده است استفاده می شود.
<br>
8: در صورت كه فعاليت هاي عمومی موسسه مذکوردرافغانستان خاتمه پیدا ميكند طبق قانون موسسات تمام وسايل، تجهیزات وامکانات
موسسه به دولت افغانستان سپرده خواهد شد.
<br>
<p style="color:red">
    9: اختلافات طي مجالس کمیته هماهنگي صحي ولایتی و یا ذریعه نماینده گان مرکز وزارت صحت عامه حل و فصل میگردد. این تفاهم
    نامه به مدت تکمیل پروژه به مدت یک سال قابل اعتبار بوده وزارت صحت عامه حق تعدیل و فسخ یک جانبه ان را دارد. درصورتیکه
    موسسه بتواند تمام تعهدات اش را عملی نماید وقابل قبول وزارت صحت عامه قرارگیرد قابل تمدید میباشد.

</p>

<p class="content-text" id="mou-section-3">

<div class="article-title">

    <h3>
        مادۀ چهاردهم</h3>

</div>
{!! '<tocentry content=" تاریخ اجراء و فسخ تفاهم نامه:" level="3" />' !!}
<h4>
    تاریخ اجراء و فسخ تفاهم نامه:
</h4>
تفاهم نامه زمانيكه توسط نمايندۀ موسسه و نمايندۀ وزارت صحت عامه امضاء گردید از تاریخ امضاء قابل اجراء مي باشد. با امضاء
خويش هر يك طرفین اين تفاهم نامه تمام ماده هاي اين تفاهم نامه را قبول نموده وبا تمام تعهدات موجود در آن موافق هستند.
<br>
این تفاهم نامه به اساس توافق تحریری جوانب ذیدخل در صورت لزوم دید طرفین توسط مکتوب تحریری با ذکر دلایل مقنع میتواند تعدیل
و یا فسخ گردد. این تفاهم نامه در دو نسخه اصل به امضاء می رسد که هر نهاد یک کاپی را باخود میداشته باشند.

<br>

مدت اعتبار تفاهمنامه از تاریخ سال (...........) الی ختم ماه (..........) میباشد.

<br>
امضاء این تفاهم نامه توسط اشخاص با صلاحیت که اسماء شان در ذیل ذکر است صورت گرفته است:

</p>

{{-- ds --}}

<div class="page"></div>

<div class="sing-page">
    <div class="irddirector">
        <b> از طرف وزارت صحت عامه</b>
        <br>
        {{ $ird_director }}<br>
        مولوی نورجلال"جلالی"
        سرپرست وزارت صحت عامه

        امضاْ
        ____________________________________
        تاریخ ( / / ):

    </div>
    <br>

    <div class="ngodirector">
        <b>از طرف موسسه</b> <br>
        <br>
        امضاْ
        ____________________________________
        تاریخ ( / / ):

    </div>
</div>

</body>

</html>
