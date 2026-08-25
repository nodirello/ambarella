<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\News;
use App\Services\PlatformStatsService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(PlatformStatsService $stats): View
    {
        return view('home', [
            'stats' => $stats->homeStats(),
            'jobs' => Job::active()->with('businessProfile')->latest()->take(4)->get(),
            'news' => News::published()->latest()->take(3)->get(),
            'announcements' => \App\Models\Announcement::visible()->latest()->take(3)->get(),
        ]);
    }
}
