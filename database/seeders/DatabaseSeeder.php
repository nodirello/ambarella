<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Announcement;
use App\Models\BusinessProfile;
use App\Models\Challenge;
use App\Models\Course;
use App\Models\EcoTask;
use App\Models\Faq;
use App\Models\Job;
use App\Models\Mentor;
use App\Models\News;
use App\Models\PlatformEvent;
use App\Models\Startup;
use App\Models\User;
use App\Models\VolunteerProject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Administrator',
            'email' => 'admin@ambarella.uz',
            'password' => Hash::make('Admin12345'),
        ]);

        User::factory()->create([
            'name' => 'Jasur Toshmatov',
            'email' => 'demo@ambarella.uz',
            'password' => Hash::make('Demo12345'),
        ]);

        $business = User::factory()->create([
            'name' => 'TechCorp UZ',
            'email' => 'tech@ambarella.uz',
        ]);

        BusinessProfile::create([
            'user_id' => $business->id,
            'company_name' => 'TechCorp UZ',
            'description' => 'IT sohasida ishlovchi kompaniya — dasturlash, dizayn va konsalting.',
            'industry' => 'IT',
            'city' => 'Toshkent',
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        Job::factory()->count(8)->create();
        Job::factory()->count(4)->create([
            'user_id' => $business->id,
            'business_profile_id' => $business->businessProfile->id,
            'company' => 'TechCorp UZ',
        ]);

        EcoTask::factory()->count(8)->create();

        foreach ([
            'Python dasturlash asoslari',
            'Web development (Full-Stack)',
            'Grafik dizayn',
            'Digital marketing',
            'Data Science',
            'Flutter mobil ilova',
        ] as $title) {
            Course::factory()->create(['title' => $title]);
        }

        foreach (['Aziz Sultonov', 'Malika Karimova', 'Otabek Mirzayev', 'Dildora Rahimova'] as $index => $name) {
            Mentor::create([
                'name' => $name,
                'role' => ['Senior Frontend Developer', 'Product Manager', 'DevOps Engineer', 'UX/UI Designer'][$index],
                'experience_years' => [7, 5, 6, 4][$index],
                'skills' => [['React', 'TypeScript'], ['Agile', 'Jira'], ['AWS', 'Docker'], ['Figma', 'Prototyping']][$index],
                'rating' => [4.9, 4.8, 4.7, 4.8][$index],
                'bio' => 'Tajribali mutaxassis — yoshlarga 1:1 mentorship beradi.',
                'is_available' => true,
            ]);
        }

        foreach ([
            ['AMBARELLA platformasi ishga tushdi!', 'Platforma', 'O‘zbekiston yoshlari uchun ko‘p funksiyali platforma rasman ishga tushdi.'],
            ['GreenCoin tizimi yangilandi', 'Ekologiya', 'Ekologik vazifalar uchun yangi mukofotlar qo‘shildi.'],
            ['Yangi kurslar qo‘shildi', 'Ta’lim', 'Academy moduliga 6 ta yangi kurs qo‘shildi.'],
        ] as [$title, $category, $content]) {
            News::factory()->create([
                'title' => $title,
                'category' => $category,
                'content' => $content."\n\n".'Platforma barcha modullar bilan ishlaydi va muntazam yangilanib turadi.',
            ]);
        }

        PlatformEvent::create([
            'title' => 'Yoshlar IT Festivali',
            'description' => 'Kunlik ma’ruzalar, ustaxonalar va startaplar ko‘rgazmasi.',
            'city' => 'Toshkent',
            'venue' => 'Toshkent shahri, IT Park',
            'starts_at' => now()->addDays(10),
            'ends_at' => now()->addDays(11),
            'capacity' => 300,
            'price' => 0,
        ]);

        Challenge::create([
            'title' => 'Haftalik eko-challenge',
            'description' => 'Hafta davomida 5 ta eko vazifa bajaring va bonus oling.',
            'reward' => 50,
            'category' => 'Ekologiya',
            'starts_at' => now()->startOfWeek(),
            'ends_at' => now()->endOfWeek(),
        ]);

        Startup::create([
            'user_id' => $business->id,
            'name' => 'EcoTrack',
            'description' => 'Yashil chiqindilarni kuzatish va hisoblash uchun mobil ilova — mahalliy hokimiyatlar bilan hamkorlikda.',
            'category' => 'Ekologiya',
            'stage' => 'mvp',
            'team_size' => 3,
            'looking_for' => 'Backend developer va investor',
            'website' => 'https://ecotrack.uz',
        ]);

        VolunteerProject::create([
            'title' => 'Daraxt ekish marafoni',
            'description' => 'Shahar atrofida 1000 tup ko‘chat ekishda ishtirok eting.',
            'organization' => 'Yashil Vatan',
            'city' => 'Samarqand',
            'starts_at' => now()->addDays(5),
            'hours_expected' => 4,
            'capacity' => 150,
        ]);

        Announcement::create([
            'title' => 'Xush kelibsiz!',
            'body' => 'AMBARELLA platformasiga xush kelibsiz. Profilingizni to‘ldiring va GreenCoin yig‘ishni boshlang.',
            'priority' => 'normal',
        ]);

        foreach ([
            ['Register', 'Ro‘yxatdan o‘tish qanday ishlaydi?', 'Email yoki Telegram orqali 1 daqiqada hisob yarating.'],
            ['Register', 'Platforma bepulmi?', 'Ha, barcha asosiy xizmatlar bepul.'],
            ['GreenCoin', 'GreenCoin nima?', 'Eko-vazifalar uchun beriladigan ichki mukofot valyutasi.'],
        ] as [$category, $question, $answer]) {
            Faq::create([
                'category' => $category,
                'question' => $question,
                'answer' => $answer,
                'is_published' => true,
            ]);
        }

        foreach ([
            ['streak_7', '7 kunlik seriya', 'Ketma-ket 7 kun tizimga kirish', 'flame', 15],
            ['coins_100', '100+ GreenCoin', 'Balans 100 coin dan oshishi', 'coin', 20],
            ['first_application', 'Birinchi ariza', 'Ishga birinchi arizangiz', 'briefcase', 10],
            ['first_eco', 'Birinchi eko qadam', 'Birinchi eko-vazifani bajarish', 'leaf', 10],
        ] as [$code, $title, $description, $icon, $reward]) {
            Achievement::create(compact('code', 'title', 'description', 'icon', 'reward'));
        }

        // ── Community demo content ──────────────────────────────────────────
        \App\Models\BlogPost::create([
            'user_id' => $demoUser->id,
            'title' => 'Yoshlar uchun IT sohasida qanday boshlash kerak?',
            'content' => "IT sohasini boshlash uchun eng muhim narsa — kichik qadamlar.\n\nAvval bir yo'nalishni tanlang: frontend, backend yoki dizayn. Keyin Academy kurslaridan birini boshlang va har kuni 30 daqiqa mashq qiling. Forumda savollar bering, mentorlardan yordam oling.\n\nEng asosiysi — to'xtamaslik!",
            'category' => 'Dasturlash',
            'tags' => ['IT', 'Yoshlar'],
            'status' => 'approved',
            'published_at' => now(),
        ]);

        $topic = \App\Models\ForumTopic::create([
            'user_id' => $demoUser->id,
            'title' => 'Birinchi ish topish uchun nimalarga e\'tibor berish kerak?',
            'category' => 'bandlik',
            'body' => 'Assalomu alaykum! Yaqinda universitetni tugatdim va birinchi ishimni qidiryapman. Rezyume tuzishda va intervyuda nimalarga e\'tibor berish kerak? Tajribangizni ulashing.',
        ]);

        \App\Models\ForumReply::create([
            'forum_topic_id' => $topic->id,
            'user_id' => $demoUser->id,
            'body' => 'Eng muhimi — portfolionizni tayyorlang. Kichik loyihalar ham katta ahamiyatga ega.',
        ]);

        $poll = \App\Models\Poll::create([
            'title' => 'Qaysi yo‘nalish sizga ko‘proq qiziq?',
            'allow_multiple' => false,
            'is_featured' => true,
        ]);
        foreach (['Dasturlash', 'Dizayn', 'Marketing', 'Ekologiya'] as $label) {
            \App\Models\PollOption::create(['poll_id' => $poll->id, 'label' => $label]);
        }

        \App\Models\SuccessStory::create([
            'user_id' => $demoUser->id,
            'author_name' => 'Jasur Toshmatov',
            'company' => 'EcoTrack',
            'title' => 'Hobby’dan startupgacha',
            'content' => 'Platforma orqali mentor topdim, 8 oy ichida o‘z ekologik startupimni ishga tushirdim. Izchillik — hamma narsa!',
            'is_published' => true,
        ]);
    }
}
