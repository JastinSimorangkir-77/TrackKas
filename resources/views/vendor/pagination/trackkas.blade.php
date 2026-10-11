{{--
    Footer daftar transaksi: berapa baris yang sedang tampil dan tautan halaman.

    Dipanggil lewat ->links('vendor.pagination.trackkas'). Laravel sudah punya
    view halaman bawaan, tetapi warnanya bukan warna aplikasi ini, jadi halaman
    ini ditulis sendiri memakai kelas yang sama seperti sisa aplikasi. Isinya
    mengikuti bentuk view bawaan: $elements berisi nomor halaman dan titik-titik
    pemisah, dan halaman yang sedang dibuka tidak dibuatkan tautan.
--}}
@if ($paginator->total() > 0)
    <nav role="navigation" aria-label="{{ __('Navigasi halaman') }}"
         class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 px-5 py-4">
        <p class="text-xs text-gray-500">
            {{ __('Menampilkan :dari sampai :sampai dari :total transaksi', [
                'dari' => number_format($paginator->firstItem(), 0, ',', '.'),
                'sampai' => number_format($paginator->lastItem(), 0, ',', '.'),
                'total' => number_format($paginator->total(), 0, ',', '.'),
            ]) }}
        </p>

        @if ($paginator->hasPages())
            <div class="flex flex-wrap items-center gap-1">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true"
                          class="rounded-lg border border-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-300">
                        {{ __('Sebelumnya') }}
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                       class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-brand-300 hover:text-brand-600">
                        {{ __('Sebelumnya') }}
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-1 text-xs font-semibold text-gray-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                      class="rounded-lg border border-transparent bg-brand-600 px-3 py-1.5 text-xs font-semibold text-white">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                   class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-brand-300 hover:text-brand-600">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                       class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-brand-300 hover:text-brand-600">
                        {{ __('Berikutnya') }}
                    </a>
                @else
                    <span aria-disabled="true"
                          class="rounded-lg border border-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-300">
                        {{ __('Berikutnya') }}
                    </span>
                @endif
            </div>
        @endif
    </nav>
@endif
