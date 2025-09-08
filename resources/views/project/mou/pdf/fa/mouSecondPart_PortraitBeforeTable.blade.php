<!DOCTYPE html>
<html lang="en" dir="rtl">

    <head>
        <meta charset="UTF-8">
        <title>Memorandum of Understanding</title>
        <style>
            /* Your existing styles here (min-logo, header, etc.) */
            .min-logo {
                height: 70px;
                width: 70px;
                float: right;
                margin-top: -40px;
            }

            .moph-logo {
                height: 70px;
                width: 70px;
                float: left;
                margin-top: -40px;
            }

            .header-logo {
                height: 50px;
                width: 100%;
                margin-top: 120px;
            }

            .min-logo-div {
                width: 100%;
                text-align: center;
            }

            * {
                margin: 0;
                padding: 0;
            }

            .header-text-cont {
                margin-top: 80px;
            }

            .header-text {
                margin: 0;
            }

            .first-page-text {
                text-align: center;
            }

            .page {
                page-break-after: always;
            }

            .min-contents p {
                font-size: 15px;
            }

            .sing-page {
                width: 100%;
                margin-top: 50px;
                padding-top: 50px;
            }

            .irddirector,
            .ngodirector {
                width: 50%;
                margin-top: 10px;
                height: 50px;
            }

            .irddirector {
                float: left;
            }

            .ngodirector {
                float: right;
            }

            .article-title {
                width: 100%;
                text-align: center;
            }

            .article-title h3 {
                color: #0000FF
            }

            .content-title {
                margin: 0;
            }
        </style>
    </head>

    <body dir="rtl">

        <div class="min-contents ">

            {{-- artical one --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    مادۀ اول</h3>

            </div>
            {!! '<tocentry content="  مقدمه:" level="3" />' !!}
            <h4 class="content-title">
                مقدمه: </h4>
            {{ $preamble }}
            </p>
            {{-- artical two --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    مادۀ دوم</h3>

            </div>
            {!! '<tocentry content="   طرفین:" level="3" />' !!}
            <h4>
                طرفین: </h4>
            => وزارت صحت عامه امارت اسلامی افغانستان
            <br>
            => {{ $ngo_name }}
            <br>
            </p>

            {{-- artical Three --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    مادۀ سوم</h3>

            </div>
            {!! '<tocentry content="  معرفی موسسه:" level="3" />' !!}
            <h4>
                معرفی موسسه: </h4>

            {{ $introduction_ngo }}
            <br>
            </p>

            {{-- Article Four --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    مادۀ چهارم</h3>

            </div>
            {!! '<tocentry content="  تعریف اصطلاحات/مخففات: " level="3" />' !!}
            <h4>
                تعریف اصطلاحات/مخففات: </h4>

            {{ $abbr }}
            <br>
            </p>
            {{-- Article five --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    مادۀ پنجم</h3>

            </div>
            {!! '<tocentry content="دیدگاه موسسه:" level="3" />' !!}
            <h4>
                دیدگاه موسسه:</h4>

            {{ $org_vision }}
            <br>
            </p>
            {{-- Article six --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    مادۀ ششم</h3>

            </div>
            {!! '<tocentry content="مأموریت موسسه:" level="3" />' !!}
            <h4>
                مأموریت موسسه: </h4>

            {{ $org_mission }}
            <br>
            </p>

            {{-- Article seven --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    مادۀ هفتم</h3>

            </div>
            {!! '<tocentry content="ساختار اداری وساحات کاری موسسه:" level="3" />' !!}
            <h4>
                ساختار اداری وساحات کاری موسسه: </h4>

            {{ $org_management_working_area }}

            <br>
            {!! '<tocentry content=" جدول ساختار اداری : " level="3" />' !!}
            <h5>
                جدول ساختار اداری : </h5>
            {{ $project_structure }}
            </p>

            {{-- Article Eight --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    مادۀ هشتم</h3>

            </div>
            {!! '<tocentry content=" سابقه فعالیت های موسسه در بخش های مربوطه:" level="3" />' !!}
            <h4>
                سابقه فعالیت های موسسه در بخش های مربوطه: </h4>

            {{ $backgroud_experince }}

            <br>
            {{-- {!! '<tocentry content="Provision of Health Services" level="3" />' !!} --}}
            {{-- <h5>
                Provision of Health Services:</h5><br>
            </p>

            {{--   Article Nine --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    مادۀ نهم</h3>

            </div>
            {!! '<tocentry content="معرفی پروژه فعلی " level="3" />' !!}
            <h4>
                معرفی پروژه فعلی </h4>

            {{ $introduction_current_project }}
            <br>
        </div>
    </body>

</html>
