# Cara Bikin TrackKas dari NOL — Versi Bahasa Manusia

> Dokumen ini buat kamu yang **belum pernah bikin website pakai Laravel**.
> Semua dijelaskan pakai bahasa sehari-hari, dari **install alat** sampai **website jalan**.
> Setiap perintah dijelaskan **artinya apa** dan **kenapa perlu** — bukan cuma "copy-paste terus jalan".
>
> Untuk hafalan cepat saat sidang, pakai [`sidang-trackkas.md`](sidang-trackkas.md).

---

## Daftar Isi

| Bab | Isi |
|---|---|
| 0 | Kenalan dulu: istilah yang wajib ngerti |
| 1 | Konsep inti: 1 kali klik itu ke mana |
| 2 | Alat & bahan (install dari nol) |
| 3 | Bikin project Laravel |
| 4 | Tambahan kecil: Blade & Tailwind |
| 5 | Bikin aplikasinya, langkah 1–12 |
| 6 | Kamus syntax (PHP, Blade, Artisan, Tailwind) |
| 7 | Peta file: mau ganti X, buka Y |
| 8 | Kalau muncul error |
| 9 | Ringkasan 10 perintah |

---

# BAB 0 — Kenalan Dulu: Istilah Wajib

Sebelum nulis kode, kenali 8 kata ini. Kalau sudah paham ini, sisanya jauh lebih gampang.

| Istilah | Arti sederhana | Contoh di TrackKas |
|---|---|---|
| **Server** | komputer yang "menyajikan" halaman web | di laptopmu, pakai Laragon |
| **Framework** | kerangka kerja siap pakai, biar tidak nulis semuanya dari nol | **Laravel** |
| **Backend** | bagian yang diproses di server, tidak kelihatan user | PHP, controller, database |
| **Frontend** | bagian yang dilihat & disentuh user di layar | HTML, CSS, tombol |
| **Database** | tempat menyimpan data permanen (tabel-tabel) | MySQL, tabel `transactions` |
| **Terminal** | jendela hitam tempat mengetik perintah | Command Prompt / terminal Laragon |
| **Library / Paket** | kode buatan orang lain yang kita pinjam | Breeze, Tailwind, Alpine |
| **Error** | pesan bahwa ada yang salah | "Class not found", dsb. |

### Analogi rumah makan (ini yang paling gampang dihafal)

Bayangkan TrackKas itu **restoran**:

| Bagian restoran | Di Laravel | Tugasnya |
|---|---|---|
| Buku menu | **Route** | daftar "alamat" yang boleh dikunjungi |
| Pelayan | **Controller** | menerima pesananmu lalu mengurus |
| Tukang masak | **Model** | menyiapkan/mengambil data |
| Gudang bahan | **Database** | tempat data disimpan |
| Ruang makan | **View** | yang kamu lihat di layar |

Alurnya: **kamu pesan → pelayan catat → masak → hidangan datang ke mejamu.**

---

# BAB 1 — Konsep Inti: 1 Kali Klik Itu Ke Mana?

Ini alur yang terjadi setiap kali kamu klik tombol di TrackKas:

```
    Kamu klik / ketik alamat
             |
             v
    routes/web.php        <-- "alamat ini ditangani siapa?"
             |
             v
    Controller            <-- "cek data, panggil database, siapkan jawaban"
       |         ^
       v         |
     Model    Database    <-- "ambil / simpan datanya"
       |
       v
    View (Blade)          <-- "susun tampilannya jadi HTML"
       |
       v
    Muncul di browser
```

**Bagian yang wajib diingat:** Route → Controller → Model → View.

Sebelum masuk ke halaman mana pun, ada **satpam** bernama **middleware**. Satpam ini mengecek "kamu sudah login belum?" Kalau belum, kamu dibelokkan ke halaman login. Di kode, satpam ini yang namanya `auth`.

---

# BAB 2 — Alat & Bahan (Install dari Nol)

Kamu perlu 5 hal. Semua gratis.

## 2.1 Laragon — biang keladi yang bikin semuanya nyala

**Apa itu?** Aplikasi yang membungkus **PHP + MySQL + Apache/Nginx** jadi satu, tinggal klik "Start". Kalau tidak pakai Laragon, kamu harus install PHP dan MySQL manual (ribet).

**Kenapa perlu?** PHP itu bahasa yang kita pakai; MySQL itu tempat data. Laragon cuma "pintu masuknya".

**Cara install (Windows):**

1. Buka `laragon.org` → download **Laragon Full**.
2. Jalankan installer → Next sampai selesai.
3. Buka Laragon. Klik tombol **Start All**.
4. Kalau dua lampu kecil sudah **hijau**, berarti PHP + MySQL sudah nyala. ✅ **Checkpoint**: ada tulisan "All services are running".

> **Kenapa harus nyala?** Kalau MySQL mati, website error "Connection refused" karena tidak bisa menghubungi database.

**Web root Laragon** = folder tempat project disimpan, biasanya `C:\laragon\www`. Project TrackKas ada di `C:\laragon\www\trackkasdev`.

## 2.2 Composer — "toko online"-nya library PHP

**Apa itu?** Alat untuk mengunduh library PHP (termasuk Laravel sendiri).

**Cara cek:** buka terminal Laragon (di Laragon klik kanan → **Terminal Here**), lalu ketik:

```bash
composer -v
```

Kalau muncul tulisan versi Composer, berarti sudah ada.

> ⚠️ **Penting:** Composer biasanya **hanya bisa dipakai dari terminal Laragon**, bukan dari Git Bash biasa. Jadi kalau `composer` bilang "command not found", buka Laragon → Terminal Here, atau ikuti cara di Bab 8.

## 2.3 Node.js + npm — buat bagian tampilan (CSS & JS)

**Apa itu?** Pasangan alat untuk mengolah file CSS (Tailwind) dan JavaScript (Alpine). **npm** = "toko online"-nya library JavaScript.

**Cara cek:**

```bash
node -v
npm -v
```

Di komputer ini hasilnya `v26.4.0` dan `11.17.0`. Kalau belum ada, download di `nodejs.org` (pilih versi **LTS**).

## 2.4 VS Code — tempat menulis kode

**Apa itu?** Aplikasi editor kode. Alternatifnya Notepad, tapi VS Code jauh lebih enak karena ada penanda warna dan bantuan.

**Ekstensi yang sangat membantu** (buka menu Extensions, cari lalu Install):

| Ekstensi | Fungsinya |
|---|---|
| Laravel Blade Snippets | mewarnai file `.blade.php` biar enak dibaca |
| Laravel Extra Intellisense | bantuan saat mengetik nama view/route |
| Tailwind CSS IntelliSense | memunculkan pilihan kelas Tailwind |

## 2.5 Terminal — tempat mengetik perintah

**Apa itu?** Jendela hitam tempat kamu mengetik perintah (bukan klik-klik). Semua perintah di dokumen ini diketik di sini.

**Cara buka:** di Laragon, klik kanan → **Terminal Here**. Pastikan alamatnya sudah di folder project:

```bash
cd C:\laragon\www\trackkasdev
```

**6 perintah terminal yang wajib tahu:**

| Perintah | Artinya |
|---|---|
| `cd nama-folder` | pindah masuk ke folder (CD = change directory) |
| `cd ..` | naik satu folder di atasnya |
| `ls` (atau `dir`) | lihat isi folder sekarang |
| `pwd` | lihat "aku sekarang di folder mana" |
| `Ctrl + C` | hentikan perintah yang sedang jalan |
| tombol `Tab` | melengkapi nama file otomatis (hemat ketikan) |

> **Tips awam:** kalau lupa posisi, ketik `pwd`. Kalau terminal "nyangkut" dan tidak bisa dipakai, tekan `Ctrl + C`.

## 2.6 Cek semua alat sudah siap

Jalankan berturut-turut di terminal Laragon:

```bash
php -v
composer -v
node -v
npm -v
```

**Checkpoint:** keempatnya keluar tulisan versi. Kalau ada satu yang "not found", jangan lanjut dulu — selesaikan yang itu.

---

# BAB 3 — Bikin Project Laravel

## 3.1 Perintah ajaib pertama

Masuk dulu ke folder web (di Laragon ini `C:\laragon\www`), lalu:

```bash
cd C:\laragon\www
composer create-project laravel/laravel trackkasdev
```

**Baca perintahnya kata per kata:**

| Bagian | Artinya |
|---|---|
| `composer` | alat pengunduh library |
| `create-project` | perintah: "buat project baru" |
| `laravel/laravel` | nama paket kerangka Laravel di toko Composer |
| `trackkasdev` | nama folder project yang akan dibuat |

Proses ini mengunduh ratusan file. **Butuh waktu, sabar ya.** Kalau selesai, akan ada folder `C:\laragon\www\trackkasdev`.

**Masuk ke foldernya:**

```bash
cd trackkasdev
```

> **Ada jalan pintas?** Di Laravel versi baru, seluruh proses Bab 3.4 bisa diganti satu perintah: `composer run setup`. Tapi lebih baik kamu tahu langkah manualnya dulu.

## 3.2 Buka di VS Code

```bash
code .
```

Tanda titik (`.`) artinya "folder yang sekarang". Kalau `code` tidak dikenali, buka VS Code lalu **File → Open Folder → pilih `trackkasdev`**.

## 3.3 Nyalakan website

```bash
php artisan serve
```

**Arti tiap kata:** `php` (jalankan PHP) + `artisan` (alat perintah Laravel, namanya diambil dari "artisan"=pengrajin) + `serve` (perintah: nyalakan server untuk uji coba).

Akan muncul `http://127.0.0.1:8000`. Buka di browser.

✅ **Checkpoint:** muncul halaman sambutan Laravel. **Berhasil!** Website kamu sudah hidup.

> ❗ **Terminal ini jangan ditutup.** Selama website dipakai, terminal ini harus terus jalan. Kalau mau mengetik perintah lain, **buka terminal baru**.

## 3.4 Menyalakan "tukang rias" (Vite)

Buka **terminal kedua** (jendela baru, arahkan ke folder project), lalu:

```bash
npm install
npm run dev
```

| Perintah | Artinya |
|---|---|
| `npm install` | unduh semua library tampilan (Tailwind, Alpine, dll). Cukup sekali. |
| `npm run dev` | jalankan "pengawas": setiap kali kamu ubah CSS/JS, hasilnya langsung diperbarui otomatis |

**Kenapa perlu 2 terminal?** Karena `php artisan serve` dan `npm run dev` sama-sama harus terus jalan, dan satu terminal cuma bisa menjalankan satu perintah.

> **Ada versi praktisnya?** Kalau malas 2 terminal, jalankan satu perintah ini saja: `composer run dev`. Perintah itu otomatis menyalakan server, Vite, pemantau antrian, dan penampil log sekaligus.

## 3.5 Struktur folder Laravel — kenalan dulu

Ini penting: **kamu tidak akan menyentuh semua folder**, cuma beberapa.

| Folder/File | Isinya | Perlu diutak-atik? |
|---|---|---|
| `app/` | kode inti PHP (Model, Controller) | ✅ **sering** |
| `routes/` | daftar alamat/URL | ✅ **sering** |
| `resources/views/` | tampilan (file `.blade.php`) | ✅ **sering** |
| `resources/css/` `resources/js/` | CSS & JavaScript sumber | ✅ kadang |
| `database/migrations/` | "cetakan" tabel database | ✅ kalau nambah tabel |
| `database/factories/` `seeders/` | pembuat data palsu untuk tes | kadang |
| `tests/` | file pengujian otomatis | kadang |
| `config/` | pengaturan bawaan Laravel | jarang |
| `public/` | yang boleh diakses browser; hasil kompilasi ada di `public/build` | jangan |
| `storage/` | hasil sementara (log, cache) | jangan |
| `vendor/` | library PHP dari Composer | ❌ **jangan** |
| `node_modules/` | library tampilan dari npm | ❌ **jangan** |
| `.env` | pengaturan rahasia (database, nama app) | ✅ kadang |
| `artisan` | alat perintah Laravel | jangan |
| `composer.json` | daftar library PHP | jarang |
| `package.json` | daftar library JavaScript | jarang |
| `vite.config.js` | pengaturan "tukang rias" Vite | jarang |
| `tailwind.config.js` | pengaturan warna & font | ✅ kalau ganti warna |
| `postcss.config.js` | pengaturan pengolah CSS | jarang |

> **Aturan awam:** kalau bukan folder `app`, `routes`, `resources/views`, atau `database/migrations`, kemungkinan besar kamu tidak perlu mengutak-atiknya.

---

# BAB 4 — Tambahan Kecil: Blade & Tailwind

Kalau kamu sudah bisa HTML + CSS, dua ini cuma "bumbu tambahan".

## 4.1 Tailwind — CSS yang ditulis di dalam class

**Cara lama (CSS biasa):**

```html
<style>
  .kotak { background: white; border-radius: 12px; padding: 20px; }
</style>
<div class="kotak">Halo</div>
```

**Cara Tailwind:** tidak bikin nama class sendiri, tapi menyusun kelas siap pakai:

```html
<div class="bg-white rounded-xl p-5">Halo</div>
```

| Kelas Tailwind | Artinya |
|---|---|
| `bg-white` | background putih (bg = background) |
| `text-gray-500` | warna tulisan abu-abu sedang |
| `rounded-xl` | sudut membulat |
| `p-5` / `px-5` / `py-2` | jarak dalam (p=padding, x=kiri-kanan, y=atas-bawah) |
| `mt-3` / `mb-6` | jarak luar (m=margin, t=top, b=bottom) |
| `flex` | menyusun anak-anaknya berjajar |
| `gap-3` | jarak antar anak saat pakai `flex` |
| `hidden` | disembunyikan |
| `hover:bg-brand-700` | saat kena kursor, background berubah |
| `lg:flex` | aturan ini hanya berlaku pada layar besar (lg = large) |

> **Angka di belakang itu ukuran** (semakin besar semakin besar), dihitung dari skala tetap. `p-5` = 20px, `mt-6` = 24px.

## 4.2 Blade — HTML yang bisa "ngoding"

Blade itu file `.blade.php`: HTML biasa, tapi boleh disisipi logika. Contoh:

```blade
@foreach ($transactions as $transaction)
    <tr>
        <td>{{ $transaction->category }}</td>
    </tr>
@endforeach
```

Artinya: "ulangi baris ini untuk setiap transaksi yang ada, dan tampilkan kategorinya."

Syntax Blade lengkap ada di **Bab 6**.

---

# BAB 5 — Bikin Aplikasinya, Langkah 1–12

Anggap Bab 1–4 sudah beres, dan kita punya project Laravel kosong yang sudah bisa jalan. Sekarang kita isi jadi TrackKas.

---

## Langkah 1 — Atur kartu identitas: file `.env`

**Apa itu `.env`?** File pengaturan **rahasia** (isi password database, nama aplikasi). Namanya singkatan dari *environment*. **Jangan pernah di-upload ke GitHub.**

Buka `.env` di VS Code, cari dan ubah jadi seperti ini:

```env
APP_NAME=TrackKas
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_trackkas
DB_USERNAME=root
DB_PASSWORD=
```

| Baris | Artinya |
|---|---|
| `APP_NAME` | nama aplikasi; dipakai di judul tab browser |
| `APP_URL` | alamat aplikasimu |
| `DB_CONNECTION=mysql` | "pakai database jenis MySQL" |
| `DB_HOST` `DB_PORT` | alamat & pintu masuk database |
| `DB_DATABASE` | nama database yang dipakai |
| `DB_USERNAME` `DB_PASSWORD` | akun login database |

> **Penting:** database `db_trackkas` **harus dibuat dulu**. Caranya di Laragon klik **Database** → buka phpMyAdmin/HeidiSQL → **Create database** → beri nama `db_trackkas`. MySQL di Laragon biasanya tanpa password.

**Kalau sudah ubah `.env`, wajib jalankan:**

```bash
php artisan config:clear
```

*Kenapa?* Karena Laravel menyimpan salinan pengaturan. Perintah ini menghapus salinan lama supaya `.env` barumu dibaca.

> **Ada juga `php artisan key:generate`** — fungsinya membuat "kunci rahasia" aplikasi. Biasanya sudah otomatis saat bikin project. Kalau ada peringatan soal APP_KEY, jalankan perintah itu.

✅ **Checkpoint Langkah 1:** di `tailwind.config.js` nanti kita pakai warna `brand`. Pastikan file `.env` sudah tidak ada `DB_CONNECTION=sqlite` kalau kamu mau pakai MySQL.

---

## Langkah 2 — Pasang halaman login (Breeze)

**Masalahnya:** bikin sistem login dari nol itu susah (mengenkripsi password, mengurus sesi, lupa password). Untungnya ada paket siap pakai.

**Breeze** = paket resmi Laravel untuk halaman login/register/reset password.

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
```

| Perintah | Artinya |
|---|---|
| `composer require laravel/breeze --dev` | unduh paket Breeze (khusus untuk masa pengembangan) |
| `php artisan breeze:install blade` | pasang Breeze dengan tampilan **Blade** (bukan React/Vue) |
| `php artisan migrate` | buat tabel-tabel bawaan Laravel (users, cache, sessions, jobs) |

**Setelah Breeze dipasang, apa yang muncul?**

- Halaman login, register, lupa password (di `resources/views/auth/`).
- File alamat login di `routes/auth.php`.
- **Tabel `users`** — penting, karena setiap transaksi milik satu user.

> **Bedanya Blade, React, Vue?** Breeze menawarkan beberapa pilihan tampilan. Kita pilih `blade` karena paling sederhana (HTML biasa) dan sudah cukup. Ini yang kamu pelajari.

✅ **Checkpoint Langkah 2:** buka `http://127.0.0.1:8000`, kamu sekarang diarahkan ke halaman **Login**. Coba daftar satu akun, lalu login. Berhasil? Lanjut.

> **Kalau login gagal / "SQLSTATE"**: biasanya tabel belum dibuat (lupa `migrate`), atau `DB_DATABASE` salah. Ingat: Laravel menyimpan sesi login di database, jadi tabel `sessions` wajib ada.

---

## Langkah 3 — Bikin tabel `transactions` (Migration)

**Apa itu migration?** "Cetakan resep" untuk membuat tabel di database. Kelebihannya: orang lain bisa menjalankan ulang cetakan yang sama di komputernya, dan hasilnya identik.

Bukaan terminal, jalankan:

```bash
php artisan make:migration create_transactions_table
```

Perintah `make:` artinya **membuat file baru**. Tanda `:` memisahkan "jenis" dan "nama". Jadi ini membuat file baru di `database/migrations/` dengan nama berisi tanggal + `create_transactions_table`.

Buka file itu, isi seperti ini:

```php
public function up(): void
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('type');
        $table->string('category');
        $table->decimal('amount', 15, 2);
        $table->date('date');
        $table->string('note')->nullable();
        $table->timestamps();
    });
}
```

**Baca kodenya baris per baris:**

| Kode | Artinya |
|---|---|
| `public function up()` | resep yang dijalankan saat membuat tabel |
| `Schema::create('transactions', ...)` | buat tabel bernama `transactions` |
| `function (Blueprint $table)` | cara menulis struktur tabel |
| `$table->id()` | kolom `id`, nomor otomatis 1,2,3,… (kunci utama) |
| `$table->foreignId('user_id')` | kolom `user_id` untuk menandai pemilik |
| `->constrained()` | hubungkan ke tabel `users` |
| `->cascadeOnDelete()` | kalau user dihapus, transaksinya ikut terhapus |
| `$table->string('type')` | kolom teks pendek: `pemasukan` / `pengeluaran` |
| `$table->decimal('amount', 15, 2)` | angka uang, total 15 digit, 2 di belakang koma |
| `$table->date('date')` | kolom tanggal |
| `$table->string('note')->nullable()` | kolom teks pendek; `nullable` = boleh kosong (boleh tidak diisi) |
| `$table->timestamps()` | otomatis membuat `created_at` & `updated_at` |

> **Kenapa uang pakai `decimal`, bukan `float`?** Float menyimpan angka desimal secara "kira-kira", bisa muncul `0.30000000000000004`. Untuk uang wajib `decimal`.

**Jalankan cetakannya:**

```bash
php artisan migrate
```

| Perintah | Kapan dipakai |
|---|---|
| `php artisan migrate` | jalankan cetakan yang belum dijalankan |
| `php artisan migrate:rollback` | batalkan migrasi terakhir |
| `php artisan migrate:fresh` | **hapus semua tabel lalu buat ulang** — semua data hilang! |

> **Kenapa cuma satu file?** Kadang orang membuat migration dua kali: file pertama cuma `id` + timestamps, lalu file kedua menambah kolomnya. Hasilnya sama, tapi jadi 2 file. Di project ini sudah **dirapikan jadi satu file** supaya rapi dan gampang dijelaskan.
>
> Kalau kamu sendiri pernah membuatnya dua kali lalu baru menggabungkan, **wajib** jalankan `php artisan migrate:fresh` — bukan `migrate`. Sebab `migrate` biasa hanya menjalankan file yang belum pernah tercatat, jadi perubahan di file yang sudah tercatat tidak akan terpakai. Ingat: `migrate:fresh` menghapus semua data.

✅ **Checkpoint Langkah 3:** buka phpMyAdmin/HeidiSQL, tabel `transactions` sudah ada dengan kolom-kolom di atas.

---

## Langkah 4 — Model: "petugas gudang data"

**Apa itu Model?** Satu file PHP yang mewakili satu tabel. Lewat Model, kita ambil dan simpan data tanpa menulis perintah SQL manual.

```bash
php artisan make:model Transaction
```

Buka `app/Models/Transaction.php`, isi jadi:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['type', 'category', 'amount', 'date', 'note'])]
class Transaction extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date'   => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

**Baca per baris:**

| Kode | Artinya |
|---|---|
| `namespace App\Models;` | "alamat" file ini, biar tidak bentrok dengan file lain |
| `use Illuminate\...\Model;` | "saya mau pakai alat ini, ambilkan dari sana" |
| `class Transaction extends Model` | buat kelas `Transaction` yang mewarisi semua kemampuan `Model` |
| `#[Fillable([...])]` | daftar kolom yang **boleh diisi** dari form (keamanan) |
| `protected function casts()` | aturan otomatis mengubah tipe data |
| `'amount' => 'decimal:2'` | dibaca sebagai angka desimal |
| `'date' => 'date'` | dibaca sebagai tanggal (jadi bisa `->format('d M Y')`) |
| `public function user()` | relasi **1 transaksi milik 1 user** |
| `$this->belongsTo(User::class)` | "kolom `user_id` ini menunjuk ke tabel `users`" |

Di `app/Models/User.php` ada pasangannya:

```php
public function transactions(): HasMany
{
    return $this->hasMany(Transaction::class);   // 1 user punya banyak transaksi
}
```

**Fungsi relasi yang paling penting:** dengan `$user->transactions()`, kita otomatis **hanya mengambil milik user itu**. Inilah yang membuat user A tidak bisa melihat transaksi user B. Ini bukan cuma soal tampilan — datanya memang difilter.

> **Kenapa `#[Fillable]` penting?** Kalau semua kolom bebas diisi dari form, orang jahat bisa mengirim kolom tersembunyi (mis. `user_id` milik orang lain). `Fillable` menutup celah itu.

✅ **Checkpoint Langkah 4:** belum ada tampilan berubah. Ini normal — kita baru bikin "mesin", belum "etalse".

---

## Langkah 5 — Route: daftar alamat

**Buka `routes/web.php`.** Ubah jadi (baris `use App\Http\Controllers\...;` di paling atas file dibiarkan — itu gunanya "mengambil" kelas controller supaya bisa dipakai di bawahnya):

```php
Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/transaksi',        [TransactionsController::class, 'index'])  ->name('transaksi.index');
    Route::get('/transaksi/create', [TransactionsController::class, 'create']) ->name('transaksi.create');
    Route::post('/transaksi',       [TransactionsController::class, 'store'])  ->name('transaksi.store');
    Route::delete('/transaksi/{transaction}', [TransactionsController::class, 'destroy'])->name('transaksi.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])  ->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
```

**Baca per bagian:**

| Kode | Artinya |
|---|---|
| `Route::get(...)` | buat alamat yang dibuka dengan cara "melihat" |
| `Route::post(...)` | alamat untuk **mengirim** data (form) |
| `Route::delete(...)` | alamat untuk **menghapus** |
| `/transaksi/{transaction}` | ada isian dari URL; `/transaksi/7` berarti id 7 |
| `[TransactionsController::class, 'index']` | "yang melayani: TransactionsController, method `index`" |
| `->name('transaksi.index')` | beri nama, supaya di view cukup tulis `route('transaksi.index')` |
| `Route::middleware(['auth','verified'])` | wajib login + email terverifikasi |
| `->group(function () { ... })` | bungkus beberapa route biar tidak menulis satpam berulang |
| `auth()->check()` | tanya: "user sudah login?" (jawaban benar/salah) |
| `? :` (ternary) | singkatan "kalau begini → A, kalau tidak → B" |
| `redirect()->route('dashboard')` | belokkan user ke halaman lain |
| `Route::middleware('auth')->group(...)` | grup kedua, khusus halaman profil (tanpa syarat email terverifikasi) |
| `Route::patch(...)` | mengirim perubahan sebagian data (dipakai form profil) |
| `require __DIR__.'/auth.php'` | selipkan semua route login/register dari Breeze |

> **Kenapa HTML cuma kenal GET dan POST, tapi Laravel bisa DELETE?** Nanti di form kita pakai `@method('DELETE')` — HTML tetap mengirim POST, tapi dikasih "label" bahwa itu sebenarnya DELETE. Laravel membaca labelnya.

**Lihat semua daftar alamat:**

```bash
php artisan route:list
```

Ini perintah yang bagus banget untuk membuktikan alamat yang kita bikin sudah benar. Cocok ditunjukkan saat sidang.

---

## Langkah 6 — Controller: pelayan yang mengurus pesanan

```bash
php artisan make:controller TransactionsController
php artisan make:controller DashboardController --invokable
```

`--invokable` artinya controller dengan **satu** method saja (namanya `__invoke`).

### 6a. `TransactionsController.php`

```php
public function index(Request $request)
{
    $transactions = $request->user()->transactions()
        ->when($request->type, fn ($query, $type) => $query->where('type', $type))
        ->latest('date')
        ->latest('id')
        ->get();

    return view('transaksi.index', [
        'transactions' => $transactions,
        'type'         => $request->type,
    ]);
}
```

| Kode | Artinya |
|---|---|
| `Request $request` | bungkusan berisi semua yang dikirim browser + info user |
| `$request->user()` | user yang sedang login |
| `->transactions()` | ambil lewat relasi → otomatis milik dia sendiri |
| `->when($nilai, fn ...)` | "kalau `$nilai` ada, pakai filter berikut; kalau kosong, lewati" |
| `fn ($query, $type) => ...` | cara singkat menulis function kecil |
| `->where('type', $type)` | filter hanya baris yang kolom `type`-nya sama |
| `->latest('date')` | urutkan dari tanggal terbaru |
| `->latest('id')` | kalau tanggalnya sama, yang terakhir diinput naik ke atas |
| `->get()` | jalankan dan ambil semua hasilnya |
| `return view('transaksi.index', [...])` | tampilkan file view sambil mengirim data |

```php
public function store(Request $request)
{
    $request->user()->transactions()->create($request->validate([
        'type'     => ['required', 'in:pemasukan,pengeluaran'],
        'date'     => ['required', 'date'],
        'category' => ['required', 'string', 'max:50'],
        'amount'   => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
        'note'     => ['nullable', 'string', 'max:255'],
    ]));

    return redirect()->route('transaksi.index')->with('status', 'Transaksi berhasil ditambahkan.');
}
```

**Aturan validasi, arti satu per satu:**

| Aturan | Artinya |
|---|---|
| `required` | wajib diisi, tidak boleh kosong |
| `in:pemasukan,pengeluaran` | isinya harus salah satu dari dua kata itu |
| `date` | harus format tanggal yang sah |
| `string` | harus berupa teks |
| `max:50` | maksimal 50 huruf — batas panjang nama kategori |
| `numeric` | harus angka |
| `min:0` | tidak boleh negatif |
| `max:999999999999.99` | nominal terbesar yang boleh masuk |
| `nullable` | boleh dikosongkan |

> **Kenapa ada batas `max` di kategori dan jumlah?**
> Karena tiap kolom punya "jatah muat" di database. `amount` disimpan sebagai `decimal(15,2)` — 15 angka total, 2 di antaranya di belakang koma — jadi angka terbesar yang muat adalah **9.999.999.999.999,99**. Kalau user mengetik lebih dari itu, MySQL menolak dengan error *Out of range* yang membingungkan, bukan pesan validasi yang ramah. Kolom `category` sendiri bertipe teks (muat 255 huruf), tapi nama kategori sepanjang itu tidak ada gunanya, jadi kita batasi 50 huruf sebagai keputusan aplikasi.
>
> Jadi batasnya dipasang **dua kali**: di validasi (`max:50`, `max:999999999999.99`) sebagai pengaman utama, dan di HTML (`maxlength="50"`, `max="999999999999.99"`) supaya user tidak keburu mengetik lalu baru ditolak. Nilai `max:999999999999.99` sengaja dipasang **sedikit di bawah** kapasitas asli kolomnya — lebih baik user ditolak di pintu dengan pesan jelas daripada lolos ke database dan kena *Out of range*. Batas DB adalah "tembok terakhir", validasi di aplikasi adalah "penjaga pintu".

**Yang paling menyenangkan:** `$request->validate([...])` **otomatis** membalikkan user ke form + menampilkan pesan error kalau ada yang salah. Kita tidak perlu menulis if-else panjang.

| Kode lain | Artinya |
|---|---|
| `->create([...])` | simpan data baru ke database |
| `redirect()->route(...)` | pindahkan user ke halaman lain |
| `->with('status', '...')` | titip pesan sekali tampil (dibaca view lewat `session('status')`) |

```php
public function destroy(Request $request, string $id)
{
    $request->user()->transactions()->findOrFail($id)->delete();

    return redirect()->route('transaksi.index')->with('status', 'Transaksi berhasil dihapus.');
}
```

| Kode | Artinya |
|---|---|
| `findOrFail($id)` | cari data dengan id itu; **kalau tidak ada, langsung error 404** |
| `->delete()` | hapus barisnya |

> **Perhatikan urutannya:** `$request->user()->transactions()->findOrFail($id)`. Kalau ditulis `Transaction::findOrFail($id)`, maka user A bisa menghapus transaksi user B! Ini **poin keamanan** yang bagus dijelaskan saat sidang.

### 6b. `DashboardController.php`

```php
public function __invoke(): View
{
    $income  = auth()->user()->transactions()->where('type', 'pemasukan')->sum('amount');
    $expense = auth()->user()->transactions()->where('type', 'pengeluaran')->sum('amount');

    return view('dashboard', [
        'income'  => $income,
        'expense' => $expense,
        'balance' => $income - $expense,
        'latest'  => auth()->user()->transactions()->latest('date')->latest('id')->take(5)->get(),
    ]);
}
```

| Kode | Artinya |
|---|---|
| `__invoke()` | method yang otomatis dijalankan saat controller ini dipanggil |
| `auth()->user()` | user yang sedang login (sama seperti `$request->user()`) |
| `->sum('amount')` | **jumlahkan** semua nilai kolom `amount` |
| `->take(5)` | ambil maksimal 5 baris saja |
| `: View` | penanda bahwa hasilnya berupa tampilan |

Untuk grafik per kategori, dipakai `selectRaw('category, sum(amount) as total')->groupBy('category')`:

| Kode | Artinya |
|---|---|
| `selectRaw('...')` | tulis perintah database mentah (SQL) sebagai teks |
| `sum(amount) as total` | jumlahkan `amount`, beri nama hasilnya `total` |
| `groupBy('category')` | kelompokkan per kategori (jadi satu baris = satu kategori) |
| `orderByDesc('total')` | urutkan dari total terbesar |

✅ **Checkpoint Langkah 6:** belum kelihatan di layar. Kita baru bikin "mesin" lengkap: tabel, model, alamat, dan pelayan.

---

## Langkah 7 — View: etalase yang dilihat user

View ada di `resources/views/`. Struktur project ini:

```
resources/views/
├─ layouts/guest.blade.php      -> bungkus halaman login/register
├─ layouts/app.blade.php        -> bungkus halaman setelah login (sidebar + isi)
├─ layouts/sidebar.blade.php    -> menu merah di kiri
├─ components/                  -> potongan kecil yang dipakai berulang
├─ dashboard.blade.php
└─ transaksi/
   ├─ index.blade.php           -> tabel daftar + filter + tombol hapus
   ├─ create.blade.php          -> bungkus form
   └─ partials/form.blade.php   -> isi formnya
```

### 7a. Layout: bingkai halaman

`layouts/app.blade.php` itu "bingkai" yang dipakai semua halaman setelah login. Bagian penting:

```blade
<body>
    <div class="min-h-screen bg-gray-50 lg:flex">
        @include('layouts.sidebar')          {{-- menu kiri --}}

        <div class="min-w-0 flex-1">
            <main>
                {{ $slot }}                  {{-- ISI HALAMAN ditaruh di sini --}}
            </main>
        </div>
    </div>
</body>
```

| Kode | Artinya |
|---|---|
| `@include('layouts.sidebar')` | "tempel file view lain di titik ini" |
| `{{ $slot }}` | "lubang" tempat isi halaman dimasukkan |
| `{{ config('app.name', 'TrackKas') }}` | ambil `APP_NAME` dari `.env`; `TrackKas` = cadangan kalau kosong |

Lalu di setiap halaman cukup pakai bingkai itu:

```blade
<x-app-layout>
    <x-page-header title="Transaksi" subtitle="Riwayat semua transaksi Anda" />

    Halaman ini otomatis dimasukkan ke dalam {{ $slot }} di atas.
</x-app-layout>
```

### 7b. Menampilkan data

```blade
@foreach ($transactions as $transaction)
    <tr>
        <td>{{ $transaction->date->format('d M Y') }}</td>
        <td>{{ $transaction->category }}</td>
        <td>{{ $transaction->type === 'pemasukan' ? '+' : '-' }}<x-rupiah :value="$transaction->amount" /></td>
    </tr>
@endforeach
```

| Kode | Artinya |
|---|---|
| `{{ $transactions }}` | tampilkan data (otomatis diamankan dari script jahat) |
| `@foreach` / `@endforeach` | ulangi blok ini untuk setiap baris |
| `$transaction->category` | ambil kolom `category` dari baris itu |
| `->format('d M Y')` | ubah tanggal jadi "03 Oct 2026" |
| `===` | membandingkan **persis sama** |
| `? '+' : '-'` | kalau pemasukan tampilkan `+`, kalau tidak `-` |
| `<x-rupiah :value="$amount" />` | komponen yang mencetak "Rp 1.500.000" |
| `:value="..."` | tanda titik dua = kirim nilai PHP ke komponen |

### 7c. Form yang aman

```blade
<form method="POST" action="{{ route('transaksi.destroy', $transaction) }}">
    @csrf
    @method('DELETE')
    <button type="submit">Hapus</button>
</form>
```

| Kode | Artinya | Kalau lupa? |
|---|---|---|
| `@csrf` | token keamanan; membuktikan form ini dari situs kita sendiri | error **419 Page Expired** |
| `@method('DELETE')` | HTML cuma punya GET/POST, ini "menyamar" jadi DELETE | route tidak ketemu |
| `{{ route('transaksi.destroy', $transaction) }}` | bikin URL otomatis dari nama route | URL salah tulis |
| `onsubmit="return confirm(...)"` | memunculkan konfirmasi sebelum hapus | data langsung terhapus |

### 7d. Form input

```blade
<x-input-label for="amount" value="Jumlah (Rp)" />

<x-text-input id="amount" name="amount" type="number" min="0" max="999999999999.99" step="0.01"
              :value="old('amount')" class="block w-full pl-11" />

<x-input-error :messages="$errors->get('amount')" class="mt-2" />
```

| Kode | Artinya |
|---|---|
| `<x-input-label>` `<x-text-input>` `<x-input-error>` | komponen form siap pakai |
| `name="amount"` | nama ini yang dibaca controller (`$request->amount`) |
| `min` / `max` | batas angka yang boleh diketik (di form kategori: `maxlength="50"` untuk batas huruf) |
| `old('amount')` | ingat isian sebelumnya; jadi tidak perlu ketik ulang kalau ada error |
| `$errors->get('amount')` | daftar pesan error khusus kolom amount |

✅ **Checkpoint Langkah 7:** halaman dashboard dan transaksi sudah kelihatan.

---

## Langkah 8 — Komponen: trik bikin HTML jadi pendek

**Apa itu komponen?** Potongan tampilan yang disimpan sekali, dipakai berkali-kali. Dipakai dengan `<x-nama />`.

### a. `x-icon` — semua ikon kumpul di 1 file

Dulu setiap ikon ditulis panjang begini, berulang di banyak tempat:

```html
<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 4.5v15m7.5-7.5h-15" />
</svg>
```

Sekarang semua "gambar ikon" dikumpulkan di `resources/views/components/icon.blade.php`, dan view cukup:

```blade
<x-icon name="plus" class="h-4 w-4" />
```

Ganti ikon = ganti `name`. Pilihan yang tersedia: `plus`, `arrow-up`, `arrow-down`, `wallet`, `logout`, `dashboard`, `list`, `plus-circle`, `info`, `chevrons-left`, `empty-list`, `empty-box`. Atur ukuran lewat `class` (mis. `h-5 w-5`).

### b. `x-sidebar-link` — 3 menu sidebar, 1 baris masing-masing

```blade
<x-sidebar-link route="dashboard" icon="dashboard" :active="request()->routeIs('dashboard')">Dashboard</x-sidebar-link>
```

Artinya: alamatnya route `dashboard`, ikonnya `dashboard`, dan `:active` = "sedang dibuka atau tidak" (untuk menyalakan sorotan). Bandingkan dengan sebelumnya yang satu menu butuh belasan baris.

### c. `x-category-breakdown` — 2 panel kategori di dashboard

```blade
<x-category-breakdown title="Pemasukan per Kategori" :rows="$incomeByCategory" color="green" />
<x-category-breakdown title="Pengeluaran per Kategori" :rows="$expenseByCategory" />
```

Lebar batangnya dihitung di dalam komponen (`round($row->total / $max * 100)`%), jadi view tidak penuh matematika.

### d. Komponen kecil lainnya

`x-brand-logo` (logo), `x-page-header` (judul halaman), `x-rupiah` (format Rupiah), `x-text-input`, `x-input-label`, `x-input-error`, `x-primary-button`.

> **Hasilnya:** `dashboard.blade.php` sekarang **101 baris** (sebelum dirapikan kira-kira dua kali lipatnya), `sidebar.blade.php` jadi **64 baris**. **Tampilannya tetap identik**, karena yang dipindah cuma HTML-nya, bukan CSS-nya.

**Kalau ditanya "kok view-nya pendek?"** jawab: *"Karena HTML yang berulang saya jadikan komponen Blade — ikon dikumpulkan di `icon.blade.php`, menu sidebar jadi `sidebar-link`, dan dua panel kategori jadi `category-breakdown`. Kodenya lebih pendek dan gampang diubah, tapi tampilan tetap sama karena CSS-nya tidak diubah."*

---

## Langkah 9 — Styling: Tailwind + CSS + Alpine

**1. Warna khusus** ada di `tailwind.config.js`:

```js
colors: {
    brand: { 50: '#fdf3f4', 600: '#c81e2b', 900: '#74161e' /* merah marun */ },
},
```

Dari situ muncul kelas `bg-brand-600`, `text-brand-700`, dan seterusnya.

**2. Hal yang kelas Tailwind tidak bisa** (mis. lebar sidebar saat dikecilkan, animasi halus) ditulis manual di `resources/css/app.css`:

```css
@layer components {
    .app-sidebar { width: 100%; }

    @media (min-width: 1024px) {
        .app-sidebar { width: 270px; transition: width 200ms ease; }
        html[data-sidebar='collapsed'] .app-sidebar { width: 88px; }
        html[data-sidebar='collapsed'] .app-sidebar .sidebar-label { display: none; }
    }
}
```

| Kode | Artinya |
|---|---|
| `@layer components` | tempat menaruh kelas buatan sendiri di Tailwind |
| `@media (min-width: 1024px)` | aturan di dalam ini hanya berlaku pada layar ≥1024px |
| `html[data-sidebar='collapsed']` | pilih elemen yang punya penanda keadaan "collapsed" |
| `transition` | perubahan lebar berlangsung halus, tidak "jeglek" |

**3. Interaksi** (tombol perkecil sidebar) ditulis di `resources/js/app.js` pakai **Alpine**:

```js
Alpine.data('sidebar', () => ({
    collapsed: document.documentElement.dataset.sidebar === 'collapsed',

    toggle() {
        this.collapsed = !this.collapsed;                                 // balik keadaan
        document.documentElement.dataset.sidebar = this.collapsed ? 'collapsed' : 'expanded';
        localStorage.setItem('trackkas-sidebar', document.documentElement.dataset.sidebar);
    },
}));
```

| Kode | Artinya |
|---|---|
| `Alpine.data('sidebar', ...)` | bikin perilaku bernama `sidebar` |
| `this.collapsed = !this.collapsed` | ubah benar/salah jadi kebalikannya |
| `localStorage.setItem(...)` | simpan pilihan supaya diingat saat halaman dimuat ulang |

Di HTML, Alpine dipakai sebagai atribut: `x-data="sidebar"` dan `@click="toggle()"`.

---

## Langkah 10 — Test: pembuktian kode masih jalan

```bash
php artisan test
```

Hasil terakhir di project ini: **36 test, 104 pemeriksaan, semuanya lulus.**

Contoh isi test:

- "transaksi bisa disimpan"
- "user tidak bisa menghapus transaksi orang lain"
- "dashboard menjumlahkan dengan benar"
- "email dianggap belum berubah kalau tidak diubah"

> **Kenapa ini penting?** Setiap kali kamu mengubah kode, jalankan test ini. Kalau ada yang merah, berarti ada yang rusak. Ini "rem pengaman"-mu.

Ada juga perintah merapikan gaya penulisan kode:

```bash
./vendor/bin/pint
```

---

## Langkah 11 — Nyalakan semuanya & coba

Buka **dua terminal** di folder project:

**Terminal 1:**
```bash
php artisan serve
```

**Terminal 2:**
```bash
npm run dev
```

Lalu buka browser → `http://127.0.0.1:8000`.

**Urutan uji coba:**
1. **Daftar** akun baru.
2. **Login** dengan akun itu.
3. **Tambah Transaksi** → isi form → Simpan. Harus muncul pesan hijau "Transaksi berhasil ditambahkan."
4. Lihat **Dashboard**: pemasukan, pengeluaran, saldo, transaksi terbaru, dan grafik per kategori.
5. Coba filter **Pemasukan / Pengeluaran**.
6. Coba **Hapus** satu transaksi → ada konfirmasi dulu.

✅ **Kalau keenamnya berhasil, website kamu SETENGAH JADI — eh, sudah jadi!** 🎉

---

## Langkah 12 — Kalau mau kirim ke teman (opsional)

Supaya temanmu bisa menjalankan project ini, kirim foldernya **tanpa** folder `vendor`, `node_modules`, dan file `.env`. Lalu ajak dia jalanin:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate

# Buka file .env, sesuaikan database-nya (contoh default-nya masih SQLite):
#   DB_CONNECTION=mysql
#   DB_DATABASE=db_trackkas
#   DB_USERNAME=root
#   DB_PASSWORD=

php artisan config:clear
php artisan migrate
npm run dev
```

*Kenapa `.env` tidak dikirim?* Karena isinya rahasia (password database, kunci aplikasi). Yang dikirim adalah `.env.example` — contoh kosongnya.

> **Perhatikan:** file `.env.example` bawaan Laravel masih memakai `DB_CONNECTION=sqlite` (database berupa satu file, tanpa server). Kalau mau memakai MySQL seperti di Laragon, **buka `.env` dan ubah `DB_*`-nya** seperti di atas, lalu jalankan `php artisan config:clear`.
>
> *(Di Windows CMD, perintah `cp` bisa diganti `copy`.)*

---

# BAB 6 — Kamus Syntax (buka saat lupa)

## 6.1 Perintah Artisan (`php artisan ...`)

Semua perintah Laravel lewat kata `php artisan`. Berikut yang dipakai di project ini.

| Perintah | Fungsinya |
|---|---|
| `php artisan serve` | nyalakan website untuk uji coba |
| `php artisan migrate` | buat tabel sesuai file migration |
| `php artisan migrate:fresh` | **hapus semua** tabel lalu buat ulang (data hilang!) |
| `php artisan migrate:rollback` | batalkan migrasi terakhir |
| `php artisan make:model Transaction` | buat file Model baru |
| `php artisan make:controller NamaController` | buat file Controller baru |
| `php artisan make:migration nama_file` | buat file migration baru |
| `php artisan route:list` | tampilkan semua daftar alamat |
| `php artisan config:clear` | hapus salinan pengaturan (setelah ubah `.env`) |
| `php artisan view:clear` | hapus salinan tampilan yang di-cache |
| `php artisan key:generate` | buat kunci rahasia aplikasi |
| `php artisan tinker` | "terminal ajaib" untuk mencoba kode PHP langsung |
| `php artisan test` | jalankan semua test |
| `php artisan dev` | nyalakan server + Vite + antrian + penampil log sekaligus |
| `php artisan down` / `up` | matikan / nyalakan aplikasi untuk perbaikan |

## 6.2 Syntax PHP

| Simbol/kode | Artinya dalam bahasa manusia |
|---|---|
| `<?php` | "mulai kode PHP" |
| `$nama` | variabel (wadah menyimpan nilai) |
| `->` | "ambil bagian dalam" dari objek, mis. `$user->name` |
| `=>` | "berpasangan dengan", dipakai di array: `'kunci' => nilai` |
| `::` | panggil sesuatu dari kelas, mis. `Model::class` |
| `fn (...) => ...` | cara singkat menulis function |
| `function ...() { }` | bikin function biasa |
| `namespace App\Models;` | alamat file, biar nama tidak bentrok |
| `use ... ;` | "saya mau pakai alat ini" |
| `class X extends Y` | bikin kelas X yang mewarisi kemampuan Y |
| `public` / `private` | boleh dipakai dari luar / hanya dari dalam kelas |
| `return` | "ini hasilnya" dan hentikan function |
| `===` | sama **persis** (termasuk tipenya) |
| `!=` | tidak sama |
| `?:` | "kalau ya ini, kalau tidak itu" |
| `!` (dalam `!$x`) | kebalikannya, "tidak" |
| `[ ... ]` | array (kumpulan nilai) |
| `=> 'nullable'` | nilai default saat parameter tidak diisi |

## 6.3 Method Eloquent (saat ngurus data)

| Method | Artinya |
|---|---|
| `->get()` | ambil semua hasil |
| `->first()` | ambil hasil pertama |
| `->find($id)` | cari berdasarkan id |
| `->findOrFail($id)` | sama, tapi error 404 kalau tidak ada |
| `->where('kolom', $nilai)` | filter: kolom harus sama dengan nilai |
| `->when(...)` | filter opsional ("kalau ada nilainya baru dipakai") |
| `->latest('date')` | urutkan dari yang terbaru |
| `->orderByDesc('total')` | urutkan dari yang terbesar |
| `->take(5)` | batasi maksimal 5 hasil |
| `->sum('amount')` | jumlahkan kolom `amount` |
| `->groupBy('category')` | kelompokkan per kategori |
| `->selectRaw('...')` | tulis perintah database manual |
| `->create([...])` | simpan data baru |
| `->delete()` | hapus |
| `->isEmpty()` | "kosong semua?" (hasil benar/salah) |
| `->max('total')` | nilai terbesar dari kolom `total` |

## 6.4 Syntax Blade (di file `.blade.php`)

| Syntax | Artinya |
|---|---|
| `{{ $variabel }}` | tampilkan data (otomatis aman dari script jahat) |
| `{!! $html !!}` | tampilkan HTML mentah — hati-hati, hanya untuk data terpercaya |
| `@if` `@elseif` `@else` `@endif` | percabangan |
| `@foreach` `@endforeach` | mengulang |
| `@include('file')` | tempel view lain |
| `<x-nama />` | pakai komponen |
| `<x-app-layout> ... </x-app-layout>` | bungkus halaman dengan layout |
| `{{ $slot }}` | lubang tempat isi komponen masuk |
| `@props([...])` | tentukan data yang dikirim ke komponen |
| `@csrf` | token keamanan wajib di form |
| `@method('DELETE')` | "menyamar" jadi metode DELETE |
| `route('nama.route')` | bikin URL dari nama route |
| `old('kolom')` | ingat isian sebelumnya |
| `$errors->get('kolom')` | pesan error sebuah kolom |
| `@checked(...)` | pasang centang otomatis |
| `{{-- komentar --}}` | komentar, tidak tampil di layar |

## 6.5 Perintah lain

| Perintah | Artinya |
|---|---|
| `composer install` | unduh semua library PHP |
| `composer require paket` | tambah library PHP baru |
| `composer run dev` | nyalakan semua proses pengembangan |
| `npm install` | unduh semua library tampilan |
| `npm run dev` | jalankan Vite (mode pengembangan, auto-refresh) |
| `npm run build` | bikin versi final (dipakai saat upload ke hosting) |
| `cd folder` | masuk folder |
| `ls` | lihat isi folder |
| `code .` | buka folder ini di VS Code |

---

# BAB 7 — Peta File: Mau Ganti X, Buka Y

| Mau ganti... | Buka file |
|---|---|
| Menu sidebar | `resources/views/layouts/sidebar.blade.php` |
| Tampilan halaman login | `resources/views/layouts/guest.blade.php` + `resources/views/auth/` |
| Isi dashboard | `resources/views/dashboard.blade.php` |
| Form tambah transaksi | `resources/views/transaksi/partials/form.blade.php` |
| Tabel daftar transaksi | `resources/views/transaksi/index.blade.php` |
| Ikon | `resources/views/components/icon.blade.php` |
| Alamat/URL | `routes/web.php` |
| Logika ambil/simpan data | `app/Http/Controllers/` |
| Struktur tabel | `database/migrations/` |
| Aturan kolom yang boleh diisi | `app/Models/Transaction.php` |
| Warna | `tailwind.config.js` |
| Judul tab browser | `.env` → `APP_NAME` |
| "Kelola keuangan dengan mudah" | `resources/views/layouts/guest.blade.php` |

---

# BAB 8 — Kalau Muncul Error

| Pesan/Gejala | Artinya | Solusinya |
|---|---|---|
| `composer: command not found` | Composer tidak ada di terminal yang kamu pakai | pakai **Terminal Here** dari Laragon, atau install Composer lalu buka terminal baru |
| `php: command not found` | PHP tidak dikenali | jalankan terminal dari Laragon (Laragon sudah menyiapkan PHP) |
| `SQLSTATE... unknown database` | database-nya belum dibuat | buat database bernama sama dengan `DB_DATABASE` di phpMyAdmin |
| `SQLSTATE... Access denied` | salah user/password database | cek `DB_USERNAME` / `DB_PASSWORD` di `.env` |
| `Connection refused` | MySQL belum nyala | klik **Start All** di Laragon |
| `419 Page Expired` | form tidak punya token | tambahkan `@csrf` di dalam `<form>` |
| Tampilan polos/tanpa warna | Vite belum jalan | jalankan `npm run dev` di terminal kedua |
| Ubah `.env` tapi tidak berpengaruh | masih pakai pengaturan lama | jalankan `php artisan config:clear` |
| Ubah tampilan tapi tidak berubah | cache view | jalankan `php artisan view:clear` |
| `Vite manifest not found` | CSS/JS belum dikompilasi | jalankan `npm run dev` (atau `npm run build`) |
| `Route [x] not defined` | nama route salah/tidak ada | cek di `routes/web.php`, atau jalankan `php artisan route:list` |
| `Class "App\Models\..." not found` | nama file/kelas tidak cocok | pastikan nama file sama dengan nama kelas, huruf besar-kecilnya |
| `Target class ... does not exist` | controller belum dibuat/salah nama | cek `app/Http/Controllers/` |
| Halaman putih tanpa pesan | ada error tersembunyi | buka `storage/logs/laravel.log`, atau nyalakan `php artisan dev` agar log tampil |
| Port 8000 sudah dipakai | ada server lain jalan | jalankan `php artisan serve --port=8001` |

**Kebiasaan penyelamat:** kalau bingung, jalankan tiga ini berurutan lalu coba lagi:

```bash
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

# BAB 9 — Ringkasan: 10 Perintah Inti

Kalau temanmu cuma mau tahu "garis besarnya", ini urutannya:

```bash
# 1. Siapkan alat: nyalakan Laragon (Start All), buka Terminal Here

# 2. Bikin project
cd C:\laragon\www
composer create-project laravel/laravel trackkasdev
cd trackkasdev

# 3. Pasang tampilan (Tailwind dll)
npm install

# 4. Atur database di file .env, lalu buat database "db_trackkas"
php artisan config:clear

# 5. Pasang halaman login
composer require laravel/breeze --dev
php artisan breeze:install blade

# 6. Buat tabel
php artisan migrate

# 7. (Lalu tulis kode: migration → model → route → controller → view → komponen)

# 8. Nyalakan website — di dua terminal berbeda
php artisan serve      # terminal 1
npm run dev            # terminal 2

# 9. Cek kode masih sehat
php artisan test

# 10. Selesai. Buka http://127.0.0.1:8000
```

**Hafalkan alurnya, bukan hafal kodenya:** **Route → Controller → Model → View.**
Kalau ditanya apa pun, kembali ke empat kata itu. 🎯
