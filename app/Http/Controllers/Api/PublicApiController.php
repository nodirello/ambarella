<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Job;
use App\Models\Mentor;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Read-only public API v1 — versioned, paginated, throttled.
 */
class PublicApiController extends Controller
{
    public function jobs(Request $request): AnonymousResourceCollection
    {
        $jobs = Job::active()
            ->filter($request->only(['search', 'type', 'city']))
            ->with('businessProfile')
            ->latest()
            ->paginate(min((int) $request->input('per_page', 15), 50));

        return JsonResource::collection($jobs);
    }

    public function courses(): AnonymousResourceCollection
    {
        return JsonResource::collection(
            Course::published()->latest()->paginate(min((int) request('per_page', 15), 50))
        );
    }

    public function news(): AnonymousResourceCollection
    {
        return JsonResource::collection(
            News::published()->latest()->paginate(min((int) request('per_page', 15), 50))
        );
    }

    public function mentors(): AnonymousResourceCollection
    {
        return JsonResource::collection(
            Mentor::available()->latest()->paginate(min((int) request('per_page', 15), 50))
        );
    }

    public function stats(): array
    {
        return app(\App\Services\PlatformStatsService::class)->homeStats();
    }
}
