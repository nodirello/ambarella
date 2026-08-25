<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\EcoTaskCompletion;
use App\Models\Job;
use App\Models\Mentor;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PlatformStatsService
{
    public function homeStats(): array
    {
        return Cache::remember('platform.stats.home', 300, function (): array {
            return [
                'users' => User::count(),
                'jobs' => Job::active()->count(),
                'courses' => Course::published()->count(),
                'mentors' => Mentor::available()->count(),
                'eco_actions' => EcoTaskCompletion::where('status', 'approved')->count(),
                'eco_actions_today' => EcoTaskCompletion::where('status', 'approved')->whereDate('completed_on', today())->count(),
            ];
        });
    }

    public function adminStats(): array
    {
        return Cache::remember('platform.stats.admin', 120, function (): array {
            return [
                'users' => User::count(),
                'users_new_week' => User::where('created_at', '>=', now()->subWeek())->count(),
                'jobs' => Job::count(),
                'jobs_active' => Job::active()->count(),
                'applications' => \App\Models\JobApplication::count(),
                'applications_pending' => \App\Models\JobApplication::pending()->count(),
                'courses' => Course::count(),
                'mentors' => Mentor::count(),
                'eco_pending' => EcoTaskCompletion::pending()->count(),
                'coin_balance' => User::sum('greencoin_balance'),
            ];
        });
    }
}
