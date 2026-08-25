@props(['label' => null, 'name', 'options' => [], 'value' => null, 'placeholder' => null])

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium">{{ $label }}</label>
    @endif
    <select name="{{ $name }}" id="{{ $name }}"
            {{ $attributes->merge(['class' => 'w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-900']) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $key => $labelValue)
            <option value="{{ $key }}" @selected(old($name, $value) == $key)>{{ $labelValue }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>
    @enderror
</div>
