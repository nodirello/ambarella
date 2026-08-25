<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Job;
use App\Models\News;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $static = [
            ['/', '1.0', 'daily'],
            ['/jobs', '0.9', 'daily'],
            ['/eco', '0.8', 'daily'],
            ['/academy', '0.9', 'weekly'],
            ['/mentor', '0.8', 'weekly'],
            ['/news', '0.8', 'daily'],
            ['/events', '0.7', 'weekly'],
            ['/challenges', '0.7', 'weekly'],
            ['/startups', '0.7', 'weekly'],
            ['/volunteer', '0.7', 'monthly'],
            ['/forum', '0.7', 'weekly'],
            ['/blog', '0.8', 'daily'],
            ['/about', '0.6', 'monthly'],
            ['/contact', '0.6', 'monthly'],
        ];

        $xml = collect($static)->map(
            fn (array $item) => $this->url($item[0], $item[1], $item[2])
        )->concat(
            Job::active()->select('id', 'updated_at')->get()
                ->map(fn ($m) => $this->url("/jobs/{$m->id}", '0.7', 'weekly', $m->updated_at))
        )->concat(
            Course::published()->select('id', 'updated_at')->get()
                ->map(fn ($m) => $this->url("/academy/{$m->id}", '0.7', 'weekly', $m->updated_at))
        )->concat(
            News::published()->select('id', 'updated_at')->get()
                ->map(fn ($m) => $this->url("/news/{$m->id}", '0.6', 'monthly', $m->updated_at))
        )->implode("\n");

        return response('<'.'?xml version="1.0" encoding="UTF-8"?'.">\n"
            ."<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n{$xml}\n</urlset>")
            ->header('Content-Type', 'application/xml');
    }

    private function url(string $path, string $priority, string $freq, ?\DateTimeInterface $lastmod = null): string
    {
        $lastmod = $lastmod ? "<lastmod>{$lastmod->format('c')}</lastmod>" : '';

        return "<url><loc>".url($path)."</loc>{$lastmod}<changefreq>{$freq}</changefreq><priority>{$priority}</priority></url>";
    }
}
