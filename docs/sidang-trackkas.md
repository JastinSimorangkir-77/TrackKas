# SIDANG TRACKKAS — Hafalan Cepat

> Baca 10 menit sebelum masuk. Semua jawaban di bawah **1–3 kalimat**, tinggal diucapkan.

---

## 0. TIGA KALIMAT PENYANGGA (hafal kata per kata)

1. **Kalau ditanya hal yang tidak kau kuasai:**
   > "Untuk bagian itu saya belum sampai ke detailnya, Pak/Bu. Yang saya pahami adalah alurnya dan file mana yang menanganinya."

2. **Kalau ditanya isi Breeze:**
   > "Breeze saya pakai sebagai paket starter resmi, jadi bagian internalnya tidak saya tulis sendiri. Saya memahami alurnya, dan untuk detail keamanan saya mempercayakannya pada paket resmi Laravel."

3. **Kalau ditanya kenapa ada kekurangan:**
   > "Betul, itu memang belum saya kerjakan. Rencana perbaikannya sudah saya pikirkan, yaitu ..."

**Jangan pernah mengarang.** Mengaku + sebut rencana perbaikan jauh lebih bernilai daripada jawaban palsu.

---

## 1. ALUR 1 REQUEST (paling sering ditanya)

```
1. User isi form   →  resources/views/transaksi/partials/form.blade.php
2. Klik Simpan     →  POST /transaksi            (routes/web.php)
3. Middleware      →  auth + verified  (cek sudah login & email terverifikasi)
4. Masuk controller→  TransactionsController@store
5. Validasi        →  $request->validate([...])
6. Simpan ke DB    →  $request->user()->transactions()->create($data)
7. Redirect        →  redirect()->route('transaksi.index')->with('status', ...)
```

Kalimat kuncinya: **"Request itu masuk lewat route, diproses controller, datanya lewat model, tampilannya di Blade."** Itu MVC.

---

## 2. ISTILAH INTI (1 kalimat masing-masing)

| Istilah | Jawaban singkat |
|---|---|
| **MVC** | Model = data, View = tampilan, Controller = penghubung keduanya. |
| **Route** | Daftar alamat URL dan siapa yang menanganinya. |
| **Controller** | Tempat logika: terima request, validasi, simpan, kembalikan tampilan. |
| **Model** | Wakil satu tabel di database. Satu baris tabel = satu object model. |
| **Eloquent** | Cara Laravel bicara ke database pakai object, bukan tulis SQL manual. |
| **Migration** | File PHP yang membuat/mengubah struktur tabel, jadi bisa diulang di komputer lain. |
| **Blade** | Template HTML milik Laravel. `{{ }}` untuk cetak data. |
| **Middleware** | Pemeriksa di depan route. Contoh: `auth` = harus login. |
| **Composer** | Pemasang library PHP (folder `vendor`). |
| **NPM / Vite** | Pemasang library tampilan (Tailwind, Alpine) dan pengubahnya jadi file siap pakai. |
| **Tailwind** | CSS yang ditulis lewat class di HTML, bukan file CSS terpisah. |
| **Alpine.js** | JavaScript kecil yang ditulis langsung di atribut HTML. |
| **Seeder / Factory** | Pembuat data palsu untuk uji coba dan testing. |

Kalau ditanya **"apa itu Eloquent?"** → *"Cara Laravel berkomunikasi dengan database menggunakan object PHP, jadi saya tidak perlu menulis query SQL sendiri."*

---

## 3. HTTP METHOD (kau sudah pakai kelimanya)

| Method | Arti | Dipakai di |
|---|---|---|
| **GET** | Ambil / tampilkan data | `GET /dashboard`, `GET /transaksi` |
| **POST** | Kirim data baru | `POST /transaksi` (tambah), `POST /login` |
| **PUT** | Ganti seluruh data | `PUT /password` (ganti password) |
| **PATCH** | Ubah sebagian data | `PATCH /profile` (edit profil) |
| **DELETE** | Hapus data | `DELETE /transaksi/{id}`, `DELETE /profile` |

**Dua hal yang wajib kau tahu:**

1. **HTML form cuma bisa GET dan POST.** Supaya bisa DELETE/PUT/PATCH, Laravel memakai *method spoofing*:
   ```blade
   <form method="POST" action="{{ route('transaksi.destroy', $transaction) }}">
       @csrf
       @method('DELETE')   ← ini yang mengubahnya jadi DELETE
   ```
   Kalau ditanya *"kok bisa DELETE, kan form gak bisa?"*, jawab: *"Saya pakai `@method('DELETE')`. Laravel menyisipkan field tersembunyi `_method`, lalu membacanya sebagai DELETE."*

2. **Kenapa hapus tidak pakai link?** → *"Link itu selalu GET, dan GET tidak boleh mengubah data. Kalau hapus lewat link, halaman bisa terhapus hanya karena dibuka atau ter-prefetch. Karena itu saya pakai form + `@method('DELETE')`."* (Ini bug nyata di project `komunitas_laravel`-mu — akui, jangan elak.)

**PUT vs PATCH:** *"PUT untuk mengganti seluruh data, PATCH untuk mengubah sebagian. Di project saya, ganti password pakai PUT dan edit profil pakai PATCH."*

---

## 4. "KALAU DITANYA X → JAWAB Y"

| Pertanyaan | Jawaban |
|---|---|
| Apa itu TrackKas? | Aplikasi pencatatan keuangan pribadi: catat pemasukan & pengeluaran, lihat ringkasan saldo. |
| Kenapa pakai Laravel? | Sudah menyediakan routing, validasi, database, dan autentikasi, jadi saya tidak menulis semuanya dari nol. |
| Kenapa ada 2 migrasi untuk tabel `transactions`? | Jujur saja: awalnya saya buat tabel kerangka, lalu saya tambah kolomnya di migrasi baru supaya migrasi lama tidak perlu saya rusak. (Lihat §6 no. 1) |
| Kenapa `User::all()` salah di sini? | Itu mengambil transaksi **semua orang**. Saya pakai `transactions()` milik user yang login, jadi setiap user hanya melihat datanya sendiri. |
| Kenapa validasi di controller, bukan di form? | Validasi di HTML hanya untuk kenyamanan. Bisa dimatikan lewat Inspect Element, jadi validasi wajib tetap di server. |
| Apa itu `$request->validate()`? | Memeriksa data yang masuk; kalau gagal, otomatis balik ke form + menampilkan pesan error. |
| Kenapa `transactions()` pakai kurung? | Dengan kurung artinya *query* (belum dijalankan), jadi masih bisa saya tambah `where` dan `sum`. Tanpa kurung, datanya langsung diambil semua. |
| Apa itu `findOrFail`? | Mencari data; kalau tidak ada, otomatis 404. |
| Kalau saya hapus transaksi orang lain lewat URL? | Tidak bisa. Saya cari datanya dari relasi milik user yang login, jadi data orang lain tidak ditemukan dan hasilnya 404. |
| Kenapa angka Rp 5.000 muncul tiap kali buka dashboard? | Angka total **tidak saya simpan** di database. Saya hitung ulang dari datanya tiap halaman dibuka. |
| Apa itu `hasMany` / `belongsTo`? | Relasi antar tabel. Satu user punya banyak transaksi (`hasMany`), satu transaksi milik satu user (`belongsTo`). |
| Kenapa `@csrf` di dalam form? | Mencegah serangan CSRF — permintaan dari situs lain yang mengaku-aku sebagai user yang sedang login. |
| Kenapa pakai `<x-input-label>` dan bukan `<label>` biasa? | Itu Blade component, jadi tulisan HTML-nya saya tulis sekali dan dipakai berulang. |
| Kenapa class-nya panjang (`rounded-xl`, `px-4`)? | Itu Tailwind: satu class mewakili satu aturan CSS. Jadi saya tidak perlu menulis file CSS terpisah. |
| Apa fungsi `@vite`? | Memuat hasil build CSS & JavaScript ke halaman. |
| Kenapa `@checked` / `old()`? | Supaya isi form tidak hilang saat validasi gagal. |
| Apa bedanya `auth` dan `guest`? | `auth` = hanya boleh diakses setelah login. `guest` = hanya untuk yang belum login (halaman login/register). |
| Kenapa flash message hilang sendiri? | Disimpan di session untuk **satu tampilan berikutnya** saja. |
| Ada testing? | Ada. Menjalankan `php artisan test` untuk memastikan alur tambah, hapus, filter, dan dashboard tetap benar. |
| Kalau user ganti password? | Ditangani `Auth\PasswordController` bawaan Breeze, lewat `PUT /password`. |

---

## 5. JAWABAN ANDALAN: "BEDANYA SAMA NOTEPAD?"

**Akui dulu, baru bedakan satu hal:**

> "Betul, untuk sekadar mencatat, Notepad memang cukup. Bedanya di **setelah** mencatat. Di Notepad, total itu tulisan yang saya ketik sendiri — begitu saya tambah satu baris, angkanya jadi basi dan harus saya hitung ulang manual. Di TrackKas, total **tidak saya simpan di database sama sekali**; angkanya hasil hitungan dari data, dihitung ulang setiap dashboard dibuka. Jadi tidak mungkin basi."

Poin intinya: **di Notepad total adalah data, di TrackKas total adalah hasil hitungan.**

**Kalau ditanya "kenapa bukan Excel?"** — jangan bilang Excel tidak bisa menghitung.
> "Excel bisa menghitung. Tapi di Excel, aturannya ada di dalam file: rumusnya bisa terhapus atau bergeser. Di TrackKas, aturannya ada di validasi dan database, jadi datanya tidak bisa masuk kalau tidak sah."

**Akuin sendiri sebelum ditanya:** kolom `category` masih teks bebas, jadi "Makanan" dan "makanan" dianggap dua kategori berbeda. Rencana perbaikannya: kategori dijadikan tabel sendiri dan form-nya jadi dropdown. Mengakui ini duluan justru membuatmu terlihat paham.

---

## 6. ENAM JEBAKAN (siapkan jawabannya)

1. **Dua migrasi, satu tabel.** Jangan mengarang alasan teknis. Jawab: *"Itu karena saya kembangkan bertahap — awalnya kerangka, lalu kolomnya saya tambah di migrasi baru. Di project nyata sebaiknya satu migrasi, atau migrasi baru kalau sudah berjalan di server."*
2. **`{transaction}` di route tapi controllernya `string $id`.** → *"Saya sengaja cari manual pakai `findOrFail` dari relasi user yang login, supaya kalau ID-nya milik orang lain hasilnya 404, bukan malah tampil."*
3. **Belum ada Edit/Update transaksi.** → *"Betul, yang ada baru tambah, lihat, filter, dan hapus. Rencananya menambah form edit dengan data yang sudah terisi."*
4. **`$request->all()` berbahaya.** → *"Karena user bisa mengirim kolom tambahan yang tidak saya inginkan. Karena itu saya sebutkan kolomnya satu per satu lewat `validate()`."*
5. **Judul tab browser masih "Laravel".** Ini celah kecil yang terlihat besar. **Perbaiki H-1**: ubah `APP_NAME` di `.env` jadi `TrackKas`, lalu `php artisan config:clear`.
6. **Breeze bukan tulisan sendiri.** Pakai kalimat penyangga no. 2 di §0.

---

## 7. DEMO 5 LANGKAH (buka aplikasi, jangan cuma bicara)

1. Buka `/login` → tunjukkan halaman login **TrackKas** (bukan halaman Laravel default).
2. Login → sampai di dashboard, tunjukkan saldo dan transaksi terbaru.
3. Tambah transaksi Rp 100.000 → tunjukkan pesan "Transaksi berhasil ditambahkan."
4. Buka dashboard → tunjukkan totalnya **berubah sendiri**. Ini pembuktian poin Notepad.
5. Hapus transaksi itu → tunjukkan angka kembali seperti semula.

---

## 8. CHECKLIST TERAKHIR (hari Kamis pagi)

```bash
php artisan migrate        # pasang struktur tabel terbaru
npm run build              # bangun CSS & JS
php artisan config:clear   # bersihkan cache config
php artisan test           # pastikan semua test lulus
php artisan serve          # jalankan aplikasinya
```

Lalu **coba sendiri dari login sampai hapus transaksi.** Jangan pernah masuk ruang sidang dengan aplikasi yang belum kau hidupkan hari itu.

---

## SATU HAL YANG PALING MENOLONG

> Kalau ada pertanyaan yang kau tidak tahu, jangan diam dan jangan mengarang. Buka file-nya, baca, lalu jawab dari apa yang kaubaca. Nilainya jauh lebih tinggi daripada menghafal — karena penguji sedang menilai **cara berpikirmu**, bukan hafalanmu.
