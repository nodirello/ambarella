<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Admin\AdminApplicationController;
use App\Http\Controllers\Admin\AdminContactController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminExportController;
use App\Http\Controllers\Admin\AdminGResourceController;
use App\Http\Controllers\Admin\AdminGreenCoinController;
use App\Http\Controllers\Admin\AdminJobController;
use App\Http\Controllers\Admin\AdminModerationController;
use App\Http\Controllers\Admin\AdminTelegramController;
use App\Http\Controllers\Admin\AdminUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel — protected by auth + admin + 2fa
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'banned', 'admin', '2fa'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/ban', [AdminUserController::class, 'toggleBan'])->name('users.ban');
    Route::post('/users/{user}/role', [AdminUserController::class, 'toggleAdmin'])->name('users.role');

    // Jobs
    Route::resource('jobs', AdminJobController::class)->except('show')->parameters(['jobs' => 'job']);
    Route::post('/jobs/{job}/toggle', [AdminJobController::class, 'toggle'])->name('jobs.toggle');

    // Applications
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::post('/applications/{application}/status', [AdminApplicationController::class, 'updateStatus'])->name('applications.status');

    // Moderation
    Route::get('/moderation', [AdminModerationController::class, 'index'])->name('moderation.index');
    Route::post('/moderation/blog/{post}/approve', [AdminModerationController::class, 'approveBlogPost'])->name('moderation.blog.approve');
    Route::post('/moderation/blog/{post}/reject', [AdminModerationController::class, 'rejectBlogPost'])->name('moderation.blog.reject');
    Route::post('/moderation/eco/{completion}/approve', [AdminModerationController::class, 'approveEco'])->name('moderation.eco.approve');
    Route::post('/moderation/eco/{completion}/reject', [AdminModerationController::class, 'rejectEco'])->name('moderation.eco.reject');
    Route::post('/moderation/comments/{comment}/toggle', [AdminModerationController::class, 'toggleComment'])->name('moderation.comments.toggle');

    // GreenCoin
    Route::get('/greencoin', [AdminGreenCoinController::class, 'index'])->name('greencoin.index');
    Route::post('/greencoin/users/{user}/adjust', [AdminGreenCoinController::class, 'adjust'])->name('greencoin.adjust');

    // Generic CRUD resources — {resource} is the literal param name,
    // resolved from the URL (e.g. /admin/crud/news).
    Route::get('/crud/{resource}', [AdminGResourceController::class, 'index'])->whereIn('resource', ['news', 'courses', 'eco-tasks', 'mentors', 'events', 'startups', 'volunteer', 'announcements', 'banners', 'faqs', 'stories'])->name('crud.index');
    Route::get('/crud/{resource}/create', [AdminGResourceController::class, 'create'])->whereIn('resource', ['news', 'courses', 'eco-tasks', 'mentors', 'events', 'startups', 'volunteer', 'announcements', 'banners', 'faqs', 'stories'])->name('crud.create');
    Route::post('/crud/{resource}', [AdminGResourceController::class, 'store'])->whereIn('resource', ['news', 'courses', 'eco-tasks', 'mentors', 'events', 'startups', 'volunteer', 'announcements', 'banners', 'faqs', 'stories'])->name('crud.store');
    Route::get('/crud/{resource}/{id}/edit', [AdminGResourceController::class, 'edit'])->whereNumber('id')->whereIn('resource', ['news', 'courses', 'eco-tasks', 'mentors', 'events', 'startups', 'volunteer', 'announcements', 'banners', 'faqs', 'stories'])->name('crud.edit');
    Route::put('/crud/{resource}/{id}', [AdminGResourceController::class, 'update'])->whereNumber('id')->whereIn('resource', ['news', 'courses', 'eco-tasks', 'mentors', 'events', 'startups', 'volunteer', 'announcements', 'banners', 'faqs', 'stories'])->name('crud.update');
    Route::delete('/crud/{resource}/{id}', [AdminGResourceController::class, 'destroy'])->whereNumber('id')->whereIn('resource', ['news', 'courses', 'eco-tasks', 'mentors', 'events', 'startups', 'volunteer', 'announcements', 'banners', 'faqs', 'stories'])->name('crud.destroy');
    Route::post('/crud/{resource}/{id}/toggle', [AdminGResourceController::class, 'toggle'])->whereNumber('id')->whereIn('resource', ['news', 'courses', 'eco-tasks', 'mentors', 'events', 'startups', 'volunteer', 'announcements', 'banners', 'faqs', 'stories'])->name('crud.toggle');

    // Contacts & logs & exports
    Route::get('/contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{message}', [AdminContactController::class, 'show'])->name('contacts.show');
    Route::delete('/contacts/{message}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');
    Route::get('/activity-log', [AdminActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('/export/users', [AdminExportController::class, 'users'])->name('export.users');
    Route::get('/export/applications', [AdminExportController::class, 'applications'])->name('export.applications');

    // Telegram
    Route::prefix('telegram')->name('telegram.')->group(function () {
        Route::get('/', [AdminTelegramController::class, 'index'])->name('dashboard');
        Route::get('/submissions', [AdminTelegramController::class, 'submissions'])->name('submissions');
        Route::post('/submissions/{submission}/approve', [AdminTelegramController::class, 'approveSubmission'])->name('submissions.approve');
        Route::post('/submissions/{submission}/reject', [AdminTelegramController::class, 'rejectSubmission'])->name('submissions.reject');
        Route::get('/tasks', [AdminTelegramController::class, 'tasks'])->name('tasks');
        Route::post('/tasks', [AdminTelegramController::class, 'storeTask'])->name('tasks.store');
        Route::post('/tasks/{task}/toggle', [AdminTelegramController::class, 'toggleTask'])->name('tasks.toggle');
        Route::post('/broadcast', [AdminTelegramController::class, 'broadcast'])->middleware('throttle:5,1')->name('broadcast');
        Route::post('/set-webhook', [AdminTelegramController::class, 'setWebhook'])->name('set-webhook');
    });
});
