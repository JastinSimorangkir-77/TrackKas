<div class="space-y-6">
    <!-- Jenis Transaksi -->
    <div>
        <x-input-label value="Jenis Transaksi" />

        <div class="mt-2 grid grid-cols-2 gap-3">
            <label class="cursor-pointer">
                <input type="radio" name="type" value="pemasukan" class="peer sr-only" @checked(old('type', 'pemasukan') === 'pemasukan')>

                <span class="flex items-center justify-center gap-2 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-600 transition peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 peer-focus-visible:ring-2 peer-focus-visible:ring-green-500/30">
                    <x-icon name="arrow-up" class="h-4 w-4" />
                    {{ __('Pemasukan') }}
                </span>
            </label>

            <label class="cursor-pointer">
                <input type="radio" name="type" value="pengeluaran" class="peer sr-only" @checked(old('type') === 'pengeluaran')>

                <span class="flex items-center justify-center gap-2 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-600 transition peer-checked:border-brand-600 peer-checked:bg-brand-50 peer-checked:text-brand-700 peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500/30">
                    <x-icon name="arrow-down" class="h-4 w-4" />
                    {{ __('Pengeluaran') }}
                </span>
            </label>
        </div>

        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <!-- Tanggal -->
        <div>
            <x-input-label for="date" value="Tanggal" />

            <x-text-input id="date" class="block mt-2 w-full"
                            type="date"
                            name="date"
                            :value="old('date', now()->toDateString())" />

            <x-input-error :messages="$errors->get('date')" class="mt-2" />
        </div>

        <!-- Kategori -->
        <div>
            <x-input-label for="category" value="Kategori" />

            <x-text-input id="category" class="block mt-2 w-full"
                            type="text"
                            name="category"
                            maxlength="50"
                            placeholder="{{ __('Contoh: Gaji, Makanan & Minuman') }}"
                            :value="old('category')" />

            <x-input-error :messages="$errors->get('category')" class="mt-2" />
        </div>
    </div>

    <!-- Jumlah -->
    <div>
        <x-input-label for="amount" value="Jumlah (Rp)" />

        <div class="relative mt-2">
            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm font-medium text-gray-400">Rp</span>

            <x-text-input id="amount" class="block w-full pl-11"
                            type="number"
                            name="amount"
                            min="0"
                            max="999999999999.99"
                            step="0.01"
                            placeholder="0"
                            :value="old('amount')" />
        </div>

        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
    </div>

    <!-- Catatan -->
    <div>
        <x-input-label for="note" value="Catatan (opsional)" />

        <textarea id="note" name="note" rows="3" placeholder="{{ __('Tambahkan keterangan...') }}"
                  class="mt-2 block w-full rounded-lg border-gray-200 bg-white py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-sm transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25">{{ old('note') }}</textarea>

        <x-input-error :messages="$errors->get('note')" class="mt-2" />
    </div>
</div>
