@php
    $user = auth()->user();
@endphp

<aside id="app-sidebar" x-data="sidebar"
       class="app-sidebar flex shrink-0 flex-col bg-gradient-to-b from-[#8b1c1e] via-[#741517] to-[#5b1012] py-7 lg:sticky lg:top-0 lg:h-screen">
    {{-- Brand --}}
    <div class="flex flex-col items-center">
        <div class="sidebar-head relative flex w-full items-center justify-center gap-3">
            <a href="{{ route('dashboard') }}" class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white shadow-lg">
                <x-brand-logo class="h-11 w-11" />
            </a>

            <button type="button"
                    @click="toggle()"
                    :aria-expanded="collapsed ? 'false' : 'true'"
                    :title="collapsed ? '{{ __('Perbesar sidebar') }}' : '{{ __('Perkecil sidebar') }}'"
                    aria-controls="app-sidebar"
                    class="sidebar-toggle hidden h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-white/80 transition hover:bg-white/20 hover:text-white lg:inline-flex">
                <x-icon name="chevrons-left" class="sidebar-toggle-icon h-4 w-4 transition-transform" />
            </button>
        </div>

        <a href="{{ route('dashboard') }}" class="sidebar-label mt-4 text-2xl font-bold tracking-tight text-white">
            Track<span class="text-brand-300">Kas</span>
        </a>

        <span class="sidebar-label mt-1 text-[11px] font-medium uppercase tracking-[0.2em] text-white/40">{{ __('Kelola Keuanganmu') }}</span>
    </div>

    <div class="my-6 border-t border-white/15"></div>

    {{-- Signed in user / profile link --}}
    <a href="{{ route('profile.edit') }}" title="{{ __('Profil') }}" aria-label="{{ __('Profil') }}"
       class="sidebar-center flex items-center gap-3 rounded-xl bg-black/15 px-3 py-3 transition hover:bg-black/25 {{ request()->routeIs('profile.*') ? 'ring-1 ring-white/40' : '' }}">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-100 text-sm font-bold text-brand-700">
            {{ Str::upper(Str::substr($user->name, 0, 2)) }}
        </span>

        <span class="sidebar-label min-w-0">
            <span class="block truncate text-sm font-semibold text-white">{{ $user->name }}</span>
            <span class="block truncate text-xs text-white/60">{{ $user->email }}</span>
        </span>
    </a>

    {{-- Navigation: one short line per menu thanks to x-sidebar-link --}}
    <nav class="mt-5 flex flex-col gap-1">
        <x-sidebar-link route="dashboard" icon="dashboard" :active="request()->routeIs('dashboard')">Dashboard</x-sidebar-link>
        <x-sidebar-link route="transaksi.index" icon="list" :active="request()->routeIs('transaksi.index')">Transaksi</x-sidebar-link>
    </nav>

    {{-- Log out --}}
    <form method="POST" action="{{ route('logout') }}" class="mt-6 lg:mt-auto lg:pt-6">
        @csrf

        <button type="submit" title="{{ __('Keluar') }}" aria-label="{{ __('Keluar') }}"
                class="sidebar-center flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-white/75 transition hover:bg-white/10 hover:text-white">
            <x-icon name="logout" class="h-5 w-5 shrink-0" stroke-width="1.8" />

            <span class="sidebar-label">{{ __('Keluar') }}</span>
        </button>
    </form>
</aside>
