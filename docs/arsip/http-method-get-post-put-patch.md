# GET, POST, PUT, PATCH, DELETE — Pelajaran Lengkap untuk Sidang

Dokumen ini menjelaskan **HTTP method** dari nol, dikaitkan langsung dengan kode TrackKas yang kamu punya.
Target: setelah baca sekali, kamu bisa menjawab pertanyaan jenis ini tanpa gagap.

---

## Bagian 1 — HTTP itu apa

Setiap kali kamu membuka halaman web atau menekan tombol, browser mengirim **request** ke server, dan server membalas dengan **response**.

**Request** punya empat bagian:

| Bagian | Isinya | Contoh |
|---|---|---|
| **Method** | Mau ngapain | `GET`, `POST`, `DELETE` |
| **URL** | Ke alamat mana | `http://localhost:8000/transaksi` |
| **Header** | Info tambahan | cookie session, `Content-Type` |
| **Body** | Data yang dikirim | isi form: `category=Gaji&amount=5000000` |

**Response** punya:

| Bagian | Isinya | Contoh |
|---|---|---|
| **Status code** | Hasilnya apa | `200` berhasil, `302` pindah halaman, `404` tidak ketemu |
| **Header** | Info tambahan | `Location: /transaksi` (untuk pindah halaman) |
| **Body** | Isinya | HTML halaman, atau data JSON |

**Jadi HTTP method itu apa?** Itu **kata kerja** di awal request. Fungsinya memberi tahu server: "permintaan ini maksudnya **membaca**, **membuat**, **mengubah**, atau **menghapus** data." Method-nya satu kata, dan artinya sudah disepakati standar web.

**Kalimat siap pakai:**
> "HTTP method itu kata kerja pada permintaan dari browser ke server. Fungsinya membedakan mana permintaan yang hanya membaca data dan mana yang mengubah data. Laravel memakai perbedaan itu untuk pengamanan dan untuk mengarahkan permintaan ke penanganan yang tepat."

---

## Bagian 2 — Satu per satu, lengkap dengan contohnya di TrackKas

### 1. GET — "ambilkan datanya"

Artinya: **minta sesuatu, tanpa mengubah apa pun.**

```php
Route::get('/transaksi', [TransactionsController::class, 'index'])->name('transaksi.index');
Route::get('/dashboard', DashboardController::class)->name('dashboard');
```

Ciri-ciri:
- Tidak boleh mengubah data. Sama sekali.
- Bisa dibuka berkali-kali, hasilnya sama.
- URL-nya bisa di-bookmark, di-share, di-refresh tanpa akibat apa pun.
- Data yang dikirim ikut di URL sebagai query string: `/transaksi?type=pemasukan`

> **Ini filter-nya di TrackKas.** Waktu kamu klik pill "Pemasukan", browser mengirim `GET /transaksi?type=pemasukan`. Itu **bukan** form, cuma link biasa:
> ```blade
> <a href="{{ route('transaksi.index', ['type' => 'pemasukan']) }}">
> ```
> Karena itu URL-nya bisa kamu kirim ke orang lain dan hasilnya tetap sama. Itu tanda pemakaian GET yang benar.

### 2. POST — "ini data baru, simpan"

Artinya: **kirim data ke server untuk dibuat atau diproses.**

```php
Route::post('/transaksi', [TransactionsController::class, 'store'])->name('transaksi.store');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
```

Ciri-ciri:
- **Mengubah keadaan** server: menyimpan data baru, mengubah session, dsb.
- Datanya ada di **body**, tidak di URL. Ini penting: password tidak pernah muncul di address bar.
- **Bukan idempotent** — artinya dikirim dua kali, hasilnya bisa berbeda. Kirim form transaksi dua kali = **dua baris** di database.
- Karena itu setelah POST selalu diikuti `redirect()`. Pola ini namanya **PRG (Post–Redirect–Get)**.

> **Ini pertanyaan favorit penguji.** "Kalau user menekan refresh setelah menyimpan, datanya jadi dobel nggak?"
> Jawab: **"Tidak, karena setelah `store()` saya mengembalikan `redirect()`, bukan `view()`. Jadi halaman berikutnya adalah hasil GET yang bersih. Kalau saya tampilkan view langsung, request terakhir di browser masih POST, dan refresh akan mengirim ulang formnya."**

### 3. PUT — "ganti seluruhnya"

Artinya: **timpa data yang ada dengan data baru yang lengkap.**

```php
Route::put('password', [PasswordController::class, 'update'])->name('password.update');
```

Ciri-ciri:
- Mengganti **seluruh** isi data, bukan sebagian.
- **Idempotent** — dikirim sepuluh kali, hasil akhirnya sama. Karena "ganti seluruhnya" tidak menumpuk.
- Di TrackKas dipakai untuk **ganti password**: nilainya diganti utuh, tidak ada parsialnya.

### 4. PATCH — "ubah sebagian saja"

Artinya: **ubah field tertentu saja, sisanya biarkan.**

```php
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
```

Ciri-ciri:
- Mengubah **sebagian** field yang dikirim, field lain tetap.
- Juga idempotent.
- Di TrackKas dipakai untuk **edit profil**: user mungkin mengubah nama saja dan email-nya dibiarkan.

### 🔴 Pertanyaan penguji: "Kenapa PUT dan PATCH dipakai barengan? Apa bedanya?"

Ini pertanyaan bagus dan kamu punya jawaban yang kuat karena projectmu memang memakai keduanya:

> "Bedanya di **cakupan** perubahannya. **PUT** artinya mengganti nilai secara utuh — di TrackKas dipakai untuk password, karena password memang diganti seluruhnya, tidak ada istilah 'ubah sebagian password'. **PATCH** artinya mengubah sebagian field saja — di TrackKas dipakai untuk edit profil, karena user bisa mengubah nama tanpa menyentuh email.
> Kalau ditanya apakah wajib beda: tidak, Pak/Bu. Secara teknis keduanya jalan, dan banyak aplikasi memakai PATCH untuk semua update. Breeze di project ini memilih memisahkannya sesuai makna perubahannya."

⚠️ Kalau penguji bilang "biasanya orang cuma pakai PUT": jangan berdebat, jawab — *"Betul, dan itu juga sah. Saya mengikuti paket starter yang memisahkannya."*

### 5. DELETE — "hapus"

Artinya: **hapus data.**

```php
Route::delete('/transaksi/{transaction}', [TransactionsController::class, 'destroy'])->name('transaksi.destroy');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
```

Ciri-ciri:
- Mengubah keadaan server, dan **sulit dibatalkan**.
- Harus selalu dilindungi CSRF.
- Di TrackKas dipakai untuk hapus transaksi dan hapus akun.

### Bonus: HEAD dan OPTIONS

> **HEAD** — sama seperti GET, tapi server hanya mengembalikan header tanpa body. Dipakai untuk mengecek apakah halaman ada atau untuk mengecek ukuran file. Di `route:list` kamu akan melihat `GET|HEAD` berdampingan, itu sebabnya.
> **OPTIONS** — menanyakan method apa saja yang didukung sebuah URL.

---

## Bagian 3 — Tabel "aman" dan "idempotent"

Dua istilah ini sering ditanya. Hafalkan tabel ini.

| Method | Aman? (tidak mengubah data) | Idempotent? (diulang hasilnya sama) | Fungsinya |
|---|---|---|---|
| **GET** | ✅ Ya | ✅ Ya | Membaca data |
| **POST** | ❌ Tidak | ❌ Tidak | Membuat data baru |
| **PUT** | ❌ Tidak | ✅ Ya | Mengganti seluruh data |
| **PATCH** | ❌ Tidak | ✅ Ya | Mengubah sebagian data |
| **DELETE** | ❌ Tidak | ✅ Ya | Menghapus data |

**Cara menjelaskan istilahnya:**
> "**Aman** artinya method itu tidak mengubah data apa pun di server, jadi boleh dipanggil siapa saja kapan saja. **Idempotent** artinya kalau permintaan yang sama dikirim berulang kali, hasil akhirnya tetap sama. Hapus data itu idempotent — dihapus sekali atau sepuluh kali, datanya tetap terhapus. Tapi POST tidak idempotent — kirim form dua kali, datanya jadi dua baris."

---

## Bagian 4 — Kenapa hapus TIDAK boleh pakai GET

Ini pertanyaan yang hampir pasti muncul, dan kamu punya pengalaman langsung untuk menjawabnya.

⚠️ **Di project latihanmu yang lama, kodenya seperti ini:**

```php
// routes/web.php (komunitas_laravel)
Route::get('users/{id}/destroy', [UserController::class, 'destroy']);
```

```blade
<!-- users/index.blade.php -->
<a href="/users/{{ $u->id }}/destroy">Hapus</a>
```

**Itu bermasalah, dan ada tiga alasannya:**

1. **Bisa terhapus tanpa sengaja.** Browser, mesin pencari, atau aplikasi chat yang memuat pratinjau link, bisa "mengunjungi" URL itu. Kalau dikunjungi, datanya hilang. Tidak ada tombol, tidak ada klik, tidak ada niat.
2. **Tidak ada perlindungan CSRF.** Situs lain bisa menyisipkan `<img src="http://situsmu.com/users/5/destroy">`, dan karena kamu sedang login, browser akan mengirim cookie session-mu, dan datanya terhapus. Ini yang disebut serangan CSRF.
3. **Tombol back jadi mengerikan.** Setelah menghapus, user menekan tombol back, browser memuat ulang URL hapus itu, dan menghapus data berikutnya.

**Cara memperbaikinya — dan ini yang sudah kamu lakukan di TrackKas:**

```php
// routes/web.php (TrackKas) — metodnya DELETE
Route::delete('/transaksi/{transaction}', [TransactionsController::class, 'destroy'])->name('transaksi.destroy');
```

```blade
<!-- transaksi/index.blade.php -->
<form method="POST" action="{{ route('transaksi.destroy', $transaction) }}"
      onsubmit="return confirm('Hapus transaksi ini?')">
    @csrf
    @method('DELETE')

    <button type="submit">Hapus</button>
</form>
```

**Kalimat siap pakai:**
> "Di project latihan saya sebelumnya, hapus masih memakai link GET. Saya baru sadar itu salah setelah mempelajari HTTP method. Di TrackKas saya perbaiki: hapus sekarang memakai form dengan method DELETE dan token CSRF, ditambah konfirmasi sebelum mengirim. Alasannya, GET seharusnya hanya untuk membaca data, sehingga tidak bisa terpicu tanpa sengaja."

---

## Bagian 5 — Masalahnya: HTML form cuma bisa GET dan POST

Ini bagian yang paling sering bikin bingung, dan jawabannya akan membuat penguji mengangguk.

**Faktanya, menurut spesifikasi HTML, tag `<form>` hanya mendukung dua method:**

```html
<form method="GET">   ✅ boleh
<form method="POST">  ✅ boleh
<form method="PUT">   ❌ tidak dikenali browser, akan dianggap GET
<form method="DELETE">❌ tidak dikenali browser, akan dianggap GET
```

**Tapi HTTP dan Laravel mendukung lima method.** Jadi bagaimana caranya mengirim DELETE dari sebuah form?

### Jawabannya: method spoofing (pemalsuan method yang disepakati)

Caranya, form tetap dikirim dengan **POST**, tapi di dalamnya ditaruh satu input tersembunyi bernama `_method` yang berisi method yang sebenarnya diinginkan.

```blade
<form method="POST" action="{{ route('transaksi.destroy', $transaction) }}">
    @csrf
    @method('DELETE')
    <button type="submit">Hapus</button>
</form>
```

Yang benar-benar terkirim ke server:

```
POST /transaksi/7
Body: _token=abc123...&_method=DELETE
```

Lalu Laravel membaca `_method=DELETE` dan menganggap request itu **DELETE**, sehingga cocok dengan `Route::delete(...)`.

**Ada dua cara menulisnya, fungsinya sama persis:**

| Cara baru (Blade directive) | Cara lama (helper) |
|---|---|
| `@method('DELETE')` | `{{ method_field('DELETE') }}` |
| `@csrf` | `{{ csrf_field() }}` |

> Cara lama di kolom kanan itu **yang kamu pakai di project latihanmu**. Jadi kamu sudah kenal dua-duanya — tinggal sebutkan itu saat sidang, dan nilai plusnya langsung terasa.

**Kalimat siap pakai:**
> "HTML form secara standar hanya mendukung GET dan POST. Jadi untuk mengirim DELETE atau PATCH, saya tetap memakai POST tapi menambahkan `@method('DELETE')`. Direktif itu mencetak input tersembunyi `_method`, dan Laravel membacanya sehingga request diperlakukan sebagai DELETE. Teknik ini namanya method spoofing."

### Bukti nyata di projectmu — tiga contohnya sudah ada

| File | Isinya | Route tujuan | Method sebenarnya |
|---|---|---|---|
| `resources/views/transaksi/index.blade.php` | `@method('DELETE')` | `transaksi.destroy` | **DELETE** |
| `resources/views/profile/partials/update-profile-information-form.blade.php` | `@method('patch')` | `profile.update` | **PATCH** |
| `resources/views/profile/partials/update-password-form.blade.php` | `@method('put')` | `password.update` | **PUT** |
| `resources/views/profile/partials/delete-user-form.blade.php` | `@method('delete')` | `profile.destroy` | **DELETE** |

Buka file-file itu saat sidang. Menunjukkan bukti lebih kuat daripada menjelaskan teori.

---

## Bagian 6 — Pemetaan lengkap: semua route projectmu

Ini hasil `php artisan route:list` di projectmu. Hafalkan yang berhubungan dengan transaksi dan auth.

| Method | URL | Route | Penanganannya | Fungsinya |
|---|---|---|---|---|
| `GET` | `/` | — | closure di `web.php` | Arahkan ke login atau dashboard |
| `GET` | `/dashboard` | `dashboard` | `DashboardController` | Tampilkan ringkasan |
| `GET` | `/transaksi` | `transaksi.index` | `TransactionsController@index` | **Read** — daftar transaksi |
| `GET` | `/transaksi/create` | `transaksi.create` | `TransactionsController@create` | Tampilkan form tambah |
| `POST` | `/transaksi` | `transaksi.store` | `TransactionsController@store` | **Create** — simpan transaksi |
| `DELETE` | `/transaksi/{transaction}` | `transaksi.destroy` | `TransactionsController@destroy` | **Delete** — hapus transaksi |
| `GET` | `/login` | `login` | `AuthenticatedSessionController@create` | Tampilkan form login |
| `POST` | `/login` | — | `AuthenticatedSessionController@store` | Proses login |
| `POST` | `/logout` | `logout` | `AuthenticatedSessionController@destroy` | Proses logout |
| `GET` | `/register` | `register` | `RegisteredUserController@create` | Tampilkan form daftar |
| `POST` | `/register` | — | `RegisteredUserController@store` | Proses daftar |
| `GET` | `/profile` | `profile.edit` | `ProfileController@edit` | Tampilkan halaman profil |
| `PATCH` | `/profile` | `profile.update` | `ProfileController@update` | Ubah data profil |
| `PUT` | `/password` | `password.update` | `PasswordController@update` | Ganti password |
| `DELETE` | `/profile` | `profile.destroy` | `ProfileController@destroy` | Hapus akun |

**Perhatikan pola yang muncul:** kamu punya tiga URL yang sama (`/transaksi`, `/profile`) tapi dengan method berbeda, dan Laravel mengarahkannya ke method controller yang berbeda. Itu sebabnya method itu penting — **method + URL, itu yang menentukan tujuan request**, bukan URL-nya saja.

---

## Bagian 7 — Hubungan CRUD, HTTP method, dan SQL

Penguji suka tabel ini. Bisa langsung kamu gambar di papan tulis.

| CRUD | HTTP method | Method controller | Perintah SQL-nya | Ada di TrackKas? |
|---|---|---|---|---|
| **C**reate | `POST` | `store()` | `INSERT INTO transactions ...` | ✅ `transaksi.store` |
| **R**ead | `GET` | `index()` | `SELECT * FROM transactions ...` | ✅ `transaksi.index` |
| **U**pdate | `PUT` / `PATCH` | `update()` | `UPDATE transactions SET ... WHERE id = ...` | ❌ belum ada untuk transaksi |
| **D**elete | `DELETE` | `destroy()` | `DELETE FROM transactions WHERE id = ...` | ✅ `transaksi.destroy` |

**Kalimat siap pakai:**
> "CRUD saya petakan langsung ke HTTP method: Create ke POST, Read ke GET, Update ke PUT atau PATCH, Delete ke DELETE. Nama method controller-nya juga mengikuti konvensi itu: `store` untuk simpan, `index` untuk daftar, `update` untuk ubah, `destroy` untuk hapus. Itu konvensi Laravel, jadi pembaca lain langsung tahu isi method-nya."

⚠️ **Perhatikan: kolom Update untuk transaksi isinya ❌.** Itu memang belum ada di projectmu. Kalau penguji menunjuk itu, jawab jujur:
> "Betul, Pak/Bu, edit transaksi belum saya kerjakan. Kalau ditambahkan, bentuknya: satu method `edit` untuk menampilkan form berisi data lama, satu method `update` untuk menyimpannya, plus satu route `PATCH /transaksi/{transaction}` dan satu tombol Edit di daftar."

---

## Bagian 8 — Bonus: status code yang perlu kamu tahu

| Kode | Artinya | Kapan muncul di projectmu |
|---|---|---|
| `200` | OK, berhasil | Halaman tampil normal |
| `302` | Pindah halaman (redirect) | Setelah simpan atau hapus transaksi |
| `404` | Tidak ditemukan | Hapus transaksi milik orang lain |
| `419` | Token CSRF tidak valid | Session habis, form lama dikirim |
| `422` | Data tidak lolos validasi | Form transaksi dikirim kosong |
| `500` | Error di server | Ada bug di kode |

> "Kalau validasi gagal, Laravel mengembalikan 302 balik ke form sambil menaruh pesan error di session — bukan halaman 422, kecuali kalau yang meminta adalah API yang mengharapkan JSON."

⚠️ Kalau kamu tidak yakin, cukup bilang: *"Saya hafal beberapa yang utama: 200 berhasil, 302 redirect, 404 tidak ketemu, 419 masalah token CSRF, dan 500 error server."*

---

## Bagian 9 — Latihan: uji dirimu sendiri

Tutup dokumen ini. Jawab enam pertanyaan ini. Kalau bisa semua, bagian HTTP method sudah aman.

1. Method apa yang dipakai untuk **menampilkan** daftar transaksi? Kenapa tidak boleh yang lain?
2. Method apa yang dipakai untuk **menyimpan** transaksi? Kenapa setelah menyimpan harus `redirect()` dan bukan langsung tampilkan view?
3. Kenapa **hapus** tidak boleh pakai link `<a href>`?
4. HTML form cuma bisa GET dan POST. Lalu, **bagaimana caranya mengirim DELETE?**
5. Apa bedanya **PUT** dan **PATCH**? Beri contoh masing-masing dari projectmu.
6. Apa arti **idempotent**? Sebutkan method yang idempotent dan yang tidak.

<details>
<summary>Klik untuk melihat kunci jawaban</summary>

1. **GET.** Karena hanya membaca data, tidak mengubah apa pun, jadi aman di-refresh, di-bookmark, dan di-share.
2. **POST.** Karena POST tidak idempotent — kalau halaman tetap POST dan user menekan refresh, browser mengirim ulang form dan datanya jadi dobel. Redirect membuat halaman berikutnya jadi GET yang bersih.
3. Karena link bisa dikunjungi browser atau crawler tanpa sengaja, tidak terlindungi CSRF, dan tombol back bisa mengulang penghapusan.
4. Pakai POST ditambah input tersembunyi `_method=DELETE`, yang dicetak oleh `@method('DELETE')`. Teknik ini disebut method spoofing.
5. **PUT** mengganti nilai secara utuh — contohnya ganti password di `password.update`. **PATCH** mengubah sebagian field — contohnya edit profil di `profile.update`.
6. Idempotent = dikirim berulang kali, hasil akhirnya tetap sama. Yang idempotent: GET, PUT, PATCH, DELETE. Yang tidak: POST.

</details>

---

## Bagian 10 — Ringkasan satu layar

| Method | Arti | Ada di TrackKas untuk |
|---|---|---|
| `GET` | Baca data | Daftar transaksi, dashboard, form login |
| `POST` | Buat data baru | Simpan transaksi, login, register, logout |
| `PUT` | Ganti seluruhnya | Ganti password |
| `PATCH` | Ubah sebagian | Edit profil |
| `DELETE` | Hapus | Hapus transaksi, hapus akun |

**Empat kalimat yang harus bisa kamu ucapkan tanpa berpikir:**

1. "GET untuk membaca, POST untuk membuat, PUT untuk mengganti utuh, PATCH untuk mengubah sebagian, DELETE untuk menghapus."
2. "Hapus saya taruh di form dengan method DELETE dan CSRF, bukan di link, karena GET tidak boleh mengubah data."
3. "HTML form hanya mendukung GET dan POST, jadi untuk DELETE dan PATCH saya pakai `@method()` — itu yang disebut method spoofing."
4. "Method dan URL bersama-sama menentukan tujuan request. URL `/transaksi` yang sama bisa punya tiga method berbeda dan tiga penanganan berbeda."
