<?php

declare(strict_types=1);

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DailyTaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EcoTaskController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GreenCoinController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LastViewedController;
use App\Http\Controllers\LiveCountController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\MentorshipController;
use App\Http\Controllers\MyApplicationController;
use App\Http\Controllers\MyCourseController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PsychologyController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StartupController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\TelegramAuthController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Business\BusinessAnalyticsController;
use App\Http\Controllers\Business\BusinessApplicationController;
use App\Http\Controllers\Business\BusinessDashboardController;
use App\Http\Controllers\Business\BusinessJobController;
use App\Http\Controllers\Business\BusinessProductController;
use App\Http\Controllers\Business\BusinessProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/offline', fn () => view('offline'))->name('offline');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:3,1')->name('contact.store');
Route::get('/modules', [PageController::class, 'modules'])->name('modules');
Route::get('/faqs', [PageController::class, 'faqs'])->name('faqs');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/certificate/verify/{code}', [CertificateController::class, 'verify'])->name('certificate.verify');
Route::get('/user/{user}', [\App\Http\Controllers\UserProfileController::class, 'show'])->name('user.profile');

Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
Route::get('/eco', [\App\Http\Controllers\EcoController::class, 'index'])->name('eco.index');
Route::get('/academy', [CourseController::class, 'index'])->name('academy.index');
Route::get('/academy/{course}', [CourseController::class, 'show'])->name('academy.show');
Route::get('/mentor', [MentorController::class, 'index'])->name('mentor.index');
Route::get('/mentor/{mentor}', [MentorController::class, 'show'])->name('mentor.show');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [BlogController::class, 'show'])->whereNumber('post')->name('blog.show');
Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/{topic}', [ForumController::class, 'show'])->whereNumber('topic')->name('forum.show');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
Route::get('/startups', [StartupController::class, 'index'])->name('startups.index');
Route::get('/startups/{startup}', [StartupController::class, 'show'])->whereNumber('startup')->name('startups.show');
Route::get('/volunteer', [VolunteerController::class, 'index'])->name('volunteer.index');
Route::get('/farm', [FarmController::class, 'index'])->name('farm.index');
Route::get('/farm/{product}', [FarmController::class, 'show'])->name('farm.show');
Route::get('/polls', [PollController::class, 'index'])->name('polls.index');
Route::get('/polls/{poll}', [PollController::class, 'show'])->name('polls.show');
Route::get('/psychology', [PsychologyController::class, 'index'])->name('psychology.index');
Route::get('/psychology/{slug}', [PsychologyController::class, 'show'])->name('psychology.show');
Route::post('/psychology/{slug}', [PsychologyController::class, 'submit'])->middleware('throttle:10,1')->name('psychology.submit');

/*
|--------------------------------------------------------------------------
| Health & live counters (public, throttled)
|--------------------------------------------------------------------------
*/
Route::get('/up', [HealthController::class, 'check'])->name('health');
Route::get('/api/live-count', [LiveCountController::class, 'count'])->middleware('throttle:30,1')->name('live.count');

/*
|--------------------------------------------------------------------------
| Guest auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1')->name('register.store');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'requestReset'])->middleware('throttle:3,1')->name('password.email');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

    Route::get('/auth/telegram/callback', [TelegramAuthController::class, 'callback'])->name('telegram.callback');
    Route::post('/auth/telegram/send-code', [TelegramAuthController::class, 'sendCode'])->middleware('throttle:3,1')->name('telegram.send-code');
    Route::post('/auth/telegram/verify-code', [TelegramAuthController::class, 'verifyCode'])->middleware('throttle:5,1')->name('telegram.verify-code');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Email verification / 2FA
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])->middleware('throttle:3,1')->name('verification.send');

    Route::get('/2fa/challenge', [TwoFactorController::class, 'showChallenge'])->name('2fa.challenge');
    Route::post('/2fa/challenge', [TwoFactorController::class, 'challenge'])->middleware('throttle:5,1')->name('2fa.verify');
    Route::get('/2fa/recovery', [TwoFactorController::class, 'recoveryCodes'])->name('2fa.recovery');
});

/*
|--------------------------------------------------------------------------
| Authenticated area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'banned'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    Route::middleware('profile.complete')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/2fa/enable', [SettingsController::class, 'enableTwoFactor'])->name('settings.2fa.enable');
        Route::post('/settings/2fa/disable', [SettingsController::class, 'disableTwoFactor'])->name('settings.2fa.disable');
        Route::post('/settings/tokens', [SettingsController::class, 'createApiToken'])->name('settings.tokens.create');
        Route::delete('/settings/tokens/{token}', [SettingsController::class, 'revokeApiToken'])->name('settings.tokens.revoke');
        Route::get('/settings/export', [SettingsController::class, 'exportData'])->name('settings.export');
        Route::delete('/settings/account', [SettingsController::class, 'deleteAccount'])->name('settings.account.delete');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all');

        Route::get('/greencoin', [GreenCoinController::class, 'index'])->name('greencoin.index');
        Route::post('/greencoin/transfer', [GreenCoinController::class, 'transfer'])->middleware('throttle:5,1')->name('greencoin.transfer');
        Route::get('/greencoin/leaderboard', [GreenCoinController::class, 'leaderboard'])->name('greencoin.leaderboard');

        Route::get('/my-courses', [MyCourseController::class, 'index'])->name('my-courses.index');
        Route::post('/my-courses/{course}/progress', [MyCourseController::class, 'updateProgress'])->name('my-courses.progress');
        Route::get('/my-applications', [MyApplicationController::class, 'index'])->name('my-applications.index');
        Route::get('/last-viewed', [LastViewedController::class, 'index'])->name('last-viewed.index');
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
        Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
        Route::get('/referral', [ReferralController::class, 'index'])->name('referral.index');
        Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');

        Route::get('/daily-tasks', [DailyTaskController::class, 'index'])->name('daily-tasks.index');
        Route::post('/daily-tasks', [DailyTaskController::class, 'store'])->name('daily-tasks.store');
        Route::post('/daily-tasks/{task}/toggle', [DailyTaskController::class, 'toggle'])->name('daily-tasks.toggle');
        Route::delete('/daily-tasks/{task}', [DailyTaskController::class, 'destroy'])->name('daily-tasks.destroy');

        Route::get('/habits', [HabitController::class, 'index'])->name('habits.index');
        Route::post('/habits', [HabitController::class, 'store'])->name('habits.store');
        Route::post('/habits/{habit}/complete', [HabitController::class, 'complete'])->name('habits.complete');
        Route::delete('/habits/{habit}', [HabitController::class, 'destroy'])->name('habits.destroy');

        Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
        Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
        Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
        Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
        Route::post('/notes/{note}/pin', [NoteController::class, 'togglePin'])->name('notes.pin');

        Route::get('/mentorship', [MentorshipController::class, 'index'])->name('mentorship.index');
        Route::get('/mentorship/create', [MentorshipController::class, 'create'])->name('mentorship.create');
        Route::post('/mentorship', [MentorshipController::class, 'store'])->name('mentorship.store');
        Route::post('/mentorship/{mentorship}/accept', [MentorshipController::class, 'accept'])->name('mentorship.accept');
        Route::post('/mentorship/{mentorship}/reject', [MentorshipController::class, 'reject'])->name('mentorship.reject');
        Route::post('/mentorship/{mentorship}/task', [MentorshipController::class, 'addTask'])->name('mentorship.task');
        Route::post('/mentorship/tasks/{task}/complete', [MentorshipController::class, 'completeTask'])->name('mentorship.task.complete');

        Route::post('/jobs/{job}/apply', [JobController::class, 'apply'])->name('jobs.apply');
        Route::post('/eco/tasks/{task}/complete', [EcoTaskController::class, 'complete'])->middleware('throttle:10,1')->name('eco.complete');
        Route::post('/academy/{course}/enroll', [CourseController::class, 'enroll'])->name('academy.enroll');
        Route::post('/mentor/{mentor}/request', [MentorController::class, 'requestSession'])->name('mentor.request');
        Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
        Route::post('/challenges/{challenge}/join', [ChallengeController::class, 'join'])->name('challenges.join');
        Route::post('/startups/{startup}/vote', [StartupController::class, 'vote'])->middleware('throttle:10,1')->name('startups.vote');
        Route::post('/volunteer/{project}/apply', [VolunteerController::class, 'apply'])->name('volunteer.apply');
        Route::post('/polls/{poll}/vote', [PollController::class, 'vote'])->name('polls.vote');

        Route::get('/blog/create', [BlogController::class, 'create'])->name('blog.create');
        Route::post('/blog', [BlogController::class, 'store'])->name('blog.store');
        Route::get('/startups/create', [StartupController::class, 'create'])->name('startups.create');
        Route::post('/startups', [StartupController::class, 'store'])->name('startups.store');
        Route::get('/forum/create', [ForumController::class, 'create'])->name('forum.create');
        Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
        Route::get('/pomodoro', fn () => view('pomodoro.index'))->name('pomodoro.index');
        Route::get('/playground', fn () => view('code-playground.index'))->name('playground.index');
        Route::get('/code-playground', fn () => view('code-playground.index'))->name('code-playground.index');
        Route::post('/forum/{topic}/reply', [ForumController::class, 'reply'])->name('forum.reply');
        Route::post('/forum/replies/{reply}/best', [ForumController::class, 'markBest'])->name('forum.best');
        Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
        Route::post('/reactions/toggle', [ReactionController::class, 'toggle'])->middleware('throttle:30,1')->name('reactions.toggle');

        Route::get('/certificate/{course}', [CertificateController::class, 'generate'])->name('certificate.generate');

        /*
        | Business portal
        */
        Route::prefix('business')->name('business.')->group(function () {
            Route::get('/', [BusinessDashboardController::class, 'index'])->name('dashboard');
            Route::get('/register', [BusinessProfileController::class, 'register'])->name('register');
            Route::post('/register', [BusinessProfileController::class, 'store'])->name('register.store');
            Route::get('/profile', [BusinessProfileController::class, 'show'])->name('profile');
            Route::post('/profile', [BusinessProfileController::class, 'update'])->name('profile.update');
            Route::get('/jobs', [BusinessJobController::class, 'index'])->name('jobs');
            Route::get('/jobs/create', [BusinessJobController::class, 'create'])->name('jobs.create');
            Route::post('/jobs', [BusinessJobController::class, 'store'])->name('jobs.store');
            Route::post('/jobs/{job}/toggle', [BusinessJobController::class, 'toggle'])->name('jobs.toggle');
            Route::get('/applications', [BusinessApplicationController::class, 'index'])->name('applications');
            Route::post('/applications/{application}/status', [BusinessApplicationController::class, 'updateStatus'])->name('applications.status');
            Route::get('/products', [BusinessProductController::class, 'index'])->name('products');
            Route::post('/products', [BusinessProductController::class, 'store'])->name('products.store');
            Route::get('/analytics', [BusinessAnalyticsController::class, 'index'])->name('analytics');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Telegram bot webhook (CSRF excluded in bootstrap/app.php)
|--------------------------------------------------------------------------
*/
Route::post('/webhook/telegram', [\App\Http\Controllers\Telegram\TelegramBotController::class, 'webhook'])->name('telegram.webhook');

/*
|--------------------------------------------------------------------------
| Telegram Mini App
|--------------------------------------------------------------------------
*/
Route::get('/tg-app', [\App\Http\Controllers\Telegram\TelegramWebAppController::class, 'index'])->name('tg-app.index');
