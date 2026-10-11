# Peta Belajar TrackKas — Dari CRUD Dasar ke Kode TrackKas

Dokumen ini pendamping dari [tanya-jawab-sidang.md](tanya-jawab-sidang.md).
Baca dokumen ini dulu supaya kamu **kenal peta kodenya**, baru masuk ke daftar pertanyaan.

---

## 1. Yang sudah kamu bisa (dari komunitas_laravel) itu sudah 70% modal

Di project `komunitas_laravel` kamu sudah melakukan ini:

```php
// UserController
$users = User::all();                        // READ  -> ambil semua
User::create($request->all());               // CREATE -> simpan
$user = user::find($id);                     // cari 1 data
$user->update($request->all());              // UPDATE
User::find($id)->delete();                   // DELETE
```

Dan di view kamu sudah pakai HTML biasa:

```blade
<table border="1" cellpadding="8">
<form method="POST" action="/users/tambah">
    {{ csrf_field() }}
    Nama: <input type="text" name="name"><br>
```

**Itu semua benar dan itu semua tetap dipakai di TrackKas.** Bedanya cuma:

1. TrackKas **tidak menampilkan data orang lain** — selalu punya satu user yang login.
2. TrackKas **memvalidasi** input sebelum disimpan.
3. TrackKas **memisahkan tampilan** ke komponen + layout, bukan satu file HTML panjang.
4. TrackKas **memakai Tailwind** untuk tampilan, bukan atribut HTML (`border="1"`).

Jadi kamu tidak mulai dari nol. Kamu cuma naik satu tangga.

---

## 2. Perbandingan langsung: punyamu vs TrackKas

| Hal | komunitas_laravel (punyamu) | TrackKas |
|---|---|---|
| Ambil data | `User::all()` | `$request->user()->transactions()->latest('date')->get()` |
| Simpan | `User::create($request->all())` | `->create($request->validate([...]))` |
| Hapus | `User::find($id)->delete()` lewat **link GET** | `->findOrFail($id)->delete()` lewat **form method DELETE** |
| Tampilan | HTML biasa, `<table border="1">` | Blade component + Tailwind utility class |
| Layout | tidak ada (tiap file HTML berdiri sendiri) | `x-app-layout` (ada sidebar) & `x-guest-layout` (halaman login) |
| Login | tidak ada | Laravel Breeze + middleware `auth` |
| Validasi | tidak ada | `$request->validate([...])` |
| CSS | atribut HTML + `<style>` | Tailwind (`rounded-xl border bg-white`) |
| JavaScript | tidak ada | Alpine.js (tombol buka/tutup sidebar) |
| Migrasi | 3 file bawaan Laravel | +2 file untuk tabel `transactions` |
| Testing | tidak ada | `php artisan test` (36 test, 104 assertion) |

**Kalimat siap pakai kalau penguji tanya bedanya:**
> "Pola dasarnya sama, Pak/Bu. Saya tetap pakai Model, Controller, dan View. Yang saya tambahkan di TrackKas adalah validasi input, pembatasan data per user yang login, dan pemisahan tampilan ke komponen supaya tidak berulang."

---

## 3. Alur satu request di TrackKas (hafalkan gambar ini)

Contoh: user klik menu **Tambah Transaksi**.

```
1. routes/web.php
   Route::get('/transaksi/create', [TransactionsController::class, 'create'])
        ->nama 'transaksi.create'
        ↓  (lewat middleware 'auth' dulu — kalau belum login, dilempar ke /login)

2. app/Http/Controllers/TransactionsController.php  -> method create()
        return view('transaksi.create');

3. resources/views/transaksi/create.blade.php
        <x-app-layout>                        -> resources/views/layouts/app.blade.php
            @include('transaksi.partials.form') -> isi field-nya
        </x-app-layout>
        (app.blade.php memanggil @vite -> CSS Tailwind + JS Alpine)

4. Browser menampilkan halaman
```

Lalu user klik **Simpan Transaksi**:

```
1. form method="POST" action="/transaksi"  +  @csrf
        ↓
2. Route::post('/transaksi', [... 'store'])->name('transaksi.store')
        ↓
3. store(Request $request)
        $request->validate([...])          <- kalau gagal, balik ke form + pesan error
        $request->user()->transactions()->create($data)  <- INSERT ke MySQL
        redirect()->route('transaksi.index')->with('status', 'Transaksi berhasil ditambahkan.')
        ↓
4. index() dipanggil ulang, view menampilkan flash message hijau
```

Diagram MVC-nya:

```
ROUTE  →  CONTROLLER  →  MODEL  →  DATABASE
                ↓
              VIEW (Blade)  →  HTML  →  Browser  →  Tailwind & Alpine (dari Vite)
```

---

## 4. Isi folder penting (yang perlu kamu tahu saja)

```
app/
  Http/Controllers/
    DashboardController.php        -> hitung total pemasukan/pengeluaran/saldo
    TransactionsController.php     -> CRUD transaksi
    ProfileController.php          -> dari Breeze (edit profil)
    Auth/                          -> dari Breeze (login, register, reset password)
  Models/
    User.php                       -> relasi transactions()
    Transaction.php                -> model transaksi
  View/Components/
    AppLayout.php                  -> jembatan <x-app-layout>

database/migrations/
  0001_01_01_000000_create_users_table.php
  2026_09_27_083131_create_transactions_table.php       -> bikin tabel transactions (semua kolom sekaligus)

resources/
  css/app.css                      -> Tailwind + CSS sidebar
  js/app.js                        -> Alpine (buka/tutup sidebar)
  views/
    layouts/app.blade.php          -> kerangka halaman dalam aplikasi (ada sidebar)
    layouts/sidebar.blade.php      -> isi sidebar merah
    layouts/guest.blade.php        -> kerangka halaman belum login
    components/                    -> komponen kecil yang dipakai berulang
    dashboard.blade.php
    transaksi/index.blade.php      -> daftar transaksi
    transaksi/create.blade.php     -> form tambah
    transaksi/partials/form.blade.php -> field form (dipakai bareng)

routes/
  web.php                          -> semua URL aplikasi
  auth.php                         -> URL login/register dari Breeze

tests/Feature/
  ExampleTest.php                  -> uji redirect halaman depan
  DashboardTest.php                -> uji dashboard + pembatasan tamu
  TransactionTest.php              -> uji CRUD transaksi
```

---

## 5. Glosarium istilah yang sering dipakai penguji

| Istilah | Arti sederhana |
|---|---|
| **Route** | Daftar URL di aplikasi dan siapa yang menanganinya |
| **Middleware** | Petugas pemeriksa yang jalan *sebelum* controller (contoh: cek sudah login atau belum) |
| **Controller** | Tempat logika: ambil data, simpan, arahkan ke halaman |
| **Model / Eloquent** | Perwakilan tabel database di dalam kode PHP |
| **Migration** | File PHP untuk membuat/mengubah struktur tabel (versi kode dari tabel database) |
| **Seeder / Factory** | Pengisi data contoh untuk testing |
| **Blade** | Mesin template Laravel. Semua file yang berakhiran `.blade.php` |
| **Component** | Potongan tampilan yang bisa dipakai berulang, dipanggil dengan `<x-nama>` |
| **Slot** | Isi yang diselipkan ke dalam component (`{{ $slot }}`) |
| **Layout** | Kerangka halaman: head, sidebar, footer. Isi halaman masuk ke `{{ $slot }}` |
| **Tailwind** | Kumpulan class CSS siap pakai (`p-4`, `rounded-lg`, `bg-white`) yang ditulis langsung di HTML |
| **Utility class** | Satu class = satu properti CSS kecil (`mt-2` = `margin-top: 0.5rem`) |
| **Vite** | Alat yang membungkus CSS & JS lalu menghasilkan file di `public/build` |
| **Alpine.js** | Library JavaScript kecil untuk interaksi ringan (buka/tutup sidebar) |
| **CSRF Token** | Kode rahasia di form supaya form tidak bisa dikirim dari situs lain |
| **Mass assignment** | Laravel mengisi kolom database dari array input; dikontrol oleh `$fillable` |
| **Route model binding** | Laravel otomatis mengubah `{id}` di URL menjadi objek model |
| **N+1 query** | Mengambil data baris per baris di dalam loop, bikin lambat |
| **Eager loading** | Mengambil data relasi sekaligus (`with('user')`) supaya tidak N+1 |
| **IDOR** | Celah keamanan saat user bisa mengubah ID di URL dan melihat data orang lain |
| **CRUD** | Create, Read, Update, Delete |
| **Flash message** | Pesan sekali tampil, disimpan di session, hilang setelah dibaca |

---

## 6. Cara membaca satu file controller tanpa panik

Ambil `TransactionsController.php`. Baca dengan urutan ini:

1. **Apa methodnya?** `index` (daftar), `create` (form), `store` (simpan), `destroy` (hapus).
2. **Baris pertama tiap method adalah sumber datanya.** `$request->user()` = user yang sedang login.
3. **`validate()` = daftar aturan input.** Baca sebagai "input apa yang wajib, dan bentuknya harus apa".
4. **Baris terakhir = kemana perginya.** `return view('...')` berarti tampilkan halaman, `return redirect()->route('...')` berarti pindah halaman.

Kalau 4 hal itu sudah dapat, kamu sudah bisa menjelaskan file tersebut.

---

## 7. Rencana belajar 7 hari (1–1,5 jam per hari)

| Hari | Fokus | Target |
|---|---|---|
| 1 | Baca [peta ini](peta-belajar-trackkas.md) + buka tiap file yang disebut di bagian 4 | Bisa tunjuk folder & jelaskan perannya tanpa buka catatan |
| 2 | Baca `routes/web.php` + `TransactionsController.php` + `DashboardController.php` sampai paham | Bisa jelaskan alur "klik Simpan Transaksi" dari form sampai database |
| 3 | Tailwind dasar (lihat bagian 8 di bawah) + baca `transaksi/create.blade.php` | Bisa tebak hasil dari `rounded-lg px-4 py-2 text-sm font-semibold` |
| 4 | Blade: layout, component, `@include`, `@foreach`, `@if` | Bisa jelaskan perbedaan `<x-rUpiah>` dengan `@include` |
| 5 | Model & migrasi: `#[Fillable]`, `casts()`, `hasMany`, `belongsTo` | Bisa jelaskan kenapa relasi perlu ada |
| 6 | Keamanan: validasi, IDOR, CSRF, `$request->all()` | Bisa jelaskan kenapa `create($request->all())` berbahaya |
| 7 | Latihan demo hidup + hafalkan kalimat "saya belum diajari ini" di bagian 12 | Demo lancar tanpa lihat catatan |

Tiap hari, latih **satu** bagian dengan cara: baca kode, tutup file, lalu ucapkan penjelasannya sendiri. Sidang menilai kemampuan bicara, bukan kemampuan buka file.

---

## 8. Tailwind minimum untuk bisa membaca TrackKas

Tailwind itu bukan bahasa baru. Class-nya cuma **singkatan CSS**. Baca dari kiri ke kanan:

```html
<div class="rounded-xl border border-gray-100 bg-white px-5 py-4 shadow-sm">
```

| Class | Arti CSS-nya |
|---|---|
| `rounded-xl` | `border-radius: 0.75rem;` |
| `border` | `border-width: 1px;` |
| `border-gray-100` | `border-color: #f3f4f6;` |
| `bg-white` | `background-color: #fff;` |
| `px-5` | `padding-left/right: 1.25rem;` |
| `py-4` | `padding-top/bottom: 1rem;` |
| `shadow-sm` | `box-shadow: 0 1px 2px rgba(0,0,0,.05);` |

Pola yang mesti kamu kenali:

- **p** = padding, **m** = margin. `px` (kiri-kanan), `py` (atas-bawah), `pt`/`pb`/`pl`/`pr`.
- **Angka 1–12** kelipatan 0.25rem. `mt-2` = 0.5rem ≈ 8px.
- **`sm:` `md:` `lg:` `xl:`** = hanya berlaku mulai ukuran layar itu ke atas (responsive).
- **`hover:`** = hanya saat mouse di atas elemen.
- **`flex`, `grid`, `gap-4`, `items-center`, `justify-between`** = mengatur susunan.
- **`text-sm`, `font-semibold`, `text-gray-600`** = ukuran, ketebalan, warna huruf.
- **`w-full`, `h-10`, `max-w-2xl`** = lebar/tinggi.
- **`space-y-4`** = jarak antar anak elemen ke bawah.

**Kalimat siap pakai kalau ditanya "kamu belajar Tailwind dari mana?":**
> "Saya belum diajari Tailwind secara formal, jadi saya belajar sambil jalan dari dokumentasi resminya. Saya paham polanya: setiap class itu satu properti CSS, dan prefix seperti `lg:` atau `hover:` membuat style itu aktif hanya pada kondisi tertentu. Tampilan TrackKas saya susun dari class-class itu, bukan dari file CSS terpisah."

Cara belajar cepat: pasang extension **Tailwind CSS IntelliSense** di VS Code. Tiap kali kamu hover ke nama class, extension itu menunjukkan CSS aslinya. Dua jam pakai itu, kamu sudah lancar membaca.

---

## 9. Catatan jujur tentang kondisi project (jangan sampai kaget kalau ditanya)

| Kondisi | Jawaban jujur & aman |
|---|---|
| `APP_NAME` di `.env` masih `Laravel`, jadi judul tab browser masih "Laravel" | "Brand di dalam halaman sudah TrackKas, tapi nama aplikasi di `.env` belum saya ubah. Perbaikannya satu baris: `APP_NAME=TrackKas` lalu `php artisan config:clear`." |
| Ada 2 migrasi untuk 1 tabel | "Yang pertama saya generate dari awal project, jadi tabelnya kosong. Kolom aslinya saya tambah di migrasi kedua." |
| Token `@tailwindcss/vite` masih ada di `package.json` padahal pakai Tailwind v3 | "Itu sisa dari template. Project ini nyatanya jalan pakai Tailwind v3 + PostCSS, dibuktikan oleh `postcss.config.js` dan `@tailwind base` di `app.css`." |
| Belum ada fitur **Edit/Update** transaksi | "Yang saya kerjakan baru Create, Read, dan Delete. Update sudah saya rencanakan, kurang lebih 15 baris: satu method `edit` untuk menampilkan form berisi data lama, dan satu method `update` untuk menyimpannya." |
| `resources/views/welcome.blade.php` dan `layouts/navigation.blade.php` tidak terpakai | "Itu file bawaan. Halaman depan sekarang diarahkan ke login atau dashboard, jadi file itu tidak lagi diakses." |
| Halaman verify-email & confirm-password masih tampilan bawaan Breeze | "Dua halaman itu belum saya sesuaikan karena tidak masuk alur utama aplikasi. Alur yang saya desain: login → dashboard → transaksi." |
