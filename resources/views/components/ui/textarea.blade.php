@props(['label' => null, 'name', 'value' => null, 'placeholder' => null, 'rows' => 4, 'required' => false])

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium">{{ $label }}</label>
    @endif
    <textarea name="{{ $name }}" id="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}"
              @required($required)
              {{ $attributes->merge(['class' => 'w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-900']) }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>
    @enderror
</div>
