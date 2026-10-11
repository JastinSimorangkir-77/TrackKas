{{--
    A sidebar navigation item.

    The same long list of Tailwind classes (active vs. inactive, the white marker
    bar, the icon and the label) used to be copy-pasted for every menu. It now
    lives here once.

    Usage:  <x-sidebar-link route="dashboard" icon="dashboard" :active="request()->routeIs('dashboard')">Dashboard</x-sidebar-link>
--}}
@props(['route', 'icon', 'active' => false])

<a href="{{ route($route) }}" title="{{ $slot }}" aria-label="{{ $slot }}"
   class="sidebar-center relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ $active ? 'bg-white/15 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
    @if ($active)
        <span class="sidebar-indicator absolute top-1/2 h-8 w-1 -translate-y-1/2 rounded-r-full bg-white"></span>
    @endif

    <x-icon :name="$icon" class="h-5 w-5 shrink-0" stroke-width="1.8" />

    <span class="sidebar-label">{{ $slot }}</span>
</a>
