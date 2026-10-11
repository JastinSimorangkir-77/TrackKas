<x-app-layout>
    <x-page-header title="Dashboard" subtitle="Ringkasan keuangan Anda" />

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        {{-- Summary + latest transactions --}}
        <div class="space-y-6 xl:col-span-8">
            <div class="grid grid-cols-1 gap-1.5 sm:grid-cols-2">
                {{-- Total Pemasukan --}}
                <div class="flex items-center gap-4 rounded-xl border border-gray-100 border-l-4 border-l-green-500 bg-white px-5 py-6 shadow-sm">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-600">
                        <x-icon name="arrow-up" class="h-5 w-5" stroke-width="1.8" />
                    </span>

                    <span class="min-w-0">
                        <span class="block text-xs font-medium text-gray-500">{{ __('Total Pemasukan') }}</span>
                        <span class="block text-xl font-bold text-green-600"><x-rupiah :value="$income" /></span>
                    </span>
                </div>

                {{-- Total Pengeluaran --}}
                <div class="flex items-center gap-4 rounded-xl border border-gray-100 border-l-4 border-l-brand-600 bg-white px-5 py-6 shadow-sm">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                        <x-icon name="arrow-down" class="h-5 w-5" stroke-width="1.8" />
                    </span>

                    <span class="min-w-0">
                        <span class="block text-xs font-medium text-gray-500">{{ __('Total Pengeluaran') }}</span>
                        <span class="block text-xl font-bold text-brand-600"><x-rupiah :value="$expense" /></span>
                    </span>
                </div>
            </div>

            {{-- Transaksi Terbaru --}}
            <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <x-brand-logo class="h-7 w-7" />

                        <h2 class="text-sm font-bold text-gray-800">{{ __('Transaksi Terbaru') }}</h2>
                    </div>

                    <a href="{{ route('transaksi.index') }}"
                       class="rounded-lg border border-brand-600 px-4 py-1.5 text-xs font-semibold text-brand-600 transition hover:bg-brand-50">
                        {{ __('Lihat Semua') }}
                    </a>
                </div>

                @if ($latest->isEmpty())
                    <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                        <x-icon name="empty-box" class="h-11 w-11 text-gray-300" stroke-width="1.5" />

                        <p class="mt-3 text-sm text-gray-500">{{ __('Belum ada transaksi') }}</p>

                        <a href="{{ route('transaksi.create') }}"
                           class="mt-5 inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                            <x-icon name="plus" class="h-4 w-4" />
                            {{ __('Tambah Transaksi') }}
                        </a>
                    </div>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach ($latest as $transaction)
                            <li class="flex items-center justify-between gap-4 px-5 py-4">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-gray-800">{{ $transaction->category }}</p>

                                    <p class="truncate text-xs text-gray-500">
                                        {{ $transaction->date->format('d M Y') }}@if ($transaction->note) &middot; {{ $transaction->note }}@endif
                                    </p>
                                </div>

                                <span class="shrink-0 text-sm font-semibold {{ $transaction->type === 'pemasukan' ? 'text-green-600' : 'text-brand-600' }}">
                                    {{ $transaction->type === 'pemasukan' ? '+' : '-' }}<x-rupiah :value="$transaction->amount" />
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{--
                Statistik bulanan: satu baris per bulan, angkanya sudah dijumlahkan
                oleh database (lihat App\Support\MonthlyStats).
            --}}
            <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <x-brand-logo class="h-7 w-7" />

                        <h2 class="text-sm font-bold text-gray-800">{{ __('Statistik Bulanan') }}</h2>
                    </div>

                    <span class="shrink-0 text-xs text-gray-500">{{ __('12 bulan terakhir') }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">{{ __('Bulan') }}</th>
                                <th class="px-5 py-3 text-right font-semibold">{{ __('Pemasukan') }}</th>
                                <th class="px-5 py-3 text-right font-semibold">{{ __('Pengeluaran') }}</th>
                                <th class="px-5 py-3 text-right font-semibold">{{ __('Selisih') }}</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @foreach ($monthly as $month)
                                @php
                                    $differenceTone = match ($month->tone) {
                                        'positive' => 'text-green-600',
                                        'negative' => 'text-brand-600',
                                        default => 'text-gray-500',
                                    };
                                @endphp

                                <tr class="transition hover:bg-gray-50 {{ $month->current ? 'bg-gray-50' : '' }}">
                                    <td class="whitespace-nowrap px-5 py-3">
                                        <span class="font-semibold text-gray-800">{{ $month->label }}</span>

                                        @if ($month->current)
                                            <span class="ml-1 rounded-full bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-600">
                                                {{ __('Bulan ini') }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-3 text-right text-gray-600">
                                        <x-rupiah :value="$month->income" />
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-3 text-right text-gray-600">
                                        <x-rupiah :value="$month->expense" />
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-3 text-right font-semibold {{ $differenceTone }}">
                                        {{ $month->sign }}<x-rupiah :value="abs($month->difference)" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Saldo + categories --}}
        <div class="space-y-6 xl:col-span-4">
            {{-- Saldo --}}
            <div class="flex items-center gap-4 rounded-xl bg-gradient-to-br from-brand-600 to-brand-800 px-5 py-6 shadow-sm">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-white/20 text-white">
                    <x-icon name="wallet" class="h-5 w-5" stroke-width="1.8" />
                </span>

                <span class="min-w-0">
                    <span class="block text-xs font-medium text-white/80">{{ __('Saldo') }}</span>
                    <span class="block text-xl font-bold text-white"><x-rupiah :value="$balance" /></span>
                </span>
            </div>

            {{-- Two identical panels, driven by the reusable x-category-breakdown component. --}}
            <x-category-breakdown title="Pemasukan per Kategori" :rows="$incomeByCategory" color="green" />
            <x-category-breakdown title="Pengeluaran per Kategori" :rows="$expenseByCategory" />
        </div>
    </div>

    {{--
        Tentang TrackKas: penjelasan singkat di dalam card, mengikuti gaya card
        lain di halaman ini. Isinya tetap teks biasa, tanpa hiasan tambahan.
    --}}
    <section class="mt-6 rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
            <x-brand-logo class="h-7 w-7" />

            <h2 class="text-sm font-bold text-gray-800">{{ __('Tentang TrackKas') }}</h2>
        </div>

        <div class="space-y-3 px-5 py-4">
            <p class="max-w-3xl text-sm leading-relaxed text-gray-600">
                {{ __('TrackKas adalah pencatat keuangan pribadi. Setiap pemasukan dan pengeluaran disimpan sebagai satu transaksi, lalu dijumlahkan otomatis menjadi saldo, total pemasukan, total pengeluaran, dan rekap per kategori.') }}
            </p>

            <p class="max-w-3xl text-sm leading-relaxed text-gray-600">
                {{ __('Laporan harian, mingguan, bulanan, atau tahunan bisa diunduh sebagai berkas PDF dari halaman Transaksi. Setiap akun hanya melihat transaksinya sendiri, dan bisa menyimpan sampai :batas transaksi supaya halaman tetap cepat dibuka.', ['batas' => number_format($batas, 0, ',', '.')]) }}
            </p>
        </div>
    </section>
</x-app-layout>
