@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    <div class="flex items-center gap-3">
        <x-brand-logo class="h-7 w-7" />

        <h1 class="text-lg font-bold text-gray-900">{{ $title }}</h1>
    </div>

    @if ($subtitle)
        <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
    @endif
</div>
