# Rencana Darurat 2 Hari — TrackKas

**Hari ini: Selasa, 29 September 2026. Deadline: Kamis, 1 Oktober 2026.**

Jadi kamu punya **malam ini** (2–3 jam), **Rabu** (satu hari penuh), dan **Kamis pagi**.

Dokumen ini bukan teori. Ini jadwal yang harus diikuti. Baca sampai habis (15 menit), lalu mulai.

---

## 0. Realitas dulu: apa yang harus kamu kejar, apa yang boleh dilewati

Kamu bilang Laravel-mu baru sampai CRUD dasar, belum sampai auth dan Breeze. Ini kabar baiknya: **kamu tidak perlu menguasai Breeze untuk lulus.**

Penguji tidak mungkin menuntutmu menjelaskan seluruh sistem autentikasi Laravel dalam waktu sidang. Yang dinilai adalah:

| Prioritas | Yang dinilai | Porsi realistisnya |
|---|---|---|
| **Tinggi** | Alur data: dari klik tombol → route → controller → model → database → tampilan | ~50% pertanyaan |
| **Tinggi** | CRUD-nya sendiri — ini **kamu sudah bisa** | ~20% |
| **Tinggi** | Demo aplikasi berjalan lancar | Dinilai terpisah |
| **Sedang** | Kenapa pilih cara itu, bukan cara lain (keamanan, validasi) | ~20% |
| **Rendah** | Detail internal Breeze, Tailwind, Alpine, Vite | ~10% |

**Strategi:** kuasai 3 baris pertama sampai benar-benar lancar. Untuk Breeze, kamu **tidak perlu bisa menulisnya** — kamu cukup bisa menjelaskan **alurnya** dan tahu **file mana yang harus dibuka** kalau ditanya.

**Tiga aturan dalam 2 hari ini:**

1. **Jangan menambah fitur baru.** Kecuali satu hal kecil di bagian 7. Fitur baru = risiko bug baru = demo gagal. Lebih baik aplikasi sederhana yang jalan mulus.
2. **Jangan belajar hal yang tidak ada di projectmu.** Kamu tidak akan ditanya React, Docker, atau microservice.
3. **Latih bicara, bukan hanya membaca.** Baca 10 kali tidak ada gunanya kalau belum pernah diucapkan sekali.

---

## 1. Jadwal malam ini (Selasa) — 2 jam

| Waktu | Kegiatan | Hasil yang diharapkan |
|---|---|---|
| 30 menit | Baca [http-method-get-post-put-patch.md](http-method-get-post-put-patch.md), lalu kerjakan 6 latihan di Bagian 9 | Bisa jawab bedanya GET/POST/PUT/PATCH/DELETE |
| 30 menit | Baca [peta-belajar-trackkas.md](peta-belajar-trackkas.md) bagian 1–5 | Kenal peta folder projectnya |
| 30 menit | Baca **bagian 4 dokumen ini** (crash course auth & Breeze) | Tahu alur login dan file-file Breeze |
| 30 menit | Hafalkan **10 kalimat kunci** di [Bagian 14 tanya-jawab-sidang.md](tanya-jawab-sidang.md) | Bisa ucapkan tanpa melihat |

**Jangan lanjut ke jam berikutnya kalau belum selesai.** Lebih baik 3 sesi selesai daripada 4 sesi setengah.

---

## 2. Jadwal Rabu (H-1) — hari terberat

| Waktu | Kegiatan |
|---|---|
| 09.00–11.00 | [tanya-jawab-sidang.md](tanya-jawab-sidang.md) **Bagian 2 (Controller, Q25–Q48)** dan **Bagian 4 (Model, Q61–Q78)** |
| 11.00–12.00 | **Latihan ngomong.** Rekam pakai HP. Putar ulang. Perbaiki bagian yang kamu terdengar ragu. |
| 13.00–15.00 | **Bagian 5 (Migration, Q79–Q92)** dan **Bagian 6 (Keamanan, Q93–Q112)** — ini bagian bernilai tinggi |
| 15.00–16.00 | **Bagian 3 (Validasi, Q49–Q60)** dan **Bagian 7 (Blade & Komponen, Q113–Q132)** |
| 16.00–17.00 | **Bagian 8 (Tailwind, Q133–Q146)**, **Bagian 9 (Alpine/Vite, Q147–Q154)**, **Bagian 10 (Testing, Q155–Q162)** — cukup pahami garis besarnya |
| 19.00–19.30 | **Perbaikan cepat** (bagian 7 dokumen ini) + pastikan aplikasi hidup (bagian 6) |
| 19.30–21.30 | **LATIHAN DEMO 3 KALI** dari login sampai hapus, tanpa melihat catatan. Ini sesi paling penting hari ini. |
| 21.30–22.00 | **Bagian 11 (21 pertanyaan jebakan)** dan **Bagian 12 (kalau tidak tahu)** |
| Sebelum tidur | **[jawaban-vs-notepad-dan-excel.md](jawaban-vs-notepad-dan-excel.md)** — jawaban "apa bedanya dengan Notepad", plus demo pembuktian 60 detiknya |

Kalau waktunya tidak cukup, **potong sesi 16.00–17.00**. Jangan potong sesi demo.

---

## 3. Jadwal Kamis (hari-H)

| Waktu | Kegiatan |
|---|---|
| Bangun tidur | Jalankan aplikasinya sekali. Pastikan login dan dashboard tampil. **Jangan demokan tanpa pernah dicoba hari itu.** |
| 45 menit sebelum | Baca ulang: bagian 6 (aplikasi hidup), bagian 9 (anti-panic), naskah pembukaan di bagian 10 |
| 15 menit sebelum | Baca ulang **10 kalimat kunci** dari [tanya-jawab-sidang.md Bagian 14](tanya-jawab-sidang.md) |
| 5 menit sebelum | Tarik napas. Kamu sudah latihan 3 kali. |
| Saat masuk | Buka laptop, buka VS Code di project, buka browser di `localhost:8000`, buka terminal. Siapkan sebelum diminta. |

**Yang harus dibawa:** charger laptop, HP dengan hotspot (kalau wifi kampus mati), dan **screenshot cadangan** — beberapa tangkapan layar aplikasi yang sudah kamu buat dari malam sebelumnya. Kalau laptop bermasalah, kamu masih bisa menunjukkan tampilannya.

---

## 4. Crash course Auth & Breeze — bagian yang belum kamu pelajari

Ini bagian terpenting dari dokumen ini. Baca pelan-pelan.

### 4.1 Kabar jujur: Breeze itu bukan buatanmu, dan itu tidak masalah

**Breeze** adalah paket starter resmi dari Laravel. Cara pasangnya satu perintah:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

Sekali jalankan, dia langsung membuatkan: route login, register, lupa password, semua controller-nya, semua view-nya, dan semua test-nya. Puluhan file sekaligus.

**Jadi bagaimana kamu menjelaskannya saat sidang?** Jangan berpura-pura menulisnya sendiri. Framing yang benar:

> "Sistem autentikasinya memakai Laravel Breeze, paket starter resmi dari Laravel. Saya memakainya karena autentikasi adalah bagian yang risikonya tinggi kalau dibangun sendiri — soal hashing password, CSRF, dan pengelolaan session. Bagian yang saya kerjakan adalah menyesuaikan tampilannya dengan desain TrackKas, mengarahkan halaman depan ke login, dan seluruh fitur transaksi beserta dashboard-nya."

**Kalimat itu kuat, bukan lemah.** Memilih tidak menulis ulang roda adalah keputusan engineering yang benar. Kalau kamu mencoba mengaku menulisnya sendiri lalu ditanya detail, kamu akan tenggelam.

### 4.2 Peta file yang diberikan Breeze

Hafalkan tabel ini. Kalau penguji bertanya tentang auth, kamu bisa langsung **membuka file yang tepat** — itu sudah setengah jawaban.

| File | Isinya |
|---|---|
| `routes/auth.php` | Semua URL login, register, logout, reset password |
| `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | Proses **login** dan **logout** |
| `app/Http/Controllers/Auth/RegisteredUserController.php` | Proses **daftar akun baru** |
| `app/Http/Controllers/Auth/PasswordResetLinkController.php` | Kirim tautan reset password |
| `app/Http/Controllers/Auth/NewPasswordController.php` | Simpan password baru |
| `app/Http/Controllers/Auth/PasswordController.php` | Ganti password dari halaman profil |
| `app/Http/Controllers/Auth/VerifyEmailController.php` | Verifikasi email |
| `app/Http/Requests/Auth/LoginRequest.php` | Aturan & proses percobaan login |
| `resources/views/auth/*.blade.php` | Tampilan halaman login, register, dst. |
| `tests/Feature/Auth/*.php` | Test bawaan untuk semua alur auth |

### 4.3 Alur LOGIN — hafalkan gambar ini

**Langkah 1.** User buka `/login`.

```
GET /login  →  AuthenticatedSessionController@create()
              return view('auth.login');
```

Method `create()` tugasnya cuma menampilkan form.

**Langkah 2.** User isi email dan password, klik Masuk. Form dikirim.

```
POST /login  →  AuthenticatedSessionController@store(LoginRequest $request)
```

Perhatikan, parameternya bukan `Request` biasa, tapi **`LoginRequest`** — itu class khusus untuk request login, isinya di `app/Http/Requests/Auth/LoginRequest.php`.

**Langkah 3.** Di dalamnya tiga baris ini yang bekerja:

```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();              // 1. cocokkan email & password

    $request->session()->regenerate();     // 2. perbarui session (anti session fixation)

    return redirect()->intended(route('dashboard', absolute: false));  // 3. masuk dashboard
}
```

**Langkah 4.** `$request->authenticate()` ada di `LoginRequest.php`, isinya:

```php
public function authenticate(): void
{
    $this->ensureIsNotRateLimited();       // cek batas percobaan

    if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
        RateLimiter::hit($this->throttleKey());   // hitung percobaan gagal

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),      // pesan: kredensial tidak cocok
        ]);
    }

    RateLimiter::clear($this->throttleKey());
}
```

> **`Auth::attempt()`** adalah inti login: dia mencari user berdasarkan email, lalu membandingkan password yang dikirim dengan hash di database. Kalau cocok, session user ditandai sudah login dan method-nya mengembalikan `true`. Kalau tidak cocok, dia mengembalikan `false` dan kita lempar pesan error.

> **Rate limiter** adalah pembatas percobaan. Kalau salah password 5 kali, akun itu ditahan sementara. Ini pengamanan terhadap serangan coba-coba password.

**Kalimat siap pakai:**
> "Alur login-nya: GET `/login` menampilkan form, POST `/login` memprosesnya. Di dalam prosesnya ada `Auth::attempt()` yang mencocokkan email dan password, lalu session diperbarui dengan `regenerate()` supaya aman, dan user diarahkan ke dashboard. Ada juga pembatas percobaan salah password — lima kali, setelah itu ditahan sementara."

### 4.4 Alur REGISTER

```php
public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    event(new Registered($user));

    Auth::login($user);

    return redirect(route('dashboard', absolute: false));
}
```

> "Ada empat langkah. Pertama, validasi: nama wajib, email wajib dan harus belum pernah terdaftar, dibuktikan oleh aturan `unique`, lalu password wajib dan harus sama dengan kolom konfirmasinya, dibuktikan oleh aturan `confirmed`. Kedua, data user dibuat, dan password-nya di-hash dengan `Hash::make`. Ketiga, user langsung login otomatis dengan `Auth::login`. Keempat, diarahkan ke dashboard."

**Dua istilah yang mungkin ditanya:**

- **`unique:App\Models\User`** → memastikan tidak ada dua akun dengan email yang sama.
- **`confirmed`** → memastikan field `password` sama dengan `password_confirmation`. Karena itu di form register ada dua kolom password.

### 4.5 Alur LOGOUT

```php
public function destroy(Request $request): RedirectResponse
{
    Auth::guard('web')->logout();          // hapus status login

    $request->session()->invalidate();     // buang seluruh isi session

    $request->session()->regenerateToken(); // ganti token CSRF

    return redirect('/');
}
```

> "Logout ada di method `destroy`, jadi rutenya `POST /logout`. Tiga langkah: menghapus status login, membuang isi session, dan mengganti token CSRF. Setelah itu user dikembalikan ke halaman depan, yang otomatis mengarah ke login."

### 4.6 Alur LUPA PASSWORD (tiga langkah)

Ini yang paling jarang ditanya, tapi bagus kalau kamu tahu alurnya.

1. User isi email di `/forgot-password` → server **mengirim email** berisi tautan unik.
2. Tautan itu berisi **token** → membuka `/reset-password/{token}`.
3. User isi password baru → dikirim ke `POST /reset-password` → disimpan.

> "Saat ini pengiriman email-nya saya setel ke `log` di file `.env`, jadi tautannya tidak benar-benar terkirim ke inbox, tapi tercatat di file log. Untuk dipakai sungguhan, tinggal mengubah konfigurasi mail-nya."

⚠️ **Ini detail bagus yang jarang diketahui pemula.** Kalau kamu bisa menyebutkannya, penguji akan terkesan. Cek sendiri di `.env`: `MAIL_MAILER=log`.

### 4.7 Middleware `guest` vs `auth`

Ini pembeda yang sering ditanya.

| Middleware | Artinya | Dipasang di |
|---|---|---|
| `auth` | **Hanya untuk yang sudah login.** Tamu dilempar ke login. | Dashboard, transaksi, profil |
| `guest` | **Hanya untuk yang belum login.** Yang sudah login dialihkan ke dashboard. | Login, register, lupa password |

> "Middleware `guest` dipakai di halaman login dan register — kalau user yang sudah login membukanya, dia dialihkan kembali ke dashboard, karena tidak masuk akal melihat form login saat sudah masuk. Middleware `auth` kebalikannya: hanya untuk yang sudah login, dan tamu dilempar ke halaman login."

```php
// routes/auth.php
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
```

### 4.8 Cara membaca file Breeze yang belum pernah kamu lihat

Kalau penguji menunjuk file yang belum pernah kamu buka, jangan panik. Pakai resep ini:

1. **Lihat nama method-nya.** `create` = tampilkan form. `store` = simpan. `destroy` = hapus. `update` = ubah. Konvensi ini berlaku di seluruh Laravel, termasuk Breeze.
2. **Lihat tipe kembaliannya.** `: View` berarti menampilkan halaman. `: RedirectResponse` berarti pindah halaman.
3. **Cari yang kamu sudah kenal.** Validasi, `Model::create()`, `redirect()`, `session()` — semua itu pola yang sama seperti di controller transaksimu. Kamu sudah bisa membacanya.
4. **Abaikan yang belum kamu kenal.** Kalau ada `event(new Registered($user))`, cukup bilang: *"Baris itu melempar event setelah user dibuat — biasanya dipakai untuk memicu pengiriman email verifikasi. Saya belum mempelajarinya lebih dalam."*
5. **Terakhir, berani bilang belum tahu.** Itu jauh lebih baik daripada menebak.

### 4.9 Sepuluh pertanyaan auth yang paling mungkin muncul

| Pertanyaan | Jawaban singkat |
|---|---|
| Kenapa pakai Breeze? | Karena autentikasi berisiko tinggi kalau ditulis sendiri — soal hashing, CSRF, dan session |
| Password disimpan di mana? | Di kolom `password` tabel users, dalam bentuk hash bcrypt, bukan teks asli |
| Bagaimana password dicek? | `Auth::attempt()` mencocokkan email dan membandingkan password dengan hash-nya |
| Kenapa password di-hash? | Supaya kalau database bocor, password user tidak bisa dibaca langsung |
| Apa itu session? | Cara server mengenali bahwa request ini dari orang yang sama yang sudah login |
| Kenapa logout pakai POST? | Karena logout mengubah keadaan. Kalau pakai link GET, orang bisa memaksamu logout dari situs lain |
| Tombol back setelah logout? | Sudah tidak bisa membuka halaman dalam, karena middleware `auth` memeriksanya di server, bukan di browser |
| Kenapa ada dua field password saat daftar? | Aturan `confirmed` mencocokkan `password` dengan `password_confirmation` |
| Maksimal salah password berapa kali? | Lima kali per menit, lalu ditahan sementara oleh rate limiter |
| Verifikasi email aktif? | Belum. Implementasi `MustVerifyEmail` di model `User` masih dikomentari, jadi middleware `verified` dilewati saja |

**Kalimat penyangga — hafalkan, ini akan menyelamatkanmu:**

> "Breeze saya pakai sebagai paket starter, jadi bagian internalnya tidak saya tulis sendiri. Yang saya pahami adalah alurnya secara keseluruhan dan file mana yang menangani apa. Untuk detail keamanan yang lebih dalam, saya mempercayakannya pada paket resmi Laravel."

---

## 5. Sepuluh hal yang WAJIB bisa kamu lakukan sendiri

Ini daftar periksa. Uji dirimu dengan menutup semua dokumen, lalu lakukan:

- [ ] **1.** Menjelaskan alur satu request dari klik tombol sampai tampil di browser (3 menit, tanpa catatan)
- [ ] **2.** Menjelaskan bedanya `GET`, `POST`, `PUT`, `PATCH`, `DELETE` dan menyebutkan contohnya di projectmu
- [ ] **3.** Menjelaskan kenapa hapus pakai form + `@method('DELETE')`, bukan link
- [ ] **4.** Membuka `TransactionsController.php` dan menjelaskan keempat methodnya
- [ ] **5.** Menjelaskan kenapa semua query dimulai dari `$request->user()->transactions()`
- [ ] **6.** Menjelaskan kenapa `$request->validate()` lebih aman dari `$request->all()`
- [ ] **7.** Menjelaskan alur login Breeze (GET → POST → `Auth::attempt` → redirect)
- [ ] **8.** Menjalankan aplikasinya sendiri: `migrate`, `build`, `serve`, lalu login sampai hapus transaksi
- [ ] **9.** Menyebutkan **empat** kelemahan project yang belum kamu kembangkan
- [ ] **10.** Menjawab "saya belum tahu" dengan tenang, tanpa panik dan tanpa mengarang

Kalau **1, 2, 4, 5, 8** sudah lancar, kamu sudah cukup aman. Kalau **semuanya** sudah, kamu akan tampil bagus.

---

## 6. Pastikan aplikasinya benar-benar hidup (lakukan malam ini, jangan tunggu hari-H)

Ini kesalahan paling fatal: aplikasi tidak jalan saat sidang karena hal sepele.

```bash
cd C:/laragon/www/trackkasdev

php artisan migrate          # pastikan struktur database terbaru
npm run build                # pastikan CSS & JS terkompilasi
php artisan serve            # jalankan aplikasinya
```

Lalu buka `http://127.0.0.1:8000` dan lakukan uji cepat **lima langkah**:

1. Login berhasil masuk ke dashboard
2. Tambah satu transaksi → muncul pesan hijau
3. Dashboard menampilkan total yang benar
4. Buka daftar transaksi → datanya muncul
5. Hapus → datanya hilang

Kalau kelimanya jalan, aplikasimu aman.

**Catatan:** Laragon biasanya sudah otomatis menjalankan MySQL. Kalau muncul error koneksi database, buka aplikasi Laragon, klik **Start All**, tunggu sampai hijau, baru coba lagi.

**Kalau port 8000 dipakai proses lain:**

```bash
php artisan serve --port=8010
```

---

## 7. Perbaikan cepat 15 menit yang berdampak besar

Hanya lakukan **satu** dari daftar ini, yang paling kamu yakin aman:

### a. Ubah nama aplikasi (5 menit, berdampak besar)

Sekarang judul tab browser-mu masih **"Laravel"**, bukan "TrackKas". Itu hal pertama yang dilihat penguji saat kamu buka aplikasinya.

Buka file `.env`, cari baris:

```
APP_NAME=Laravel
```

Ubah menjadi:

```
APP_NAME=TrackKas
```

Lalu jalankan:

```bash
php artisan config:clear
```

Buka ulang aplikasinya. Judul tab-nya sekarang "TrackKas". Selesai, 5 menit, dan langsung terlihat.

### b. Pastikan `transactions` bisa diisi (5 menit)

Buka daftar transaksi, tambah satu data. Kalau berhasil, berarti struktur database-nya benar. Kalau muncul error tentang kolom yang tidak ada, jalankan:

```bash
php artisan migrate
```

### c. Yang JANGAN kamu lakukan

- ❌ Menambah fitur edit/update transaksi. Ini terlihat menggoda karena deadline dekat, tapi fitur baru = risiko bug baru. Kalau gagal saat demo, nilainya jatuh. Lebih baik jujur bilang "update belum saya kerjakan" daripada fiturnya ada tapi error.
- ❌ Mengganti struktur database atau nama tabel.
- ❌ Menghapus file yang tidak kamu mengerti.
- ❌ Memperbarui paket lewat `composer update` atau `npm update`. Itu bisa merusak aplikasi yang tadinya jalan.

---

## 8. Yang boleh kamu lewati (jangan buang waktu di sini)

Abaikan semua ini. Kemungkinan kecil ditanya, dan kalau ditanya pun jawaban "belum saya pelajari" itu diterima:

- Halaman verify-email dan confirm-password yang masih tampilan bawaan Breeze
- Sisa file `welcome.blade.php`, `navigation.blade.php`, dan komponen `dropdown` yang tidak dipakai
- Alasan satu migrasi dibuat dua file (sudah ada jawabannya di [Q83](tanya-jawab-sidang.md))
- Cara kerja internal Alpine.js dan Vite
- Seluruh isi `config/` dan `bootstrap/`
- Cara kerja `composer` dan `npm` di balik layar

---

## 9. Kartu anti-panic: empat error paling umum

Kalau aplikasimu bermasalah saat demo, **cek empat hal ini dulu**. Sembilan dari sepuluh masalah berasal dari sini.

| Yang kamu lihat | Penyebabnya | Perbaikannya |
|---|---|---|
| Halaman tampil tapi **tanpa warna dan tanpa tampilan sama sekali** | Aset CSS/JS belum di-build | `npm run build` |
| **"Table not found"** atau kolom tidak ada | Migrasi belum dijalankan | `php artisan migrate` |
| **419 Page Expired** | Token CSRF kedaluwarsa atau session habis | Muat ulang halaman login, lalu login lagi |
| Perubahan tidak terlihat, atau config lama masih dipakai | Cache | `php artisan config:clear` lalu `php artisan view:clear` |

**Lima perintah penyelamat** — hafalkan:

```bash
php artisan config:clear   # bersihkan cache konfigurasi
php artisan view:clear     # bersihkan cache tampilan
php artisan route:clear    # bersihkan cache route
php artisan migrate        # jalankan migrasi
npm run build              # build ulang CSS & JS
```

**Kalau ada error yang tidak kamu mengerti:**

Baca pesan errornya dari **paling atas**, bukan dari tengah. Laravel menampilkan nama file dan nomor barisnya. Buka file itu, lihat barisnya, dan ucapkan:

> "Ada error di baris ini. Sepertinya karena [sebut yang kamu lihat]. Saya coba perbaiki sebentar, Pak/Bu."

Tetap tenang. Menangani error dengan rapi juga dinilai.

---

## 10. Naskah pembukaan & demo

### Naskah pembukaan (45 detik) — hafalkan, ini yang pertama kali dinilai

> "Selamat pagi/siang, Pak/Bu. Saya akan mempresentasikan project saya, **TrackKas** — aplikasi pencatatan keuangan pribadi berbasis Laravel.
>
> Latar belakangnya sederhana: banyak orang tidak tahu ke mana uangnya pergi karena tidak mencatat. TrackKas menyelesaikan itu dengan tiga hal: pengguna bisa mencatat transaksi pemasukan dan pengeluaran, melihat riwayatnya, dan mendapatkan ringkasan otomatis berupa total pemasukan, total pengeluaran, dan saldo.
>
> Fitur yang saya bangun adalah autentikasi, dashboard dengan ringkasan dan rincian per kategori, serta CRUD transaksi. Sistem login-nya memakai Laravel Breeze, paket resmi dari Laravel.
>
> Izinkan saya mulai dengan mendemokan aplikasinya, lalu saya jelaskan struktur kodenya."

Berlatih sampai 45 detik tanpa terbata-bata.

### Urutan demo (7 langkah, ±4 menit)

Ada di [tanya-jawab-sidang.md Bagian 13](tanya-jawab-sidang.md). Latih sampai bisa tanpa melihat catatan:

1. Buka halaman depan → diarahkan ke login
2. Daftar akun baru
3. Dashboard masih kosong → tunjukkan pesan "Belum ada transaksi"
4. Tambah pemasukan Rp 5.000.000
5. Tambah pengeluaran Rp 25.000
6. Kembali ke dashboard → tunjukkan total dan saldo Rp 4.975.000
7. Buka daftar transaksi, coba filter, hapus satu

**Penutup demo yang mengesankan** — jalankan ini di depan penguji:

```bash
php artisan test --filter=TransactionTest
```

> "Ini test otomatis untuk fitur transaksi, termasuk satu test yang membuktikan user tidak bisa menghapus transaksi milik user lain. Totalnya 36 test dan semuanya lulus."

---

## 11. Ringkasan: kalau kamu hanya punya 3 jam lagi

Kerjakan **hanya ini**, berurutan:

1. **30 menit** — jalankan aplikasimu dan pastikan jalan (bagian 6). Ini paling penting.
2. **30 menit** — baca [http-method-get-post-put-patch.md](http-method-get-post-put-patch.md) dan kerjakan 6 latihannya.
3. **30 menit** — baca bagian 4 dokumen ini (crash course Breeze) dan bagian 5 (checklist 10 hal).
4. **30 menit** — latih demo 7 langkah dari awal sampai akhir, dua kali.
5. **30 menit** — hafalkan naskah pembukaan (bagian 10), 10 kalimat kunci di [tanya-jawab-sidang.md Bagian 14](tanya-jawab-sidang.md), dan jawaban "apa bedanya dengan Notepad" di [jawaban-vs-notepad-dan-excel.md](jawaban-vs-notepad-dan-excel.md).
6. **15 menit** — perbaiki `APP_NAME` di `.env` (bagian 7a).
7. **15 menit** — baca kartu anti-panic (bagian 9).

**Selesai. Jangan mengerjakan yang lain.**

---

## Satu hal terakhir

Kamu tidak akan bisa menjelaskan semuanya, dan itu **normal**. Penguji tidak mengharapkan mahasiswa pemula menjelaskan seluruh Laravel. Yang mereka nilai:

1. Kamu **paham alur data** projectmu sendiri.
2. Kamu **jujur** soal apa yang kamu buat dan apa yang berasal dari paket.
3. Kamu **tahu kelemahannya** dan punya rencana untuk memperbaikinya.
4. Kamu **tenang** saat ditanya hal yang tidak kamu tahu.

Keempat hal itu tidak butuh 2 hari untuk dikuasai. Yang dibutuhkan hanya latihan bicara. Jadi tutup dokumen ini sekarang, dan mulai latihan demo pertama.

Semangat. Kamis kamu siap.
