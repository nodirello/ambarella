@extends('layouts.admin')

@section('title', ($item ? 'Tahrir' : 'Yangi').' '.ucfirst(str_replace('-', ' ', $resource)).' — Admin')
@section('admin-title', ($item ? 'Tahrirlash' : 'Yangi yaratish').' — '.ucfirst(str_replace('-', ' ', $resource)))

@php
    $fields = collect([
        'news' => [
            'title' => ['text', 'Sarlavha', null],
            'category' => ['text', 'Kategoriya', 'Platforma, Ekologiya…'],
            'content' => ['textarea', 'Matn', 8],
            'image' => ['file', 'Rasm', null],
        ],
        'courses' => [
            'title' => ['text', 'Nomi', null],
            'instructor' => ['text', 'O‘qituvchi', null],
            'price' => ['number', 'Narx (0 = bepul)', 0],
            'level' => ['select', 'Daraja', ['beginner' => 'Boshlang‘ich', 'intermediate' => 'O‘rta', 'advanced' => 'Yuqori'], 'beginner'],
            'category' => ['text', 'Kategoriya', 'Dasturlash'],
            'duration' => ['text', 'Davomiylik', '3 oy'],
            'description' => ['textarea', 'Tavsif', null],
        ],
        'eco-tasks' => [
            'title' => ['text', 'Nomi', null],
            'category' => ['text', 'Kategoriya', 'Chiqindi'],
            'reward' => ['number', 'Mukofot (🪙)', 5],
            'description' => ['textarea', 'Tavsif', null],
        ],
        'mentors' => [
            'name' => ['text', 'Ism', null],
            'role' => ['text', 'Lavozim', 'Senior Developer'],
            'experience_years' => ['number', 'Tajriba (yil)', 3],
            'skills' => ['text', 'Ko‘nikmalar (vergul bilan)', 'React, Laravel'],
            'bio' => ['textarea', 'Tavsif', null],
        ],
        'events' => [
            'title' => ['text', 'Nomi', null],
            'city' => ['text', 'Shahar', 'Toshkent'],
            'venue' => ['text', 'Manzil', null],
            'starts_at' => ['datetime-local', 'Boshlanish', null],
            'capacity' => ['number', 'Sig‘im', 100],
            'price' => ['number', 'Narx (0 = bepul)', 0],
            'description' => ['textarea', 'Tavsif', null],
        ],
        'startups' => [
            'name' => ['text', 'Nomi', null],
            'category' => ['text', 'Kategoriya', 'IT'],
            'stage' => ['select', 'Bosqich', ['idea' => 'G‘oya', 'mvp' => 'MVP', 'growth' => 'O‘sish', 'scale' => 'Kengayish'], 'idea'],
            'looking_for' => ['text', 'Qidirayotgani', 'Investor'],
            'description' => ['textarea', 'Tavsif', null],
        ],
        'volunteer' => [
            'title' => ['text', 'Nomi', null],
            'organization' => ['text', 'Tashkilot', null],
            'city' => ['text', 'Shahar', null],
            'hours_expected' => ['number', 'Kutilgan soat', 4],
            'capacity' => ['number', 'Sig‘im', 50],
            'description' => ['textarea', 'Tavsif', null],
        ],
        'announcements' => [
            'title' => ['text', 'Sarlavha', null],
            'priority' => ['select', 'Muhimlik', ['normal' => 'Oddiy', 'high' => 'Muhim', 'urgent' => 'Shoshilinch'], 'normal'],
            'body' => ['textarea', 'Matn', null],
        ],
        'banners' => [
            'title' => ['text', 'Sarlavha', null],
            'link_url' => ['text', 'Havola', 'https://…'],
            'placement' => ['text', 'Joylashuv', 'home'],
            'image' => ['file', 'Rasm', null],
        ],
        'faqs' => [
            'question' => ['text', 'Savol', null],
            'category' => ['text', 'Kategoriya', 'Register'],
            'answer' => ['textarea', 'Javob', null],
            'sort_order' => ['number', 'Tartib', 1],
        ],
        'stories' => [
            'author_name' => ['text', 'Muallif', null],
            'company' => ['text', 'Kompaniya', null],
            'title' => ['text', 'Sarlavha', null],
            'photo' => ['file', 'Rasm', null],
            'content' => ['textarea', 'Matn', null],
        ],
    ]);

    $visibleFields = $fields[$resource] ?? [];
@endphp

@section('content')
<div class="max-w-2xl rounded-3xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900/60">
    <form method="POST" action="{{ $item ? route('admin.crud.update', [$resource, $item->id]) : route('admin.crud.store', $resource) }}"
          enctype="multipart/form-data" class="grid gap-5">
        @csrf
        @if ($item)
            @method('PUT')
        @endif

        @foreach ($visibleFields as $name => $config)
            @php [$type, $label, $extra] = $config; @endphp
            @if ($type === 'textarea')
                <x-ui.textarea name="{{ $name }}" label="{{ $label }}" :value="$item?->{$name}" rows="{{ is_numeric($extra) ? $extra : 5 }}" />
            @elseif ($type === 'select')
                <x-ui.select name="{{ $name }}" label="{{ $label }}" :options="$extra" :value="$item?->{$name}" />
            @elseif ($type === 'file')
                <x-ui.input name="{{ $name }}" label="{{ $label }}" type="file" accept="image/*" />
            @elseif ($type === 'datetime-local')
                <x-ui.input name="{{ $name }}" label="{{ $label }}" type="datetime-local" :value="$item?->{$name}?->format('Y-m-d\TH:i')" />
            @else
                <x-ui.input name="{{ $name }}" label="{{ $label }}" :type="$type" :value="is_numeric($extra) ? ($item?->{$name} ?? $extra) : ($item?->{$name} ?? null)" :placeholder="is_string($extra) ? $extra : null" />
            @endif
        @endforeach

        <x-ui.button>Saqlash</x-ui.button>
    </form>
</div>
@endsection
