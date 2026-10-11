{{--
    "Pemasukan per Kategori" / "Pengeluaran per Kategori" panel.

    Both panels on the dashboard were the same markup twice, differing only in
    colour, title and the collection of rows. The progress bar width is worked
    out here from the biggest row, so the view stays free of maths.

    Usage:  <x-category-breakdown title="Pemasukan per Kategori" :rows="$incomeByCategory" color="green" />
--}}
@props(['title', 'rows', 'color' => 'brand'])

@php
    $max = $rows->max('total') ?: 1;

    $badge = $color === 'green' ? 'bg-green-50 text-green-600' : 'bg-brand-50 text-brand-600';
    $bar = $color === 'green' ? 'bg-green-500' : 'bg-brand-600';
@endphp

<div class="rounded-xl border border-gray-100 bg-white shadow-sm">
    <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $badge }}">
            <x-icon :name="$color === 'green' ? 'arrow-up' : 'arrow-down'" class="h-4 w-4" />
        </span>

        <h2 class="text-sm font-bold text-gray-800">{{ $title }}</h2>
    </div>

    @if ($rows->isEmpty())
        <div class="flex items-center justify-center gap-2 px-5 py-8 text-sm text-gray-500">
            <x-icon name="info" class="h-4 w-4 text-gray-400" />
            {{ __('Belum ada data') }}
        </div>
    @else
        <ul class="space-y-4 px-5 py-5">
            @foreach ($rows as $row)
                <li>
                    <div class="flex items-center justify-between gap-4 text-sm">
                        <span class="truncate text-gray-600">{{ $row->category }}</span>
                        <span class="shrink-0 font-semibold text-gray-800"><x-rupiah :value="$row->total" /></span>
                    </div>

                    <div class="mt-2 h-1.5 w-full rounded-full bg-gray-100">
                        <div class="h-1.5 rounded-full {{ $bar }}" style="width: {{ round($row->total / $max * 100) }}%"></div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
