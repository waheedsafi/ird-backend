<!DOCTYPE html>
<html lang="en" dir="rtl">

    <head>
        <meta charset="UTF-8">
        <title>Memorandum of Understanding</title>
        <style>
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
        </style>
    </head>

    <body dir="rtl">

        <div class="min-logo-div">
            <img src="{{ public_path('storage/images/emart.png') }}" class="min-logo" alt="">
            <img src="{{ public_path('storage/images/moph.png') }}" class="moph-logo" alt="">
            <img src="{{ public_path('storage/images/header.png') }}" class="header-logo" alt="">

            <div class="header-text-cont">
                <h5 class="header-text">امارت اسلامی افغانستان</h5>
                <h5 class="header-text">وزارت صحت عامه</h5>
                <h5 class="header-text">ریاست روابط بین المللی</h5>
            </div>
        </div>

        <div class="min-contents">

            <h1 class="first-page-text">
                تفـــــــــــــاهم نامه
            </h1>

            <h2 class="first-page-text">
                فی مابین<br>

                <br>
                وزارت صحت عامه امارت اسلامی افغانستان
                <br>
                و
                <br>
                {{ $ngo_name }} موسسه
            </h2>

            <img src="{{ public_path('storage/images/header.png') }}" class="header-logo" alt="">

    </body>

</html>
