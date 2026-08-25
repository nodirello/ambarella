<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\EcoTaskCompletion;
use App\Models\JobApplication;
use App\Models\BlogPost;
use App\Services\PlatformStatsService;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(PlatformStatsService $stats): View
    {
        return view('admin.dashboard', [
            'stats' => $stats->adminStats(),
            'pending' => [
                'blog_posts' => BlogPost::pending()->count(),
                'eco' => EcoTaskCompletion::pending()->count(),
                'applications' => JobApplication::pending()->count(),
                'contacts' => ContactMessage::new()->count(),
            ],
            'recent_activities' => \App\Models\ActivityLog::recent(12)->get(),
        ]);
    }
}
