<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Banner;
use App\Models\Course;
use App\Models\EcoTask;
use App\Models\Faq;
use App\Models\Mentor;
use App\Models\News;
use App\Models\PlatformEvent;
use App\Models\Startup;
use App\Models\SuccessStory;
use App\Models\VolunteerProject;
use App\Services\ActivityLogger;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Generic admin CRUD for simple platform resources — single set of views,
 * one validation map, no duplicated controllers.
 */
class AdminGResourceController extends Controller
{
    private const RESOURCES = [
        'news' => [News::class, 'crud', 'content'],
        'courses' => [Course::class, 'crud', 'description'],
        'eco-tasks' => [EcoTask::class, 'crud', 'description'],
        'mentors' => [Mentor::class, 'crud', null],
        'events' => [PlatformEvent::class, 'crud', 'description'],
        'startups' => [Startup::class, 'crud', 'description'],
        'volunteer' => [VolunteerProject::class, 'crud', 'description'],
        'announcements' => [Announcement::class, 'crud', 'body'],
        'banners' => [Banner::class, 'crud', null],
        'faqs' => [Faq::class, 'crud', 'answer'],
        'stories' => [SuccessStory::class, 'crud', 'content'],
    ];

    private const RULES = [
        'news' => ['title' => 'required|string|max:200', 'content' => 'required|string|min:30', 'category' => 'required|string|max:60', 'image' => 'nullable|image|max:4096'],
        'courses' => ['title' => 'required|string|max:200', 'description' => 'required|string|min:30', 'instructor' => 'required|string|max:120', 'price' => 'required|integer|min:0', 'level' => 'required|string|max:20', 'category' => 'required|string|max:60', 'duration' => 'nullable|string|max:50', 'tags' => 'nullable|array', 'tags.*' => 'string|max:30'],
        'eco-tasks' => ['title' => 'required|string|max:200', 'description' => 'required|string|min:20', 'reward' => 'required|integer|min:1|max:10000', 'category' => 'required|string|max:60', 'requires_proof' => 'nullable|boolean'],
        'mentors' => ['name' => 'required|string|max:120', 'role' => 'required|string|max:120', 'experience_years' => 'required|integer|min:0|max:60', 'skills' => 'required|array|min:1', 'skills.*' => 'string|max:40', 'rating' => 'nullable|numeric|between:0,5', 'bio' => 'nullable|string|max:2000', 'is_available' => 'nullable|boolean'],
        'events' => ['title' => 'required|string|max:200', 'description' => 'required|string|min:30', 'city' => 'nullable|string|max:80', 'venue' => 'nullable|string|max:160', 'starts_at' => 'required|date', 'ends_at' => 'nullable|date|after:starts_at', 'capacity' => 'required|integer|min:0', 'price' => 'nullable|integer|min:0'],
        'startups' => ['name' => 'required|string|max:160', 'description' => 'required|string|min:30', 'category' => 'required|string|max:80', 'stage' => 'required|in:idea,mvp,growth,scale', 'looking_for' => 'nullable|string|max:500', 'website' => 'nullable|url'],
        'volunteer' => ['title' => 'required|string|max:200', 'description' => 'required|string|min:30', 'organization' => 'required|string|max:120', 'city' => 'nullable|string|max:80', 'hours_expected' => 'required|integer|min:0', 'capacity' => 'nullable|integer|min:0', 'starts_at' => 'nullable|date', 'ends_at' => 'nullable|date|after:starts_at'],
        'announcements' => ['title' => 'required|string|max:200', 'body' => 'required|string|min:10', 'priority' => 'nullable|in:normal,high,urgent', 'is_active' => 'boolean'],
        'banners' => ['title' => 'required|string|max:160', 'link_url' => 'nullable|url', 'placement' => 'required|string|max:30', 'image' => 'required|image|max:4096'],
        'faqs' => ['question' => 'required|string|max:500', 'answer' => 'required|string|min:10', 'category' => 'nullable|string|max:60', 'sort_order' => 'nullable|integer|min:0'],
        'stories' => ['author_name' => 'required|string|max:120', 'company' => 'nullable|string|max:120', 'title' => 'required|string|max:200', 'content' => 'required|string|min:30', 'photo' => 'nullable|image|max:4096', 'is_published' => 'boolean'],
    ];

    /** Upload field => storage column map per resource. */
    private const IMAGE_MAP = [
        'news' => ['image' => 'image'],
        'banners' => ['image' => 'image_path'],
        'stories' => ['photo' => 'photo_path'],
    ];

    public function index(string $resource): View
    {
        [$model, $view] = $this->resolve($resource);

        return view("admin.{$view}.index", [
            'resource' => $resource,
            'items' => $model::query()->latest()->paginate(15),
        ]);
    }

    public function create(string $resource): View
    {
        [$model, $view] = $this->resolve($resource);

        return view("admin.{$view}.form", [
            'resource' => $resource,
            'item' => null,
        ]);
    }

    public function store(Request $request, string $resource, ImageService $images, ActivityLogger $activity): RedirectResponse
    {
        [$model] = $this->resolve($resource);
        $item = $this->persist($request, $resource, $model, null, $images);

        $activity->log($request->user(), 'admin_create', "Yaratildi: {$resource} #{$item->getKey()}");

        return redirect()->route('admin.crud.index', $resource)->with('success', 'Muvaffaqiyatli yaratildi.');
    }

    public function edit(string $resource, int $id): View
    {
        [$model, $view] = $this->resolve($resource);

        return view("admin.{$view}.form", [
            'resource' => $resource,
            'item' => $model::findOrFail($id),
        ]);
    }

    public function update(Request $request, string $resource, int $id, ImageService $images): RedirectResponse
    {
        [$model] = $this->resolve($resource);
        $this->persist($request, $resource, $model, $model::findOrFail($id), $images);

        return back()->with('success', 'Yangilandi.');
    }

    public function destroy(string $resource, int $id): RedirectResponse
    {
        [$model] = $this->resolve($resource);
        $model::findOrFail($id)->delete();

        return back()->with('success', 'O‘chirildi.');
    }

    public function toggle(Request $request, string $resource, int $id): RedirectResponse
    {
        [$model] = $this->resolve($resource);
        $item = $model::findOrFail($id);

        $field = match ($resource) {
            'news' => 'is_published',
            'courses' => 'is_published',
            'eco-tasks' => 'is_active',
            'mentors' => 'is_available',
            'events' => 'is_published',
            'startups' => 'is_approved',
            'volunteer' => 'is_active',
            'default' => 'is_active',
        };

        if (! $item->getAttribute($field)) {
            $item->setAttribute($field, true);
        } else {
            $item->setAttribute($field, false);
        }
        $item->save();

        return back()->with('success', 'Holat o‘zgartirildi.');
    }

    private function persist(Request $request, string $resource, string $model, ?\Illuminate\Database\Eloquent\Model $item, ImageService $images): \Illuminate\Database\Eloquent\Model
    {
        $data = $request->validate(self::RULES[$resource]);

        foreach (self::IMAGE_MAP[$resource] ?? [] as $uploadField => $column) {
            if ($request->hasFile($uploadField)) {
                $data[$column] = $images->upload($request->file($uploadField), $resource);
            }
            unset($data[$uploadField]);
        }

        if ($resource === 'news' && $item === null) {
            $data['author_name'] = $request->user()->name;
        }

        if ($item) {
            $item->update($data);

            return $item;
        }

        return $model::create($data);
    }

    private function resolve(string $resource): array
    {
        abort_unless(isset(self::RESOURCES[$resource]), 404);

        return self::RESOURCES[$resource];
    }
}
