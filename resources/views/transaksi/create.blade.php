<x-app-layout>
    <x-page-header title="Tambah Transaksi" subtitle="Catat pemasukan atau pengeluaran baru" />

    @error('transaksi')
        <div class="mb-6 rounded-lg border border-red-100 bg-red-50 px-5 py-3 text-sm font-medium text-red-700">
            {{ $message }}
        </div>
    @enderror

    @if ($jumlah >= $batas)
        {{-- The form is hidden because the account cannot take another row. --}}
        <div class="max-w-2xl rounded-xl border border-amber-100 bg-amber-50 px-5 py-4 text-sm text-amber-800">
            <p class="font-semibold">{{ __('Batas transaksi sudah tercapai') }}</p>

            <p class="mt-1">
                {{ __('Akun ini sudah menyimpan :batas transaksi, yaitu jumlah maksimal yang bisa dicatat. Hapus beberapa transaksi di halaman Transaksi dulu.', ['batas' => number_format($batas, 0, ',', '.')]) }}
            </p>

            <a href="{{ route('transaksi.index') }}"
               class="mt-4 inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-600 shadow-sm transition hover:bg-gray-50">
                {{ __('Ke Halaman Transaksi') }}
            </a>
        </div>
    @else
        <form method="POST" action="{{ route('transaksi.store') }}" class="max-w-2xl space-y-6">
            @csrf

            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                @include('transaksi.partials.form')
            </div>

            <p class="text-xs text-gray-500">
                {{ __('Tersimpan :jumlah dari maksimal :batas transaksi.', ['jumlah' => number_format($jumlah, 0, ',', '.'), 'batas' => number_format($batas, 0, ',', '.')]) }}
            </p>

            <div class="flex items-center gap-3">
                <x-primary-button>{{ __('Simpan Transaksi') }}</x-primary-button>

                <a href="{{ route('transaksi.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-600 shadow-sm transition hover:bg-gray-50">
                    {{ __('Batal') }}
                </a>
            </div>
        </form>
    @endif
</x-app-layout>
