<x-app-layout>
    <x-page-header title="Transaksi" subtitle="Riwayat semua transaksi Anda" />

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-green-100 bg-green-50 px-5 py-3 text-sm font-medium text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 px-5 py-4">
            <div class="flex items-center gap-3">
                <x-brand-logo class="h-7 w-7" />

                <h2 class="text-sm font-bold text-gray-800">{{ __('Daftar Transaksi') }}</h2>
            </div>

            <a href="{{ route('transaksi.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                <x-icon name="plus" class="h-4 w-4" />
                {{ __('Tambah Transaksi') }}
            </a>
        </div>

        {{--
            Penyaring jenis dan kotak pencarian. Keduanya membawa pilihan yang
            lain ikut serta, sehingga mengetik kata kunci tidak menghapus
            penyaring jenis yang sedang aktif, dan sebaliknya. Keduanya juga
            tidak membawa nomor halaman, supaya hasil baru selalu mulai dari
            halaman pertama.
        --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
            <div class="flex flex-wrap items-center gap-2">
                @foreach (['' => 'Semua', 'pemasukan' => 'Pemasukan', 'pengeluaran' => 'Pengeluaran'] as $value => $label)
                    <a href="{{ route('transaksi.index', array_filter(['type' => $value, 'q' => $search])) }}"
                       class="rounded-full px-4 py-1.5 text-xs font-semibold transition {{ ($type ?? '') === $value ? 'bg-brand-600 text-white' : 'border border-gray-200 text-gray-600 hover:border-brand-300 hover:text-brand-600' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('transaksi.index') }}" class="flex w-full items-center gap-2 sm:w-auto">
                @if ($type)
                    <input type="hidden" name="type" value="{{ $type }}">
                @endif

                <div class="relative flex-1 sm:flex-none">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <x-icon name="search" class="h-4 w-4" />
                    </span>

                    <x-text-input id="q" name="q" type="search" class="pl-9 sm:w-64"
                                  placeholder="{{ __('Cari kategori atau catatan') }}"
                                  :value="$search" />
                </div>

                <x-secondary-button type="submit" class="px-4">{{ __('Cari') }}</x-secondary-button>

                @if ($search !== '')
                    <a href="{{ route('transaksi.index', $type ? ['type' => $type] : []) }}"
                       class="px-2 text-sm font-semibold text-gray-500 transition hover:text-gray-700">
                        {{ __('Hapus') }}
                    </a>
                @endif
            </form>
        </div>

        @if ($transactions->isEmpty())
            <!-- Empty state: hasil pencarian kosong dibedakan dari akun yang masih kosong -->
            <div class="flex flex-col items-center justify-center border-t border-gray-100 px-6 py-20 text-center">
                <x-icon name="empty-list" class="h-11 w-11 text-gray-300" stroke-width="1.5" />

                @if ($search !== '' || $type)
                    <p class="mt-3 text-sm font-semibold text-gray-700">{{ __('Tidak ada transaksi yang cocok') }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Coba kata kunci lain, atau kosongkan penyaringnya.') }}</p>

                    <a href="{{ route('transaksi.index') }}"
                       class="mt-4 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-600 shadow-sm transition hover:bg-gray-50">
                        {{ __('Lihat semua transaksi') }}
                    </a>
                @else
                    <p class="mt-3 text-sm font-semibold text-gray-700">{{ __('Belum ada transaksi') }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Transaksi yang Anda catat akan muncul di sini.') }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Gunakan tombol Tambah Transaksi di atas untuk memulai.') }}</p>
                @endif
            </div>
        @else
            <div class="overflow-x-auto border-t border-gray-100">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">{{ __('Tanggal') }}</th>
                            <th class="px-5 py-3 font-semibold">{{ __('Kategori') }}</th>
                            <th class="px-5 py-3 font-semibold">{{ __('Jenis') }}</th>
                            <th class="px-5 py-3 text-right font-semibold">{{ __('Jumlah') }}</th>
                            <th class="px-5 py-3 text-right font-semibold">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach ($transactions as $transaction)
                            <tr class="transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-5 py-3 text-gray-600">{{ $transaction->date->format('d M Y') }}</td>

                                <td class="px-5 py-3">
                                    <span class="font-semibold text-gray-800">{{ $transaction->category }}</span>

                                    @if ($transaction->note)
                                        <span class="block text-xs text-gray-500">{{ $transaction->note }}</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $transaction->type === 'pemasukan' ? 'bg-green-50 text-green-600' : 'bg-brand-50 text-brand-600' }}">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-3 text-right font-semibold {{ $transaction->type === 'pemasukan' ? 'text-green-600' : 'text-brand-600' }}">
                                    {{ $transaction->type === 'pemasukan' ? '+' : '-' }}<x-rupiah :value="$transaction->amount" />
                                </td>

                                <td class="whitespace-nowrap px-5 py-3">
                                    <div class="flex items-center justify-end gap-4">
                                        <form method="POST" action="{{ route('transaksi.destroy', $transaction) }}"
                                              onsubmit="return confirm('{{ __('Hapus transaksi ini?') }}')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-500">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Berapa baris yang tampil dan tautan ke halaman berikutnya --}}
            {{ $transactions->links('vendor.pagination.trackkas') }}
        @endif
    </div>

    {{--
        Unduh laporan periode tertentu sebagai PDF. Form ini dulu punya
        halamannya sendiri; sekarang menempel di halaman Transaksi, dan angkanya
        hanya muncul di dalam berkas PDF.
    --}}
    @if ($errors->any())
        <div class="mt-6 rounded-lg border border-red-100 bg-red-50 px-5 py-3 text-sm font-medium text-red-700">
            {{ __('Pilihan periode atau tanggal tidak dikenali. Silakan pilih ulang.') }}
        </div>
    @endif

    <form method="GET" action="{{ route('laporan.pdf') }}"
          class="mt-6 rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-4">
            <x-icon name="report" class="h-5 w-5 text-brand-600" stroke-width="1.8" />

            <h2 class="text-sm font-bold text-gray-800">{{ __('Unduh Laporan PDF') }}</h2>
        </div>

        <div class="flex flex-wrap items-end gap-4 px-5 py-4">
            <div class="w-full sm:w-48">
                <x-input-label for="periode" value="Periode" />

                <select id="periode" name="periode"
                        class="mt-2 block w-full rounded-lg border-gray-200 bg-white py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25">
                    @foreach ($periodeOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('periode', $periode) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-56">
                <x-input-label for="jenis" value="Isi Laporan" />

                <select id="jenis" name="jenis"
                        class="mt-2 block w-full rounded-lg border-gray-200 bg-white py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25">
                    @foreach ($jenisOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('jenis', $jenis) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-48">
                <x-input-label for="tanggal" value="Tanggal Acuan" />

                <x-text-input id="tanggal" name="tanggal" type="date" :value="old('tanggal', $tanggal)" />
            </div>

            <x-primary-button>
                <x-icon name="download" class="h-4 w-4" stroke-width="1.8" />
                {{ __('Unduh PDF') }}
            </x-primary-button>
        </div>

        <p class="px-5 pb-4 text-xs text-gray-500">
            {{ __('Minggu dihitung Senin sampai Minggu. Bulan dan tahun mengikuti tanggal acuan. Pilih "Pemasukan saja" atau "Pengeluaran saja" untuk laporan yang hanya memuat satu jenis transaksi.') }}
        </p>
    </form>
</x-app-layout>
