@props(['href', 'mobile' => false])

<a href="{{ $href }}"
   {{ $attributes->class([
       'grid place-items-center rounded-lg px-3 py-2 transition-colors',
       'lg:py-1.5' => ! $mobile,
       'bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-white' => $mobile && request()->url() === $href,
       'text-slate-900 dark:text-white' => ! $mobile && request()->url() === $href,
   ]) }}>
    {{ $slot }}
</a>
