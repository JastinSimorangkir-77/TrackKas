{{--
    One place for every small icon the app draws.

    Usage:  <x-icon name="plus" class="h-4 w-4" />
    Callers only pass a size ("h-4 w-4") and, when the design calls for a thinner
    line, a stroke-width. Everything else (viewBox, colours, line caps) is set
    once here instead of being repeated in every view.
--}}
@props(['name'])

<svg {{ $attributes->merge([
        'viewBox' => '0 0 24 24',
        'fill' => 'none',
        'stroke' => 'currentColor',
        'stroke-width' => '2',
        'stroke-linecap' => 'round',
        'stroke-linejoin' => 'round',
        'aria-hidden' => 'true',
    ]) }}>
    @switch($name)
        @case('plus')
            <path d="M12 4.5v15m7.5-7.5h-15" />
            @break

        @case('arrow-up')
            <path d="M12 4.5v15m0 0-6-6m6 6 6-6" />
            @break

        @case('arrow-down')
            <path d="M12 19.5v-15m0 0-6 6m6-6 6 6" />
            @break

        @case('wallet')
            <ellipse cx="12" cy="7" rx="6.5" ry="2.75" />
            <path d="M5.5 7v4.5c0 1.52 2.91 2.75 6.5 2.75s6.5-1.23 6.5-2.75V7" />
            <path d="M5.5 11.5V16c0 1.52 2.91 2.75 6.5 2.75s6.5-1.23 6.5-2.75v-4.5" />
            @break

        @case('info')
            {{-- A solid glyph, so the fill is set on the path and the inherited stroke is switched off. --}}
            <path fill="currentColor" stroke="none" fill-rule="evenodd" clip-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.25-4.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Zm1.5 4.125c.621 0 1.125.504 1.125 1.125v4.5a1.125 1.125 0 0 1-2.25 0v-4.5c0-.621.504-1.125 1.125-1.125Z" />
            @break

        @case('empty-list')
            <path d="M8.25 6.75h7.5M8.25 12h7.5M8.25 17.25h4.5M3.75 4.5h16.5v15H3.75v-15Z" />
            @break

        @case('empty-box')
            <path d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
            @break

        @case('dashboard')
            <path d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
            <path d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
            @break

        @case('list')
            <path d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            @break

        @case('plus-circle')
            <path d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            @break

        @case('report')
            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" />
            @break

        @case('download')
            <path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            @break

        @case('logout')
            <path d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15" />
            <path d="M12 9l-3 3m0 0 3 3m-3-3h12.75" />
            @break

        @case('chevrons-left')
            <path d="M18.75 19.5 11.25 12l7.5-7.5M12.75 19.5 4.5 12l8.25-7.5" />
            @break

        @case('search')
            <path d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
            @break
    @endswitch
</svg>
