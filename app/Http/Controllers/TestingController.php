<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Role;
use App\Models\User;
use App\Models\Email;
use App\Models\NewsTran;
use App\Models\NewsDocument;
use App\Models\NotifierType;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Enums\Types\NotifierEnum;
use App\Models\NotifierTypeTrans;
use Illuminate\Support\Facades\DB;
use App\Enums\Permissions\RoleEnum;
use Illuminate\Support\Facades\App;
use App\Enums\Types\ApplicationEnum;
use Illuminate\Support\Facades\Hash;
use App\Enums\Languages\LanguageEnum;
use App\Enums\Types\ApprovalTypeEnum;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;

class TestingController extends Controller
{
    protected $pendingTaskRepository;
    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;
    }
    private $books = [
        [
            'id' => '4c03ae5e-0e57-4e38-a42e-f5c5d54d5ff9',
            'fa' => [
                'title' => 'مصاحبه برنامه‌نویسی: ۱۸۹ سوال و راه‌حل برنامه‌نویسی (مصاحبه و شغل)وميو دٸوس',
                'description' => 'زه استخدام کوونکی نه یم. زه یو سافټویر انجنیر یم. او په دې توګه، زه پوهیږم چې دا څه ډول دی چې له تاسو څخه وغوښتل شي چې په ځای کې غوره الګوریتمونه جوړ کړئ او بیا په سپینه تخته کې بې عیب کوډ ولیکئ. زه د یو نوماند او مرکه کونکي په توګه له دې څخه تیر شوی یم.
                    د کوډ کولو مرکې کریک کول، شپږمه نسخه دلته ده چې تاسو سره د دې پروسې له لارې مرسته وکړي، تاسو ته هغه څه درس درکوي چې تاسو ورته اړتیا لرئ پوه شئ او تاسو ته وړتیا درکوي چې په خپل غوره فعالیت کې کار وکړئ. ما د سلګونو سافټویر انجنیرانو روزنه او مرکې کړې دي. پایله دا کتاب دی.
                    زده کړئ چې څنګه په یوه پوښتنه کې اشارې او پټ توضیحات افشا کړئ، ومومئ چې څنګه ستونزه په اداره کیدونکو ټوټو وویشئ، تخنیکونه رامینځته کړئ ترڅو ځان خلاص کړئ کله چې بند پاتې شئ، د کمپیوټر ساینس اصلي مفکورې زده کړئ (یا بیا زده کړئ)، او د 189 مرکې پوښتنو او حلونو تمرین وکړئ.'
            ],
            'ps' => [
                'title' => 'د کوډ کولو مرکې ماتول: د پروګرام کولو ۱۸۹ پوښتنې او حل لارې (د مرکې او مسلک ماتول)',
                'description' => "من استخدام‌کننده نیستم. من یک مهندس نرم‌افزار هستم. و به همین دلیل، می‌دانم که از شما خواسته می‌شود الگوریتم‌های درخشان را درجا بنویسید و سپس کد بی‌عیب و نقصی را روی تخته سفید بنویسید. من این را به عنوان یک کاندیدا و به عنوان یک مصاحبه‌کننده تجربه کرده‌ام.
                    کتاب «شکستن مصاحبه کدنویسی، ویرایش ششم» اینجاست تا در این فرآیند به شما کمک کند، آنچه را که باید بدانید به شما آموزش دهد و شما را قادر سازد تا بهترین عملکرد خود را داشته باشید. من صدها مهندس نرم‌افزار را مربیگری و مصاحبه کرده‌ام. نتیجه این کتاب است.
                    یاد بگیرید که چگونه نکات و جزئیات پنهان یک سوال را کشف کنید، کشف کنید که چگونه یک مشکل را به بخش‌های قابل مدیریت تقسیم کنید، تکنیک‌هایی را برای رهایی از گیر افتادن در مصاحبه توسعه دهید، مفاهیم اصلی علوم کامپیوتر را یاد بگیرید (یا دوباره یاد بگیرید) و روی ۱۸۹ سوال و راه‌حل مصاحبه تمرین کنید."
            ],
            'en' => [
                'title' => 'Cracking the Coding Interview: 189 Programming Questions and Solutions (Cracking the Interview & Career)',
                'description' => "I am not a recruiter. I am a software engineer. And as such, I know what it's like to be asked to whip up brilliant algorithms on the spot and then write flawless code on a whiteboard. I've been through this as a candidate and as an interviewer.
                     Cracking the Coding Interview, 6th Edition is here to help you through this process, teaching you what you need to know and enabling you to perform at your very best. I've coached and interviewed hundreds of software engineers. The result is this book.
                     Learn how to uncover the hints and hidden details in a question, discover how to break down a problem into manageable chunks, develop techniques to unstick yourself when stuck, learn (or re-learn) core computer science concepts, and practice on 189 interview questions and solutions."
            ],
            'image' => 'https://m.media-amazon.com/images/I/61mIq2iJUXL._SY425_.jpg'
        ],
        [
            'id' => 'b7de08a3-5871-4263-a479-93ec58debb15',
            'fa' => [
                'title' => "مدیریت حافظه ++C: نوشتن کد ++C کم‌حجم‌تر و ایمن‌تر با استفاده از تکنیک‌های اثبات‌شده مدیریت حافظه",
                'description' => "++C به توسعه‌دهندگان قدرت بی‌نظیری در مدیریت حافظه می‌دهد، اما قدرت زیاد، مسئولیت زیادی نیز به همراه دارد. مدیریت ضعیف حافظه می‌تواند منجر به خرابی، تنگناهای عملکرد و آسیب‌پذیری‌های امنیتی شود.
                    چه روی سیستم‌های بلادرنگ، موتورهای بازی، برنامه‌های مالی یا دستگاه‌های تعبیه‌شده کار کنید، تسلط بر مدیریت حافظه برای ساخت نرم‌افزارهای قوی و با کارایی بالا ضروری است. این کتاب نگاهی عمیق به مدیریت حافظه مدرن ++C ارائه می‌دهد و هم اصول اساسی و هم تکنیک‌های پیشرفته مورد استفاده متخصصان برای بهینه‌سازی برنامه‌ها را پوشش می‌دهد. این کتاب شکاف بین دانش نظری و پیاده‌سازی عملی را پر می‌کند و تضمین می‌کند که شما نه تنها «چه چیزی» را درک می‌کنید، بلکه..."
            ],
            'ps' => [
                'title' => "د C++ حافظې مدیریت: د ثابت شوي حافظې مدیریت تخنیکونو په کارولو سره د C++ کوډ ډیر نرم او خوندي ولیکئ.",
                'description' => "د C++ حافظې مدیریت: د ثابت شوي حافظې مدیریت تخنیکونو په کارولو سره د C++ کوډ ډیر خوندي او خوندي ولیکئ',
                    'تفصیل' => 'C++ پراختیا کونکو ته د حافظې په اړه بې ساري ځواک ورکوي، مګر د لوی ځواک سره لوی مسؤلیت راځي. د حافظې ضعیف اداره کول کولی شي د کریشونو، فعالیت خنډونو، او امنیتي زیانونو لامل شي.
                     که تاسو په ریښتیني وخت سیسټمونو، د لوبې انجنونو، مالي غوښتنلیکونو، یا ایمبیډ شوي وسیلو کې کار کوئ، د حافظې مدیریت ماسټر کول د قوي او لوړ فعالیت سافټویر جوړولو لپاره اړین دا کتاب د عصري C++ حافظې مدیریت کې ژوره غوطه وړاندې کوي، دواړه بنسټیز اصول او پرمختللي تخنیکونه پوښي چې د متخصصینو لخوا د غوښتنلیکونو غوره کولو لپاره کارول کیږي. دا د تیوریکي پوهې او عملي پلي کولو ترمنځ تشه ډکوي، ډاډ ترلاسه کوي چې تاسو نه یوازې  پوهیږئ مګر/"
            ],
            'en' => [
                'title' => 'C++ Memory Management: Write leaner and safer C++ code using proven memory-management techniques',
                'description' => 'C++ gives developers unparalleled power over memory, but with great power comes great responsibility. Poor memory handling can lead to crashes, performance bottlenecks, and security vulnerabilities.
                     Whether you’re working on real-time systems, game engines, financial applications, or embedded devices, mastering memory management is essential to building robust and high-performance software. This book provides a deep dive into modern C++ memory management, covering both fundamental principles and advanced techniques used by experts to optimize applications. It bridges the gap between theoretical knowledge and practical implementation, ensuring that you not only understand the "what" but/'
            ],
            'image' => 'https://m.media-amazon.com/images/I/71Csss0+FWL._SY385_.jpg'
        ],
        [
            'id' => 'b7de08a3-5871-4263-a479-93ec5815',
            'fa' => [
                'title' => "راهنمای جیبی لینوکس: دستورات ضروری (راهنماهای جیبی لینوکس)",
                'description' => "اگر در کار روزمره خود از لینوکس استفاده می‌کنید، کتاب «راهنمای جیبی لینوکس» مرجع کاملی برای استفاده در محل کار است. این نسخه که به طور کامل در بیستمین سالگرد انتشار به‌روزرسانی شده است، بیش از ۲۰۰ دستور لینوکس، از جمله دستورات جدید برای مدیریت فایل، مدیریت بسته، کنترل نسخه، تبدیل فرمت فایل و موارد دیگر را توضیح می‌دهد.
                     در این راهنمای مختصر، نویسنده، دنیل برت، مفیدترین دستورات لینوکس را بر اساس عملکرد دسته‌بندی کرده است. چه تازه‌کار باشید و چه کاربر باتجربه، این کتاب کاربردی مرجعی ایده‌آل برای مهم‌ترین دستورات لینوکس است."
            ],
            'ps' => [
                'title' => "د لینکس پاکټ لارښود: اړین امرونه (د لینکس پاکټ لارښودونه)",
                'description' => 'که تاسو په خپل ورځني کار کې لینکس کاروئ، نو د لینکس پاکټ لارښود د دندې پرمهال یو مناسب حواله ده. دا په بشپړه توګه تازه شوی د شلمې کلیزې نسخه د 200 څخه ډیر لینکس قوماندې تشریح کوي، پشمول د فایل اداره کولو، د بسته بندۍ مدیریت، د نسخې کنټرول، د فایل فارمیټ تبادلو، او نورو لپاره نوي قوماندې.پدې لنډ لارښود کې، لیکوال ډینیل بیرټ د فعالیت له مخې ګروپ شوي خورا ګټور لینکس قوماندې چمتو کوي. که تاسو یو نوی یا تجربه لرونکی کارونکی یاست، دا عملي کتاب د خورا مهم لینکس قوماندې لپاره یو مثالی حواله ده.'
            ],
            'en' => [
                'title' => 'Linux Pocket Guide: Essential Commands (Linux Pocket Guides)',
                'description' => "If you use Linux in your day-to-day work, then Linux Pocket Guide is the perfect on-the-job reference. This thoroughly updated 20th anniversary edition explains more than 200 Linux commands, including new commands for file handling, package management, version control, file format conversions, and more.
                     In this concise guide, author Daniel Barrett provides the most useful Linux commands grouped by functionality. Whether you're a novice or an experienced user, this practical book is an ideal reference for the most important Linux commands."
            ],
            'image' => 'https://m.media-amazon.com/images/I/41jWjmIoJYL._SY445_SX342_.jpg'
        ],
    ];
    private $healthBooks = [
        [
            'id' => '4c03ae5e-0e57-4e38-a42e-f5c5d54d5ff9',
            'fa' => [
                'title' => 'بدن شما چگونه کار می‌کند: راهنمای جامع فیزیولوژی انسان',
                'description' => 'این کتاب مروری جامع بر سیستم‌های مختلف بدن انسان ارائه می‌دهد، از جمله دستگاه گردش خون، تنفس، گوارش و عصبی. با استفاده از تصاویر واضح و توضیحات علمی ساده، خواننده را با نحوه عملکرد بدن در سطح سلولی و سیستماتیک آشنا می‌کند. این کتاب منبعی عالی برای دانشجویان پزشکی، پرستاران و علاقه‌مندان به سلامت بدن است.'
            ],
            'ps' => [
                'title' => 'ستاسو بدن څنګه کار کوي: د انسان فیزیولوژۍ بشپړ لارښود',
                'description' => 'دا کتاب د انسان د بدن مختلفو سیسټمونو ته یو جامع کتنه وړاندې کوي، پشمول د دوران، تنفس، هضم او عصبي سیستمونه. د روښانه انځورونو او ساده علمي توضیحاتو په مرسته، دا لوستونکي ته دا ښيي چې بدن څنګه کار کوي، هم په حجروي او هم سیسټماتيک کچه. دا د طبي زده کوونکو، نرسانو، او روغتیا ته علاقه لرونکو لپاره غوره سرچینه ده.'
            ],
            'en' => [
                'title' => 'How Your Body Works: A Complete Guide to Human Physiology',
                'description' => 'This book offers a comprehensive overview of the human body systems, including the circulatory, respiratory, digestive, and nervous systems. Using clear illustrations and simple scientific explanations, it helps readers understand how the body functions at both cellular and systemic levels. A great resource for medical students, nurses, and health enthusiasts.'
            ],
            'image' => 'https://m.media-amazon.com/images/I/616RIzbjiPL._SX342_SY445_.jpg'
        ],
        [
            'id' => 'b7de08a3-5871-4263-a479-93ec58debb15',
            'fa' => [
                'title' => 'تغذیه برای زندگی سالم: راهنمای عملی برای رژیم متعادل',
                'description' => 'در این کتاب، اصول پایه تغذیه سالم و تاثیر آن بر سلامت جسم و ذهن بررسی می‌شود. همچنین نکاتی کاربردی برای برنامه‌ریزی وعده‌های غذایی، مدیریت وزن و جلوگیری از بیماری‌های مزمن ارائه شده است. یک مرجع عالی برای افرادی که به دنبال تغییر سبک زندگی خود هستند.'
            ],
            'ps' => [
                'title' => 'د سالم ژوند لپاره تغذیه: د متوازن رژیم لپاره عملي لارښود',
                'description' => 'په دې کتاب کې د سالم تغذیې بنسټیز اصول او د بدن او ذهن پر روغتیا یې اغېزې بیان شوي دي. دا همدارنګه د خوړو پلان جوړونې، د وزن کنټرول، او د مزمنو ناروغیو مخنیوي لپاره عملي مشورې وړاندې کوي. دا د هغو کسانو لپاره یو غوره لارښود دی چې غواړي خپل ژوند ته بدلون ورکړي.'
            ],
            'en' => [
                'title' => 'Nutrition for a Healthy Life: A Practical Guide to Balanced Diet',
                'description' => 'This book explores the fundamental principles of healthy eating and its impact on both physical and mental well-being. It includes practical tips for meal planning, weight management, and prevention of chronic diseases. An excellent guide for anyone seeking to improve their lifestyle.'
            ],
            'image' => 'https://m.media-amazon.com/images/I/41DocX7CozL._SY445_SX342_.jpg'
        ],
        [
            'id' => 'b7de08a3-5871-4263-a479ggg3ec58debb15',
            'fa' => [
                'title' => 'مغز در حال تغییر: علم جدید درباره انعطاف‌پذیری عصبی',
                'description' => 'این کتاب نگاهی نوین به مغز انسان و توانایی آن برای تغییر، یادگیری و بهبود پس از آسیب دارد. با بررسی مطالعات علمی و داستان‌های واقعی، نویسنده نشان می‌دهد که چگونه می‌توان از ظرفیت‌های پنهان مغز بهره‌مند شد. کتابی الهام‌بخش برای علاقمندان به علوم اعصاب و روانشناسی.'
            ],
            'ps' => [
                'title' => 'بدلیدونکی دماغ: د عصبي انعطاف وړتیا نوې پوهه',
                'description' => 'دا کتاب د انسان دماغ ته نوې کتنه وړاندې کوي، او دا ښيي چې دماغ څنګه بدلون مومي، زده کړه کوي، او له زیان وروسته رغېږي. د علمي څېړنو او واقعي کیسو له لارې، لیکوال ښيي چې څنګه د دماغ پټ ظرفیتونه فعالولی شو. دا د اعصابو علومو او روان پوهنې مینه‌والو لپاره یو الهام بخښونکی کتاب دی.'
            ],
            'en' => [
                'title' => 'The Changing Brain: The New Science of Neuroplasticity',
                'description' => 'This groundbreaking book explores the human brain’s ability to change, learn, and recover from injury. Through scientific studies and real-life stories, the author demonstrates how to unlock the brain’s hidden potential. A truly inspiring read for those interested in neuroscience and psychology.'
            ],
            'image' => 'https://m.media-amazon.com/images/I/41HDhuXnnkL._SY445_SX342_.jpg'
        ],
    ];
    private $sportBooks = [
        [
            'id' => '865c4653-c0bc-4df1-b101-52f4e75f99f7',
            'fa' => [
                'title' => 'علم تمرین: راهنمای مربیان و ورزشکاران',
                'description' => 'این کتاب اصول علمی پشت تمرین ورزشی را توضیح می‌دهد، از جمله فیزیولوژی ورزش، طراحی برنامه تمرینی، و بازیابی. منبعی کلیدی برای مربیان، ورزشکاران حرفه‌ای و دانشجویان تربیت بدنی است.'
            ],
            'ps' => [
                'title' => 'د تمرین ساینس: د روزونکو او ورزشکارانو لپاره لارښود',
                'description' => 'دا کتاب د تمرین شاته علمي اصول بیانوي، لکه د سپورت فیزیولوژي، د تمرین پروګرام جوړونه، او ریکاوري. دا د روزونکو، مسلکي لوبغاړو، او د فزیکي روزنې زده کوونکو لپاره یوه مهمه سرچینه ده.'
            ],
            'en' => [
                'title' => 'Science of Training: A Guide for Coaches and Athletes',
                'description' => 'This book explains the scientific principles behind sports training, including exercise physiology, workout planning, and recovery strategies. A key resource for coaches, professional athletes, and physical education students.'
            ],
            'image' => 'https://m.media-amazon.com/images/I/51C81C6T6DL._SY445_SX342_.jpg'
        ],
        [
            'id' => 'c44d08bb-bb21-4b8c-a45a-0c5886567913c',
            'fa' => [
                'title' => 'تغذیه ورزشی: راهنمای کاربردی برای بهبود عملکرد ورزشی',
                'description' => 'کتابی جامع درباره نیازهای تغذیه‌ای ورزشکاران در رشته‌های مختلف. شامل راهکارهایی برای افزایش انرژی، بازیابی سریع‌تر و پیشگیری از آسیب‌های ورزشی است.'
            ],
            'ps' => [
                'title' => 'د سپورت تغذیه: د فعالیت لوړولو لپاره عملي لارښود',
                'description' => 'دا کتاب د ورزشکارانو د تغذیې اړتياوې بیانوي، د انرژۍ لوړولو، چټک ریکاوري، او د ټپونو مخنیوي لپاره مشورې وړاندې کوي. دا د هر لوبغاړي لپاره یوه مهمه مرجع ده.'
            ],
            'en' => [
                'title' => 'Sports Nutrition: A Practical Guide to Boost Performance',
                'description' => 'A comprehensive book on the nutritional needs of athletes across various sports. Includes strategies for boosting energy, faster recovery, and injury prevention. A must-read for active individuals and fitness professionals.'
            ],
            'image' => 'https://m.media-amazon.com/images/I/51oGoYEtVrL._SY445_SX342_.jpg'
        ],
        [
            'id' => 'e3fa280e-04c5-4bbf-a89b-5101c78a2c30',
            'fa' => [
                'title' => 'روانشناسی ورزش: تقویت ذهن برای موفقیت ورزشی',
                'description' => 'این کتاب به بررسی نقش ذهن و تمرکز در عملکرد ورزشی می‌پردازد. تکنیک‌هایی مانند تجسم، مدیریت استرس و انگیزه بخشی برای رسیدن به اوج عملکرد را آموزش می‌دهد.'
            ],
            'ps' => [
                'title' => 'د سپورت روان پوهنه: د ذهني ځواک د لوړوالي لپاره لارښود',
                'description' => 'په دې کتاب کې د ذهن، تمرکز، او فشار مدیریت اهمیت بیان شوی دی. د تمرکز، تجسم، او ذهني چمتووالي تخنیکونه د لوړې کړنې لپاره وړاندې شوي دي.'
            ],
            'en' => [
                'title' => 'Sport Psychology: Strengthening the Mind for Peak Performance',
                'description' => 'This book explores the role of mental strength and focus in sports success. It teaches techniques like visualization, stress management, and motivation to help athletes perform at their best.'
            ],
            'image' => 'https://m.media-amazon.com/images/I/51SnAC31ApL._SY445_SX342_.jpg'
        ],
        [
            'id' => '7d5635a2-2d50-4643-bd83-39b27a5a432c',
            'fa' => [
                'title' => 'تمرین قدرتی برای همه: برنامه‌های مقاومتی برای هر سطح',
                'description' => 'کتابی مناسب برای همه سطوح ورزشکاران که شامل برنامه‌های تمرینی قدرتی با وزن بدن و وزنه است. هدف کتاب بهبود قدرت، تعادل و سلامت کلی بدن است.'
            ],
            'ps' => [
                'title' => 'د هرچا لپاره د ځواک تمرین: د ټولو کچو لپاره د مقاومت پروګرامونه',
                'description' => 'دا کتاب د ځواک تمرین لپاره ساده خو مؤثر پروګرامونه وړاندې کوي، چې د وزن او بدن وزن تمرینونه پکې شامل دي. موخه یې د ځواک، توازن، او ټولیزې روغتیا لوړول دي.'
            ],
            'en' => [
                'title' => 'Strength Training for Everyone: Resistance Programs for All Levels',
                'description' => 'A book designed for athletes at all levels, offering strength training routines using both weights and bodyweight. The focus is on improving power, balance, and overall physical health.'
            ],
            'image' => 'https://m.media-amazon.com/images/I/41EfWgZwh5L._SY445_SX342_.jpg'
        ]
    ];
    private $foodBooks = [
        [
            'id' => '7d5635a2-2d50-4643-bd83-39b27a5a342c',
            'fa' => [
                'title' => 'آشپزی سالم برای زندگی بهتر',
                'description' => 'این کتاب شامل دستورهای غذایی سالم با مواد طبیعی و مغذی است. تمرکز بر وعده‌های متعادل و آسان برای تهیه در خانه دارد و برای خانواده‌ها، ورزشکاران و افراد مبتلا به بیماری‌های مزمن مناسب است.'
            ],
            'ps' => [
                'title' => 'د ښه ژوند لپاره سالم پخلی',
                'description' => 'په دې کتاب کې د سالمو، طبیعي او مغذي خوړو ترکیبونه شامل دي. دا کتاب د متوازنو او ساده کورني خوړو تمرکز کوي او د کورنۍ، ورزشکارانو او د اوږدمهاله ناروغیو لرونکو لپاره مناسب دی.'
            ],
            'en' => [
                'title' => 'Healthy Cooking for a Better Life',
                'description' => 'This book offers healthy recipes made with natural and nutritious ingredients. It focuses on balanced, easy-to-make meals at home, perfect for families, athletes, and people with chronic conditions.'
            ],
            'image' => 'https://m.media-amazon.com/images/I/51iM94tev-L._SX342_SY445_.jpg'
        ],
        [
            'id' => '7d5635a2-2d50-4643-bd83-_SX342_SY445_',
            'fa' => [
                'title' => 'هنر آشپزی گیاهی',
                'description' => 'راهنمایی جامع برای پخت غذاهای گیاهی خوشمزه و متنوع، از صبحانه تا شام. مناسب برای گیاه‌خواران و کسانی که به دنبال کاهش مصرف گوشت هستند.'
            ],
            'ps' => [
                'title' => 'د نباتي پخلي هنر',
                'description' => 'دا کتاب د خوندورو نباتي خوړو پخلو بشپړه لارښود وړاندې کوي، له ناشتې څخه تر ماښام پورې. د هغو کسانو لپاره مناسب دی چې نباتي رژیم لري یا غواړي د غوښې مصرف کم کړي.'
            ],
            'en' => [
                'title' => 'The Art of Plant-Based Cooking',
                'description' => 'A comprehensive guide to delicious and diverse plant-based meals, from breakfast to dinner. Perfect for vegetarians or anyone looking to reduce meat consumption.'
            ],
            'image' => 'https://m.media-amazon.com/images/I/91UAt5UxzCL._SY385_.jpg'
        ],
        [
            'id' => '7d5635a2-2d50-4643-bd83-81GPu9Tt0rL',
            'fa' => [
                'title' => 'تغذیه در دوران بارداری',
                'description' => 'این کتاب به مادران باردار کمک می‌کند تا با انتخاب مواد مغذی مناسب، سلامت خود و کودکشان را حفظ کنند. شامل برنامه غذایی، مواد مورد نیاز، و نکاتی برای هر سه‌ماهه بارداری است.'
            ],
            'ps' => [
                'title' => 'د امیندوارۍ پر مهال تغذیه',
                'description' => 'دا کتاب د امیندوارو میندو سره مرسته کوي چې مناسب مغذي خواړه غوره کړي او د ځان او خپل ماشوم روغتیا وساتي. پدې کې د درېیو ټریمسترونو لپاره تغذیوي پلانونه او مشورې شاملې دي.'
            ],
            'en' => [
                'title' => 'Nutrition During Pregnancy',
                'description' => 'This book helps expectant mothers choose the right nutrients to maintain their own and their baby’s health. Includes meal plans, essential nutrients, and trimester-by-trimester tips.'
            ],
            'image' => 'https://m.media-amazon.com/images/I/81GPu9Tt0rL._SY385_.jpg'
        ],
        [
            'id' => '7d5635a2-2d50-4643-bd83-_SY466_',
            'fa' => [
                'title' => 'کتاب نان: راهنمای کامل تهیه نان‌های سنتی و مدرن',
                'description' => 'کتابی جذاب برای علاقه‌مندان به نان‌پزی که شامل دستور پخت نان‌های ایرانی، اروپایی و بدون گلوتن است. با نکات کاربردی برای هر سطح از مهارت.'
            ],
            'ps' => [
                'title' => 'د ډوډۍ پخولو لارښود',
                'description' => 'دا کتاب د دودیزو او عصري ډوډیو ترکیبونه وړاندې کوي، لکه ایرانی، اروپایي او بې ګلوټن ډوډۍ. د هرې کچې پخلي لپاره ساده او عملي مشورې لري.'
            ],
            'en' => [
                'title' => 'The Bread Book: Traditional and Modern Baking Guide',
                'description' => 'A great book for bread enthusiasts with recipes for Persian, European, and gluten-free breads. Includes practical tips for all skill levels.'
            ],
            'image' => 'https://m.media-amazon.com/images/I/71ovph-O-2L._SY466_.jpg'
        ],
        [
            'id' => '7d5635a2-2d50-4643-bd83-91sAG6hqzNL',
            'fa' => [
                'title' => 'غذاهای سریع و سالم برای مشغول‌ها',
                'description' => 'برای کسانی که زمان کمی دارند اما نمی‌خواهند سلامت خود را قربانی کنند، این کتاب راهکارهایی برای تهیه غذاهای سریع، مغذی و ساده ارائه می‌دهد.'
            ],
            'ps' => [
                'title' => 'د بوختو کسانو لپاره چټک او سالم خواړه',
                'description' => 'دا کتاب د هغو کسانو لپاره دی چې لږ وخت لري خو غواړي صحي خواړه وخوري. پدې کې چټک، مغذي او ساده ترکیبونه شامل دي.'
            ],
            'en' => [
                'title' => 'Quick & Healthy Meals for Busy People',
                'description' => 'For those short on time but not willing to sacrifice health, this book offers fast, nutritious, and simple recipes anyone can make.'
            ],
            'image' => 'https://m.media-amazon.com/images/I/91sAG6hqzNL._SY466_.jpg'
        ],
        [
            'id' => '7d5635a2-2d50-4643-bd83-51DVed3JIaL',
            'fa' => [
                'title' => 'تاریخچه غذاهای جهان',
                'description' => 'سفری جذاب به تاریخ غذا و فرهنگ‌های غذایی از تمدن‌های باستان تا دنیای مدرن. کتابی آموزنده برای علاقه‌مندان به تاریخ، فرهنگ و آشپزی.'
            ],
            'ps' => [
                'title' => 'د نړۍ د خوړو تاریخ',
                'description' => 'دا کتاب د پخلي او خوراکي کلتورونو تاریخي سفر وړاندې کوي، له لرغونو تمدنونو تر معاصر نړۍ پورې. دا د تاریخ، کلتور او پخلي مینه‌والو لپاره یو ارزښتناک اثر دی.'
            ],
            'en' => [
                'title' => 'A History of World Cuisine',
                'description' => 'A fascinating journey through the history of food and culinary cultures from ancient civilizations to the modern world. Great for fans of history, culture, and cooking.'
            ],
            'image' => 'https://m.media-amazon.com/images/I/51DVed3JIaL._SX342_SY445_.jpg'
        ]
    ];

    public function books()
    {
        $locale = App::getLocale();

        $localizedBooks = array_map(function ($book) use ($locale) {
            return [
                'id' => $book['id'],
                'title' => $book[$locale]['title'] ?? $book['en']['title'],
                'description' => $book[$locale]['description'] ?? $book['en']['description'],
                'image' => $book['image'],
            ];
        }, $this->books);

        return response()->json($localizedBooks);
    }
    public function healthBooks()
    {
        $locale = App::getLocale();

        $localizedBooks = array_map(function ($book) use ($locale) {
            return [
                'id' => $book['id'],
                'title' => $book[$locale]['title'] ?? $book['en']['title'],
                'description' => $book[$locale]['description'] ?? $book['en']['description'],
                'image' => $book['image'],
            ];
        }, $this->healthBooks);

        return response()->json($localizedBooks);
    }
    public function sportBooks()
    {
        $locale = App::getLocale();

        $localizedBooks = array_map(function ($book) use ($locale) {
            return [
                'id' => $book['id'],
                'title' => $book[$locale]['title'] ?? $book['en']['title'],
                'description' => $book[$locale]['description'] ?? $book['en']['description'],
                'image' => $book['image'],
            ];
        }, $this->sportBooks);

        return response()->json($localizedBooks);
    }
    public function foodBooks()
    {
        $locale = App::getLocale();

        $localizedBooks = array_map(function ($book) use ($locale) {
            return [
                'id' => $book['id'],
                'title' => $book[$locale]['title'] ?? $book['en']['title'],
                'description' => $book[$locale]['description'] ?? $book['en']['description'],
                'image' => $book['image'],
            ];
        }, $this->foodBooks);

        return response()->json($localizedBooks);
    }
    public function aboutUs()
    {
        $locale = App::getLocale(); // current locale, e.g. 'en', 'fa', 'ps'

        $values = [
            [
                'en' => [
                    'title' => 'Empowerment',
                    'description' => 'Building capacity and enabling communities to improve health.',
                ],
                'fa' => [
                    'title' => 'توانمندسازی',
                    'description' => 'ایجاد ظرفیت و توانمندسازی جوامع برای بهبود سلامت.'
                ],
                'ps' => [
                    'title' => 'توانمندسازي',
                    'description' => 'ظرفیت جوړول او ټولنو ته د روغتیا د ښه کولو زمینه برابرول.',
                ],
            ],
            [
                'en' => [
                    'title' => 'Innovation',
                    'description' => 'Advancing health systems with modern technology and solutions.',
                ],
                'fa' => [
                    'title' => 'نوآوری',
                    'description' => 'ارتقای سیستم‌های بهداشتی با تکنولوژی‌های مدرن و راهکارهای نوین',
                ],
                'ps' => [
                    'title' => 'نوښت',
                    'description' => 'د روغتیايي سیستمونو پرمختګ د عصري ټکنالوژۍ او نوښتونو په کارولو سره',
                ],
            ],
            [
                'en' => [
                    'title' => 'Inclusivity',
                    'description' => 'Creating an environment where everyone benefits from better health.',
                ],
                'fa' => [
                    'title' => 'شمولیت',
                    'description' => 'ایجاد محیطی که همه از سلامت بهتر بهره‌مند شوند.',
                ],
                'ps' => [
                    'title' => 'شمولیت',
                    'description' => 'یو داسې چاپیریال رامنځته کول چې هرڅوک له ښه روغتیا څخه ګټه واخلي',
                ],
            ],
            [
                'en' => [
                    'title' => 'Integrity',
                    'description' => 'Ensuring openness and accountability in all our efforts.',
                ],
                'fa' => [
                    'title' => 'صداقت',
                    'description' => 'اطمینان از شفافیت و پاسخگویی در تمام تلاش‌های ما',
                ],
                'ps' => [
                    'title' => 'صداقت',
                    'description' => 'په خپلو ټولو هڅو کې د روڼتیا او حساب ورکونې ډاډ ورکول',
                ],
            ],
            [
                'en' => [
                    'title' => 'Passion',
                    'description' => 'Driven by dedication to better health for all Afghans..',
                ],
                'fa' => [
                    'title' => 'شور و اشتیاق',
                    'description' => 'متعلق به تعهد برای بهداشت بهتر برای همه افغان‌ها',
                ],
                'ps' => [
                    'title' => 'تنده',
                    'description' => 'د ټولو افغانانو لپاره د ښه روغتیا په لاره ژمنتیا',
                ],
            ],
        ];

        $our_mission = [
            [
                'en' => [
                    'description' => 'The Directorate of International Relations, Aid Coordination, and Integration is a key department within the Ministry of Public Health that manages Global Fund aid, co-financing projects, and government-funded programs. It collaborates with international organizations, NGOs, and donors, oversees diplomatic relations, and facilitates legal frameworks for foreign investments. Through its two main units—Aid Mobilization and Internal and External Health Coordination—it strengthens the health system by introducing modern technology, medicines, equipment, and skilled personnel, supports the expansion of health centers and capacity building, and actively participates in the prevention of diseases such as HIV/AIDS, tuberculosis, and malaria. This directorate plays a vital role in resource management, international cooperation, and improving the quality of health services across Afghanistan.',
                ],
                'fa' => [
                    'description' => 'ریاست روابط بین‌المللی، هماهنگی کمک‌ها و انسجام وزارت صحت عامه، اداره‌ای مهم است که کمک‌های گلوبال فند، پروژه‌های تمویل مشترک و برنامه‌های دولت را مدیریت می‌کند. این ریاست با سازمان‌های بین‌المللی، نهادهای غیردولتی و تمویل‌کنندگان همکاری کرده، روابط دیپلوماتیک را تنظیم و چارچوب‌های قانونی سرمایه‌گذاری خارجی را فراهم می‌سازد. همچنین با دو واحد جذب کمک و هماهنگی داخلی و خارجی، سیستم صحی را با تکنالوژی نوین، دارو، تجهیزات و پرسونل متخصص تقویت می‌کند، از توسعه مراکز صحی و ارتقای ظرفیت‌ها حمایت می‌نماید و در پیشگیری از بیماری‌های واگیر چون HIV/AIDS، توبرکلوز و ملاریا مشارکت فعال دارد. این ریاست نقش کلیدی در مدیریت منابع، هماهنگی بین‌المللی و بهبود کیفیت خدمات صحی افغانستان ایفا می‌کند.',
                ],
                'ps' => [
                    'description' => 'ریاست د نړیوالو اړیکو، مرستو همغږۍ او انسجام د عامې روغتیا وزارت یوه مهمه اداره ده چې د ګلوبل فند مرستې، ګډ تمویل او د دولت له لوري تمویل شوې پروژې اداره کوي. دغه ریاست له نړیوالو سازمانونو، غیر دولتي موسسو او تمویل کوونکو سره همکاري کوي، ديپلوماتیکې اړیکې سمبالوي او د بهرنیو پانګه والو لپاره قانوني چارې برابروي. له دوه برخو—د مرستو د جذب او د داخلي او بهرنۍ روغتیايي همغږۍ آمریتونو—سره، د روغتیا سیستم د نوې تکنالوژۍ، درملو، تجهیزاتو او متخصصو کادرونو په راوستلو سره پیاوړی کوي، د روغتیايي مرکزونو پراختیا او ظرفیت لوړولو ملاتړ کوي، او په HIV/AIDS، توبرکلوز او ملاریا ناروغیو د مخنیوي په برخه کې فعاله برخه اخلي. دغه ریاست د منابعو مدیریت، نړیوالې همکارۍ او د افغانستان د روغتیايي خدماتو په ښه کولو کې مهم رول لري',
                ],
            ],
        ];

        $localizedValues = array_map(function ($value) use ($locale) {
            return [
                'title' => $value[$locale]['title'] ?? $value['en']['title'],
                'description' => $value[$locale]['description'] ?? $value['en']['description'],
            ];
        }, $values);

        $localizedMission = array_map(function ($mission) use ($locale) {
            return [
                'description' => $mission[$locale]['description'] ?? $mission['en']['description'],
            ];
        }, $our_mission);
        $achievements = [
            'published' => 222,
            'readers' => 222,
            'experience' => 25,
            'authors' => 700,
        ];

        return response()->json([
            'achievements' => $achievements,
            "objectives" => [
                'our_values' => $localizedValues,
                'our_mission' => $localizedMission,
            ]
        ]);
    }
    public function testing(Request $request)
    {
        $userId = 1; // use an existing user ID or create one
        $priorities = [1, 2, 3]; // existing priority IDs
        $types = [1, 2]; // existing type IDs

        for ($i = 1; $i <= 20; $i++) {
            $news = News::create([
                'user_id' => $userId,
                'visible' => true,
                'date' => now()->subDays(rand(0, 30)),
                'visibility_date' => now()->addDays(rand(1, 10)),
                'priority_id' => $priorities[array_rand($priorities)],
                'news_type_id' => $types[array_rand($types)],
            ]);

            NewsTran::insert([
                [
                    'news_id' => $news->id,
                    'language_name' => LanguageEnum::default->value,
                    'title' => "English Title $i",
                    'contents' => "This is English content for news $i.",
                ],
                [
                    'news_id' => $news->id,
                    'language_name' => LanguageEnum::pashto->value,
                    'title' => "د خبر سرلیک $i",
                    'contents' => "د خبر محتوا $i",
                ],
                [
                    'news_id' => $news->id,
                    'language_name' => LanguageEnum::farsi->value,
                    'title' => "عنوان خبر $i",
                    'contents' => "محتوای خبر $i",
                ],
            ]);

            NewsDocument::create([
                'news_id' => $news->id,
                'url' => "news/772f5c31-0b73-4e74-a412-831a10e6d3ad.png",
                'extension' => "png",
                'name' => "news_image_$i.png",
            ]);
        }
        return "hw";
    }
}
