# Tanya–Jawab Sidang Project TrackKas

Dokumen ini berisi **162 pertanyaan inti** plus **21 pertanyaan jebakan** yang paling mungkin muncul saat project TrackKas disidangkan, lengkap dengan cara menjawabnya. Total **183 pertanyaan**.

**Cara pakai:**
1. Baca [peta-belajar-trackkas.md](peta-belajar-trackkas.md) dulu supaya kenal kodenya.
2. Jangan hafal kata per kata. Hafalkan **isi kepala setiap jawaban**, lalu ucapkan dengan bahasa sendiri.
3. Tanda 🔴 = pertanyaan yang hampir pasti ditanya. Prioritaskan itu.
4. Tanda ⚠️ = pertanyaan jebakan. Penguji biasanya sudah tahu jawabannya dan sedang menguji kejujuranmu.

**Aturan emas saat sidang:**
- Kalau tidak tahu: *"Untuk bagian itu saya belum sempat pelajari lebih dalam, Pak/Bu. Yang saya pahami adalah ... (sebut yang kamu tahu). Kalau diizinkan, saya jelaskan rencana saya untuk mempelajarinya."*
- Jangan pernah mengarang istilah. Lebih baik bilang "saya belum tahu" daripada salah sebut.
- Selalu buka dengan alasan **kenapa**, bukan **apa**. Penguji menilai penalaran.

---

# BAGIAN 0 — Pembuka & Gambaran Umum (Q1–Q10)

### 🔴 Q1. Coba jelaskan project kamu secara singkat.

> "TrackKas adalah aplikasi pencatatan keuangan pribadi berbasis web. User bisa mendaftar dan login, lalu mencatat transaksi pemasukan dan pengeluaran. Setiap transaksi punya tanggal, kategori, jumlah, dan catatan opsional. Dari data itu, dashboard menghitung total pemasukan, total pengeluaran, saldo, dan rincian per kategori. Fitur yang saya bangun: autentikasi, dashboard, tambah transaksi, daftar transaksi, filter jenis, dan hapus transaksi."

Target 30–45 detik. Jangan lebih.

### 🔴 Q2. Kenapa kamu pilih tema keuangan pribadi?

> "Karena masalahnya nyata dan dekat: banyak orang kesulitan tahu uangnya habis ke mana. Fiturnya juga cukup untuk menunjukkan satu siklus CRUD penuh — tambah data, simpan ke database, tampilkan kembali, olah jadi ringkasan — tanpa membuat scope-nya terlalu besar untuk ukuran project pemula."

### 🔴 Q3. Pakai framework apa, dan kenapa?

> "Laravel versi 13 dengan PHP 8.3. Alasan saya pakai framework: Laravel sudah menyediakan routing, autentikasi, validasi, dan ORM (Eloquent) yang siap dipakai. Kalau saya tulis dari PHP murni, saya harus membuat sendiri semua bagian itu, dan risikonya lebih besar untuk celah keamanan."

### 🔴 Q4. Arsitektur yang kamu pakai apa? Jelaskan pola MVC-nya.

> "MVC — Model, View, Controller. **Model** mewakili tabel database, di project saya `User` dan `Transaction`. **View** adalah tampilan Blade di folder `resources/views`. **Controller** menjembatani keduanya: menerima request, memproses lewat model, lalu mengembalikan view. Contohnya `TransactionsController@index`: ambil transaksi milik user yang login lewat model, lalu kirim ke view `transaksi.index`."

### Q5. Apa bedanya route, controller, dan view?

> "Route itu daftar alamat URL dan siapa penanggung jawabnya. Controller itu ruang kerjanya — tempat logikanya. View itu hasil tampilannya. Analoginya: route itu nomor meja, controller itu pelayan, view itu makanan yang disajikan."

### 🔴 Q6. Coba tunjukkan struktur folder projectnya.

Buka VS Code, tunjuk berturut-turut: `app/Http/Controllers`, `app/Models`, `routes/web.php`, `database/migrations`, `resources/views`, `resources/css`, `resources/js`. Sebutkan perannya masing-masing satu kalimat (lihat bagian 4 di peta belajar).

### Q7. Kenapa ada dua layout, `app` dan `guest`?

> "`guest` dipakai untuk halaman saat user belum login — login, register, lupa password. Bentuknya satu kartu putih di tengah halaman. `app` dipakai untuk halaman setelah login, karena di situ ada sidebar navigasi. Pemisahannya supaya halaman login tidak ikut memuat sidebar."

### 🔴 Q8. Halaman pertama yang muncul saat aplikasi dibuka apa?

> "Login. Di `routes/web.php` saya bikin route `/` yang memeriksa status login: kalau belum login diarahkan ke route `login`, kalau sudah login diarahkan ke route `dashboard`."

```php
Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});
```

### Q9. Fitur apa saja yang sudah selesai, dan apa yang belum?

> "Yang selesai: register, login, logout, halaman profil, dashboard dengan ringkasan dan grafik bar sederhana per kategori, daftar transaksi dengan filter jenis, tambah transaksi, dan hapus transaksi dengan konfirmasi.
> Yang belum: edit/update transaksi, fitur pencarian, pembagian halaman (pagination), dan ekspor laporan."

Jujur. Penguji lebih menghargai daftar yang jelas daripada klaim "semua selesai".

### Q10. Kalau diberi waktu satu minggu lagi, kamu tambahkan apa?

> "Pertama, edit transaksi — itu melengkapi CRUD-nya. Kedua, pagination di daftar transaksi supaya kalau datanya sudah ribuan tidak berat. Ketiga, filter rentang tanggal dan ekspor ke CSV atau PDF untuk laporan bulanan."

---

# BAGIAN 1 — Routing (Q11–Q24)

### 🔴 Q11. Tunjukkan isi `routes/web.php` dan jelaskan.

```php
Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/transaksi', [TransactionsController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/create', [TransactionsController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi', [TransactionsController::class, 'store'])->name('transaksi.store');
    Route::delete('/transaksi/{transaction}', [TransactionsController::class, 'destroy'])->name('transaksi.destroy');
});
```

> "Ada tiga bagian. Pertama, route `/` yang mengarahkan tamu ke login dan user yang sudah login ke dashboard. Kedua, grup bertanda middleware `auth` dan `verified` — semua di dalamnya hanya bisa diakses user yang sudah login. Ketiga, grup middleware `auth` untuk halaman profil. Di baris paling bawah ada `require __DIR__.'/auth.php'` yang memuat route login dan register bawaan Breeze."

### 🔴 Q12. Kenapa route transaksi dimasukkan ke dalam `group`?

> "Supaya middleware-nya tidak perlu ditulis ulang di setiap baris. Satu `group` dengan `middleware(['auth', 'verified'])` membuat semua route di dalamnya otomatis terproteksi. Lebih rapi dan kalau nanti aturannya berubah, saya hanya ubah satu tempat."

### Q13. Apa fungsi `->name('transaksi.index')`?

> "Memberi nama pada route, supaya di view saya bisa memanggil `route('transaksi.index')` alih-alih menulis `/transaksi` manual. Keuntungannya: kalau URL-nya nanti saya ubah, semua link di seluruh aplikasi ikut berubah otomatis tanpa saya sentuh satu per satu."

### 🔴 Q14. Kenapa `<a href="/transaksi">` tidak dipakai?

> "Untuk link navigasi biasa seperti menu, sebenarnya boleh. Tapi di TrackKas saya konsisten pakai `route()` supaya kalau URL berubah tidak ada link yang rusak. Yang **tidak boleh** pakai `<a>` adalah aksi yang mengubah data seperti hapus — itu harus lewat form."

### 🔴 Q15. Kenapa hapus transaksi pakai method `DELETE`, bukan `GET`?

> "Karena `GET` seharusnya hanya untuk membaca data. Kalau hapus pakai `GET`, ada tiga masalah: pertama, browser dan mesin pencari bisa mengunjungi link itu tanpa sengaja dan menghapus data; kedua, tidak ada perlindungan CSRF; ketiga, kalau user menekan tombol back, request bisa terulang. Jadi aksi destruktif saya taruh di method `DELETE` di dalam form yang dilindungi CSRF."

⚠️ Catat: di project latihan lamamu, route hapusnya masih `Route::get('users/{id}/destroy', ...)` dan dipanggil pakai `<a href>`. Kalau penguji menyinggung itu, jawab:
> "Betul, di latihan sebelumnya saya masih pakai `GET`. Di TrackKas itu saya perbaiki jadi `DELETE` karena saya sekarang paham risikonya."

### 🔴 Q16. Di `routes/web.php` parameternya `{transaction}`, tapi di controller method-nya `destroy(Request $request, string $id)`. Kok bisa jalan?

Ini pertanyaan bagus dan hampir pasti muncul kalau penguji teliti.

> "Yang pertama, karena di controller saya tidak menulis tipe modelnya di parameter (`Transaction $transaction`), Laravel **tidak melakukan route model binding** — jadi nilainya dikirim sebagai teks biasa, bukan langsung diubah jadi objek. Yang kedua, Laravel mengisi parameter controller dari daftar parameter route, dan karena `$id` tidak punya kecocokan nama, nilainya diisi berdasarkan urutan. Makanya tetap masuk.
> Tapi saya sadar itu membingungkan pembaca. Perbaikan yang benar adalah menyamakan namanya — route jadi `{id}`, atau method-nya diketik sebagai `Transaction $transaction` supaya route model binding aktif dan otomatis menghasilkan 404 kalau datanya tidak ada."

Kalau penguji menyuruhmu memperbaikinya, ini yang benar:

```php
// routes/web.php
Route::delete('/transaksi/{transaction}', [TransactionsController::class, 'destroy'])->name('transaksi.destroy');

// app/Http/Controllers/TransactionsController.php
public function destroy(Transaction $transaction)
{
    abort_unless($transaction->user_id === auth()->id(), 404);

    $transaction->delete();

    return redirect()->route('transaksi.index')->with('status', 'Transaksi berhasil dihapus.');
}
```

### Q17. Apa itu route model binding?

> "Fitur Laravel yang mengubah nilai di URL menjadi objek model secara otomatis. Kalau saya tulis `destroy(Transaction $transaction)`, Laravel otomatis menjalankan pencarian berdasarkan ID. Keuntungannya, kalau datanya tidak ada, Laravel langsung membalas 404 tanpa saya perlu menulis `find` atau `findOrFail` sendiri."

### Q18. Kenapa ada route di `routes/auth.php` yang tidak kamu tulis sendiri?

> "Itu dibuat oleh Laravel Breeze, paket starter autentikasi. Isinya route login, register, logout, lupa password, reset password, verifikasi email, dan konfirmasi password. Saya memakai itu supaya tidak membangun sistem login dari nol — bagian yang risikonya tinggi kalau salah."

### 🔴 Q19. Kenapa halaman profil tidak ikut di grup `verified`?

> "Karena halaman profil hanya butuh syarat sudah login, bukan syarat email terverifikasi. Jadi cukup middleware `auth`. Untuk route auth.php saya tidak mengubahnya, itu bawaan Breeze."

### Q20. Coba tunjukkan semua route aplikasimu.

Buka terminal, jalankan:

```bash
php artisan route:list
```

Output yang harus kamu tunjukkan dan jelaskan: kolom Method, URI, Name, Action, Middleware. Tunjuk satu baris dan jelaskan.

### Q21. Apa yang terjadi kalau user yang belum login membuka `/transaksi`?

> "Middleware `auth` menangkapnya sebelum sampai ke controller, lalu mengalihkan ke halaman login. Setelah berhasil login, user dikembalikan ke halaman yang tadi dituju. Ini sudah saya uji, ada di `DashboardTest` method `test_guests_cannot_open_authenticated_pages`."

### 🔴 Q22. Kenapa kamu pakai middleware `verified` padahal email verification belum aktif?

Ini pertanyaan jebakan halus. Jawaban jujur:

> "Karena itu bawaan grup dari Breeze, saya biarkan supaya sesuai standar. Efeknya sekarang nol, karena di `app/Models/User.php` implementasi `MustVerifyEmail` masih saya komentari. Jadi middleware `verified` dilewati saja tanpa menahan apa pun. Kalau nanti verifikasi email saya aktifkan, proteksinya sudah siap."

### Q23. Kalau URL-nya salah ketik, apa yang muncul?

> "Halaman 404 bawaan Laravel. Kalau mau, tampilannya bisa saya sesuaikan lewat file `resources/views/errors/404.blade.php`, tapi belum saya kerjakan karena bukan bagian alur utama."

### Q24. Apa arti `Route::get` vs `Route::post` vs `Route::delete`?

> "Itu HTTP verb, jenis permintaan. `GET` untuk menampilkan halaman, `POST` untuk mengirim data baru, `PUT`/`PATCH` untuk mengubah data yang ada, `DELETE` untuk menghapus. Saya pakai sesuai fungsinya supaya aplikasinya sesuai standar web."

---

# BAGIAN 2 — Controller (Q25–Q48)

### 🔴 Q25. Buka `TransactionsController`. Jelaskan satu per satu methodnya.

```php
class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $transactions = $request->user()->transactions()
            ->when($request->type, fn ($query, $type) => $query->where('type', $type))
            ->latest('date')
            ->latest('id')
            ->get();

        return view('transaksi.index', [
            'transactions' => $transactions,
            'type' => $request->type,
        ]);
    }

    public function create()
    {
        return view('transaksi.create');
    }

    public function store(Request $request)
    {
        $request->user()->transactions()->create($request->validate([
            'type' => ['required', 'in:pemasukan,pengeluaran'],
            'date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]));

        return redirect()->route('transaksi.index')->with('status', 'Transaksi berhasil ditambahkan.');
    }

    public function destroy(Request $request, string $id)
    {
        $request->user()->transactions()->findOrFail($id)->delete();

        return redirect()->route('transaksi.index')->with('status', 'Transaksi berhasil dihapus.');
    }
}
```

> "`index` menampilkan daftar transaksi milik user yang login, diurutkan dari tanggal terbaru. `create` hanya menampilkan form. `store` memvalidasi input lalu menyimpannya ke database. `destroy` mencari transaksi milik user tersebut berdasarkan ID, lalu menghapusnya."

### 🔴 Q26. Kenapa `$request->user()` dan bukan `auth()->user()`?

> "Fungsinya sama, sama-sama mengambil user yang sedang login. `$request->user()` saya pakai karena objek `$request` sudah tersedia sebagai parameter di method itu, jadi tidak perlu memanggil helper. Di `DashboardController` saya memakai `auth()->user()` karena method itu tidak menerima parameter `$request`. Dua-duanya sah; saya usahakan konsisten dalam satu file."

⚠️ Kalau penguji bilang "harusnya `auth()` saja", jawab: *"Siap, Pak/Bu. Dua-duanya sah — `$request->user()` itu cara yang lebih eksplisit karena mengambil dari objek request, bukan dari global helper."* Jangan berdebat.

### 🔴 Q27. Baris ini maksudnya apa? `$request->user()->transactions()->...`

> "Ini gabungan tiga hal. `$request->user()` mengambil user yang login. `transactions()` memanggil relasi `hasMany` di model User. Karena saya memakai tanda kurung setelah nama relasi, hasilnya adalah **query builder**, bukan kumpulan data. Dari situ saya bisa menambahkan `where`, `latest`, `sum`, atau `get`. Ini yang membuat datanya otomatis terbatas pada milik user itu saja."

### 🔴 Q28. Kenapa `transactions()` pakai kurung, apa bedanya dengan `transactions` tanpa kurung?

> "Dengan kurung, `$user->transactions()` menghasilkan query builder yang belum dieksekusi — jadi saya masih bisa menambahkan kondisi di belakangnya. Tanpa kurung, `$user->transactions` langsung menjalankan query dan mengembalikan Collection, jadi datanya sudah final. Saya butuh yang pakai kurung karena mau menambahkan filter dan pengurutan."

Ini salah satu pertanyaan favorit penguji. Hafalkan.

### 🔴 Q29. Jelaskan baris `->when($request->type, fn ($query, $type) => $query->where('type', $type))`.

> "`when` itu kondisi pada query. Kalau argumen pertamanya bernilai benar — artinya user memilih filter jenis transaksi — maka closure-nya dijalankan dan menambahkan `where('type', ...)`. Kalau `type` kosong, kondisi itu dilewati. Jadi satu query yang sama bisa melayani dua keadaan: semua data, atau data terfilter."

### Q30. Kalau tidak pakai `when`, bagaimana?

> "Bisa pakai `if` biasa:
> ```php
> $query = $request->user()->transactions();
> if ($request->type) {
>     $query->where('type', $request->type);
> }
> $transactions = $query->latest('date')->get();
> ```
> Hasilnya sama. Saya pilih `when` supaya alur query-nya terbaca dalam satu rangkaian, tapi versi `if` itu lebih mudah dibaca pemula."

### 🔴 Q31. Kenapa `->latest('date')->latest('id')` dua kali?

> "`latest('date')` mengurutkan dari tanggal terbaru, tapi kalau ada beberapa transaksi di tanggal yang sama, urutannya tidak pasti. `latest('id')` jadi penentu kedua supaya transaksi yang terakhir dimasukkan muncul di atas. Jadi urutannya konsisten."

### 🔴 Q32. Kenapa `destroy` pakai `findOrFail`, bukan `find`?

> "`find` mengembalikan `null` kalau datanya tidak ada, dan kalau saya langsung panggil `->delete()` setelahnya aplikasinya error. `findOrFail` otomatis melempar 404, jadi lebih aman dan lebih ringkas."

⚠️ Di project latihanmu dulu tertulis `User::find($id)->delete()` — itu bisa error. Kalau ditanya, akui: *"Betul, itu kelemahan di latihan saya sebelumnya. Di TrackKas saya perbaiki pakai `findOrFail`."*

### 🔴 Q33. Kalau `create` di atas dipanggil, data apa yang masuk ke database?

> "Lima kolom: `type`, `date`, `category`, `amount`, `note`. Kolom `user_id` terisi otomatis dari user yang login, karena `create` dipanggil setelah `->transactions()`. Jadi saya tidak perlu mengirim `user_id` dari form — dan itu memang saya sengaja, supaya user tidak bisa memalsukan pemilik datanya."

### Q34. Kenapa `store` mengembalikan `redirect()` bukan `return view(...)`?

> "Karena ini pola PRG — Post, Redirect, Get. Kalau setelah menyimpan data saya langsung menampilkan view, halaman itu akan berisi form POST. Kalau user menekan refresh, browser akan mengirim ulang form dan datanya jadi dobel. Dengan redirect, browser melakukan GET bersih, jadi refresh tidak menduplikasi data."

### 🔴 Q35. `->with('status', 'Transaksi berhasil ditambahkan.')` itu apa?

> "Itu flash message. Datanya disimpan sebentar di session, hanya untuk satu request berikutnya. Di view saya membacanya lewat `session('status')` dan menampilkannya sebagai kotak hijau. Setelah dibaca, pesannya otomatis hilang."

### Q36. Kenapa pesannya berbahasa Indonesia?

> "Karena target penggunanya orang Indonesia. Seluruh teks antarmuka saya tulis dalam bahasa Indonesia supaya konsisten — judul halaman, label form, tombol, sampai pesan berhasil dan pesan error."

### 🔴 Q37. Kenapa di `create()` tidak ada `Request $request`?

> "Karena method itu memang tidak butuh data dari user. Tugasnya hanya menampilkan form. Parameter hanya saya tambahkan kalau memang dipakai."

### Q38. Kenapa `DashboardController` method-nya `__invoke`?

> "Itu controller dengan satu tugas — hanya menampilkan dashboard. Kalau controllernya cuma punya satu method, Laravel mengizinkan penulisan ringkas: class-nya langsung bisa dipanggil. Di route cukup `Route::get('/dashboard', DashboardController::class)` tanpa menyebut nama method."

### 🔴 Q39. Jelaskan isi `DashboardController`.

```php
public function __invoke(): View
{
    $income = auth()->user()->transactions()->where('type', 'pemasukan')->sum('amount');
    $expense = auth()->user()->transactions()->where('type', 'pengeluaran')->sum('amount');

    return view('dashboard', [
        'income' => $income,
        'expense' => $expense,
        'balance' => $income - $expense,
        'latest' => auth()->user()->transactions()->latest('date')->latest('id')->take(5)->get(),
        'incomeByCategory' => $this->totalsByCategory('pemasukan'),
        'expenseByCategory' => $this->totalsByCategory('pengeluaran'),
    ]);
}
```

> "Saya menjumlahkan kolom `amount` untuk transaksi bertipe pemasukan dan pengeluaran, lalu saldo adalah selisih keduanya. `latest` mengambil lima transaksi terbaru untuk daftar di dashboard. Dua baris terakhir memanggil method privat `totalsByCategory` untuk menghitung total per kategori."

### 🔴 Q40. Kenapa `DashboardController` punya method privat?

> "Karena perhitungan 'total per kategori' dipakai dua kali, untuk pemasukan dan pengeluaran, dan isinya identik. Daripada menulis blok yang sama dua kali, saya bikin satu method privat yang menerima parameter jenis transaksi. Ini prinsip DRY — don't repeat yourself."

### Q41. Apa itu `: View` setelah kurung method?

> "Itu deklarasi tipe nilai kembalian. Jadi method itu dideklarasikan mengembalikan objek `View`. Manfaatnya dua: editor bisa memberi peringatan kalau saya salah mengembalikan sesuatu, dan pembaca langsung tahu method itu mengembalikan view."

### Q42. Kenapa method privat itu tidak bisa diakses dari route?

> "Karena route hanya bisa memanggil method `public`. Method privat adalah fungsi internal dalam class, dipakai hanya oleh method lain di class yang sama. Dan memang tidak perlu diakses dari luar — jadi saya tutup."

### Q43. Apa yang terjadi kalau `amount` berisi huruf?

> "Kalau lewat form, dia ditolak oleh validasi `numeric` sebelum sampai ke database. Kalau tidak lewat form, kolomnya bertipe `decimal` sehingga MySQL menolak atau mengonversinya. Validasi di aplikasi dan tipe kolom di database itu dua lapis pengamanan."

### 🔴 Q44. Kenapa di `store` kamu tidak pakai `$request->all()`?

> "Karena `all()` mengambil **semua** kiriman dari browser, termasuk field yang tidak saya harapkan. `validate()` hanya mengembalikan data yang lolos aturan dan yang saya daftarkan — jadi kolom lain otomatis terbuang. Ini melindungi dari mass assignment yang tidak sengaja."

### Q45. Boleh tunjukkan bedanya secara nyata?

> "Misalnya saya menulis `$request->all()`. Seseorang bisa menambahkan input tersembunyi bernama `user_id` lewat developer tools dan menyimpan transaksi itu atas nama user lain. Dengan `validate()`, kolom `user_id` tidak ada di daftar aturan, jadi tidak akan pernah ikut tersimpan."

### Q46. Apa itu closure `fn ($query, $type) => ...`?

> "Itu arrow function, bentuk singkat dari `function () { return ...; }`. Saya pakai karena isinya cuma satu baris. Parameter `$query` dan `$type` diisi otomatis oleh Laravel: `$query` adalah query builder yang sedang berjalan, `$type` adalah nilai filter."

### Q47. Kalau penguji minta "ringkas satu baris controller ini", apa jawabanmu?

> "Isinya tiga hal: ambil data milik user yang login, validasi atau ubah data itu, lalu arahkan ke halaman berikutnya."

### Q48. Kenapa tidak ada `show()` (lihat detail satu transaksi)?

> "Karena tidak dibutuhkan. Semua informasi transaksi — tanggal, kategori, jenis, jumlah, catatan — sudah saya tampilkan langsung di halaman daftar. Membuat halaman detail hanya akan menambah satu langkah klik tanpa menambah informasi baru."

---

# BAGIAN 3 — Validasi (Q49–Q60)

### 🔴 Q49. Bagaimana kamu memvalidasi input?

> "Memakai `$request->validate()` di dalam controller. Saya mendaftarkan setiap field beserta aturannya. Kalau ada yang gagal, Laravel otomatis mengembalikan user ke form sebelumnya bersama pesan error, dan data yang sudah diisi tetap tersimpan supaya user tidak perlu mengetik ulang."

### 🔴 Q50. Jelaskan aturan validasimu satu per satu.

```php
'type'     => ['required', 'in:pemasukan,pengeluaran'],
'date'     => ['required', 'date'],
'category' => ['required', 'string', 'max:255'],
'amount'   => ['required', 'numeric', 'min:0'],
'note'     => ['nullable', 'string', 'max:255'],
```

> "Untuk `type` wajib diisi dan nilainya harus salah satu dari dua: pemasukan atau pengeluaran. `date` wajib dan harus format tanggal yang sah. `category` wajib, berupa teks, maksimal 255 karakter sesuai panjang kolomnya. `amount` wajib, harus angka, minimal nol. `note` opsional, kalau diisi harus teks maksimal 255 karakter."

### 🔴 Q51. Kenapa `type` divalidasi dengan `in:`? Bukankah di form sudah radio button?

> "Karena validasi di HTML itu hanya kenyamanan untuk pengguna, bukan keamanan. User tetap bisa mengirim nilai apa pun lewat developer tools atau lewat Postman. Kalau `type` berisi teks bebas di luar dua pilihan itu, laporan keuangannya jadi rusak karena ada baris yang tidak masuk hitungan pemasukan maupun pengeluaran. Jadi aturan `in:` itu yang benar-benar menjaga."

### Q52. Kenapa `min:0` dan bukan `min:1`?

> "Karena transaksi bernilai nol masih masuk akal. Misalnya ada mutasi dengan nilai nol. Tapi kalau nilainya harus benar-benar positif, `min:1` lebih tepat. Saya pilih `min:0` supaya lebih longgar."

### Q53. Kenapa `note` pakai `nullable`?

> "Karena catatan itu opsional. Tanpa `nullable`, field kosong akan dianggap gagal validasi karena `required` — atau karena aturan lain menganggap string kosong tidak sah. `nullable` memberi izin kolom itu dibiarkan kosong."

### Q54. Kalau validasi gagal, apa yang user lihat?

> "Kembali ke halaman form, lalu di bawah tiap field yang bermasalah muncul teks error. Itu dari komponen `<x-input-error :messages="$errors->get('nama_field')" />`. Nilai yang tadi diketik juga tetap ada, karena setiap field memakai `old('nama_field')`."

### 🔴 Q55. Apa itu `old()` dan kenapa perlu?

> "`old()` mengambil kembali nilai dari input sebelumnya yang tersimpan di session. Fungsinya supaya user tidak perlu mengisi ulang seluruh form hanya karena satu field salah. Contoh di TrackKas: `:value="old('category')"`."

### Q56. Bagaimana `old()` bisa tahu nilai sebelumnya?

> "Laravel menaruh seluruh input yang gagal validasi ke dalam session, lalu mengalihkan user kembali ke form. Saat view dirender, `old()` mengambil dari session itu. Setelah ditampilkan, datanya dibersihkan seperti flash message."

### Q57. Kenapa tanggal diberi nilai awal dari `now()->toDateString()`?

> "Supaya field tanggal langsung terisi hari ini, karena kebanyakan orang mencatat transaksi hari ini. Tapi tetap saya bungkus dengan `old('date', ...)` supaya kalau validasi gagal, tanggal yang tadi dipilih tetap dipertahankan."

### Q58. Kenapa ada validasi juga di HTML (`required`, `type="number"`, `maxlength`)?

> "Itu lapisan pertama supaya user langsung tahu tanpa menunggu ke server. Tapi lapisan itu bisa dilewati, jadi validasi server tetap wajib ada. Prinsipnya: validasi di browser untuk kenyamanan, validasi di server untuk keamanan."

### 🔴 Q59. Kenapa validasi ditaruh di controller, bukan di Form Request?

> "Untuk project sebesar ini, validasi di controller lebih ringkas — saya bisa melihat aturan dan penyimpanannya dalam satu tempat. Kalau field-nya sudah banyak atau dipakai di beberapa controller, baru lebih rapi dipisah ke Form Request tersendiri."

### Q60. Coba tunjukkan validasi yang gagal berjalan.

Demo hidup: buka halaman Tambah Transaksi, kosongkan kategori, isi jumlah dengan huruf, klik Simpan. Tunjuk pesan errornya. Lalu tunjuk di `TransactionTest` method `test_a_transaction_needs_valid_data` yang menguji hal yang sama secara otomatis.

---

# BAGIAN 4 — Model & Eloquent (Q61–Q78)

### 🔴 Q61. Jelaskan model `Transaction`.

```php
#[Fillable(['type', 'category', 'amount', 'date', 'note'])]
class Transaction extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

> "Model ini mewakili tabel `transactions`. `#[Fillable]` mendaftarkan kolom mana yang boleh diisi massal dari input. `casts()` menentukan tipe konversi: `amount` jadi desimal dua angka, `date` jadi objek tanggal. `user()` mendefinisikan relasi ke pemiliknya."

### 🔴 Q62. Apa itu `$fillable` / `#[Fillable]` dan kenapa penting?

> "`fillable` adalah daftar kolom yang **boleh** diisi lewat `create()` atau `update()` dengan array. Fungsinya mencegah *mass assignment* — supaya input dari browser tidak bisa mengisi kolom yang tidak seharusnya, seperti `user_id` atau `is_admin`.
> Di Laravel 13 ada cara baru memakai atribut PHP, `#[Fillable([...])]`, yang saya pakai di sini. Fungsi dan isinya sama persis dengan cara lama `protected $fillable = [...]`, yang mungkin lebih kamu kenal."

Kalau penguji tanya versi lama, tulis:

```php
protected $fillable = ['type', 'category', 'amount', 'date', 'note'];
```

### 🔴 Q63. Kenapa `user_id` tidak ada di daftar fillable?

> "Justru sengaja. `user_id` saya isi otomatis dari user yang login di controller, bukan dari input. Kalau dimasukkan ke fillable, user bisa memalsukan pemilik data. Ini pengaman lapis kedua, di samping validasi yang tidak menerima field itu."

### 🔴 Q64. Kalau `$fillable` belum diisi, apa error yang muncul?

> "`MassAssignmentException`. Laravel menolaknya secara sengaja, bukan diam-diam mengabaikan. Jadi kalau kamu lupa mengisi fillable, kamu akan langsung tahu saat mencoba menyimpan."

### 🔴 Q65. Apa itu `casts()` dan apa untungnya?

> "`casts` menentukan bagaimana nilai kolom diubah saat dibaca dan ditulis. `amount => decimal:2` membuat jumlah selalu dibaca sebagai angka dengan dua angka desimal, jadi perhitungannya konsisten. `date => date` mengubah string tanggal dari database menjadi objek Carbon, sehingga di view saya bisa memanggil `$transaction->date->format('d M Y')`."
> "Kalau tanpa cast `date`, `$transaction->date` cuma teks, dan `->format()` akan error."

### Q66. Boleh tunjukkan bukti cast `date` bekerja?

Tunjuk `resources/views/transaksi/index.blade.php`:

```blade
{{ $transaction->date->format('d M Y') }}
```

> "Kalau tidak ada cast `date`, tanda panah `->format()` itu error, karena string tidak punya method `format`. Padahal aplikasinya berjalan, jadi cast-nya terbukti aktif."

### 🔴 Q67. Apa itu relasi `hasMany` dan `belongsTo`?

> "Pasangan dua arah. Di `User` saya menulis `hasMany(Transaction::class)` — satu user punya banyak transaksi. Di `Transaction` saya menulis `belongsTo(User::class)` — satu transaksi milik satu user. Keduanya dihubungkan oleh kolom `user_id`."

```php
// app/Models/User.php
public function transactions(): HasMany
{
    return $this->hasMany(Transaction::class);
}
```

### Q68. Kenapa relasinya perlu ditulis dua kali? Kan bisa satu saja?

> "Secara teknis bisa satu saja, tapi arahnya beda dan keduanya dipakai. `hasMany` saya pakai untuk mengambil dan memfilter transaksi milik user yang login. `belongsTo` saya pakai kalau perlu jalan balik — misalnya menampilkan nama pemilik dari sebuah transaksi. Menulis keduanya juga membuat struktur relasinya jelas bagi pembaca."

### Q69. Bagaimana Laravel tahu kolom penghubungnya `user_id`?

> "Dari konvensi. Nama relasinya `transactions`, jadi modelnya mencari kolom `transaction_id`; tapi karena saya menulis `belongsTo(User::class)`, Laravel mencari `user_id`, yaitu nama model-nya dalam bentuk snake_case ditambah `_id`. Cocok dengan kolom yang saya buat di migrasi. Kalau namanya beda, saya harus menyebutkannya manual sebagai argumen kedua."

### Q70. Apa itu `use HasFactory`?

> "Trait yang menambahkan `Transaction::factory()` pada model. Saya pakai ini di testing: `Transaction::factory()->for($user)->create(['category' => 'Gaji'])` langsung membuat satu baris data transaksi untuk user tersebut. Ini bikin test jadi ringkas dan tidak bergantung pada data asli."

### Q71. Apa itu `scope`? Kamu pakai?

> "Belum. Scope adalah cara membungkus potongan query yang sering dipakai, misalnya `scopePemasukan()`, sehingga bisa dipanggil sebagai `$transaction->pemasukan()`. Di project ini kondisi filternya masih sedikit, jadi saya belum merasa perlu."

### Q72. Apa itu accessor dan mutator?

> "Accessor mengubah nilai saat dibaca, mutator mengubahnya saat ditulis. Cast `decimal:2` dan `date` yang saya pakai itu bentuk paling sederhana. Kalau misalnya saya mau `$transaction->formatted_amount` langsung berbentuk 'Rp 25.000', itu bisa dibuat dengan accessor. Saya pilih membuatnya sebagai komponen Blade `<x-rupiah>` supaya urusan tampilan tetap di lapisan view."

### Q73. Kenapa kamu butuh `Transaction::class` dan bukan nama string biasa?

> "`::class` menghasilkan nama lengkap class termasuk namespace, dalam bentuk teks. Jadi `Transaction::class` berisi `App\Models\Transaction`. Dengan begitu kalau saya memindahkan file modelnya, saya juga memindahkan namespace-nya, dan referensi ini ikut menyesuaikan. Kalau ditulis manual sebagai string, bisa tidak sinkron."

### Q74. Apa itu Collection?

> "Hasil `get()` bukan array biasa, tapi Collection — objek dari Laravel yang membungkus array dan menambahkan banyak method praktis seperti `isEmpty()`, `max()`, `filter()`, `first()`. Di dashboard saya memakai `$incomeByCategory->max('total')` dan `$transactions->isEmpty()`, yang keduanya kekuatan Collection."

### 🔴 Q75. Kalau penguji tanya `with()` atau `load()`, apa jawabanmu?

> "Itu untuk eager loading. Kalau saya punya daftar transaksi lalu perlu nama pemiliknya masing-masing di dalam loop, tanpa `with('user')` Laravel akan menembak satu query untuk setiap baris — itu yang disebut masalah N+1. Di TrackKas saya belum memerlukannya karena yang ditampilkan selalu milik user yang login sendiri. Kalau nanti ada tampilan admin yang menampilkan transaksi banyak user, saya akan pakai `->with('user')`."

### Q76. Di mana `sum('amount')` dijalankan, di PHP atau di database?

> "Di database. Laravel menerjemahkannya menjadi SQL `SELECT SUM(amount) ...`. Jadi yang dikirim kembali ke PHP hanya satu angka, bukan seluruh barisnya. Ini jauh lebih efisien."

### Q77. Kenapa `selectRaw` dipakai di `totalsByCategory`?

> "Karena saya butuh kolom tambahan hasil hitungan, yaitu `sum(amount) as total`, dan itu tidak ada di kolom asli. `selectRaw` mengizinkan saya menulis potongan SQL mentah untuk kebutuhan itu. Catatannya: karena ini SQL mentah, saya tidak beri input user di dalamnya, jadi tidak ada risiko injeksi."

### 🔴 Q78. Jelaskan query per kategori itu.

```php
return auth()->user()->transactions()
    ->where('type', $type)
    ->selectRaw('category, sum(amount) as total')
    ->groupBy('category')
    ->orderByDesc('total')
    ->get();
```

> "Ambil transaksi milik user, saring berdasarkan jenis, lalu kelompokkan per kategori dan jumlahkan kolom amount-nya. Hasilnya tiap baris berisi nama kategori dan totalnya. Diurutkan dari total terbesar, dan di view saya pakai `max('total')` untuk menghitung panjang bar-nya."

---

# BAGIAN 5 — Migration & Database (Q79–Q92)

### 🔴 Q79. Kamu merancang tabelnya seperti apa? Tunjukkan migrasinya.

```php
Schema::table('transactions', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('type');            // pemasukan atau pengeluaran
    $table->string('category');
    $table->decimal('amount', 15, 2);
    $table->date('date');
    $table->string('note')->nullable();
});
```

> "Tabel `transactions` punya kolom ID bawaan, `user_id` sebagai penghubung ke tabel users, `type` untuk jenis, `category` untuk kategori, `amount` untuk jumlah, `date` untuk tanggal, `note` untuk catatan opsional, plus `created_at` dan `updated_at`."

### 🔴 Q80. Kenapa `amount` bertipe `decimal` dan bukan `float` atau `integer`?

> "Karena `float` punya masalah pembulatan pada perhitungan desimal — angka seperti 0.1 + 0.2 tidak menghasilkan tepat 0.3. Untuk data uang, itu tidak boleh. `integer` juga tidak cocok karena tidak bisa menyimpan sen. Jadi saya pakai `decimal(15,2)`: 15 digit total dengan 2 angka di belakang koma, cukup untuk nominal rupiah sampai ratusan triliun dengan pecahan sen."

### 🔴 Q81. Apa maksud `foreignId('user_id')->constrained()->cascadeOnDelete()`?

> "Tiga hal sekaligus. `foreignId` membuat kolom `user_id` bertipe unsigned big integer. `constrained()` membuat foreign key ke tabel `users` berdasarkan nama kolomnya. `cascadeOnDelete()` menentukan kalau user dihapus, semua transaksinya ikut terhapus. Jadi tidak ada transaksi yang menggantung tanpa pemilik."

### Q82. Kalau tidak pakai `cascadeOnDelete`, apa akibatnya?

> "Menghapus user bisa gagal karena database menolak ada anak yang masih mengacu ke induknya. Kalau saya biarkan tanpa aturan apa pun, bisa muncul data yatim — transaksi yang tidak punya pemilik dan tidak akan pernah tampil di aplikasi mana pun."

### 🔴 Q83. Kenapa ada DUA migrasi untuk satu tabel?

Ini pertanyaan jebakan yang hampir pasti muncul.

> "Kejujurannya: migrasi pertama saya buat saat awal merancang project, isinya baru `id` dan `timestamps`. Setelah desainnya matang, saya menambahkan migrasi kedua untuk mengisi kolom aslinya. Praktik yang lebih bersih adalah satu migrasi saja sejak awal — dan karena project ini belum pernah dirilis, saya masih bisa menggabungkannya."

⚠️ Jangan mengarang alasan teknis seperti "karena penamaan tabel". Akui apa adanya dengan tenang. Kalau penguji minta digabung, lakukan:

```bash
php artisan migrate:rollback --step=1
# lalu gabungkan kolomnya ke migrasi pertama dan hapus file kedua
php artisan migrate:fresh
```

### 🔴 Q84. Apa itu migration? Kenapa tidak bikin tabel langsung di phpMyAdmin?

> "Migration adalah versi kode dari struktur database. Keuntungannya: siapa pun yang mengambil project ini cukup menjalankan `php artisan migrate` dan struktur database-nya terbentuk otomatis, tanpa perlu tahu urutan pembuatannya. Kalau saya bikin langsung di phpMyAdmin, struktur itu tidak terekam, dan kalau project dipindah ke komputer lain akan hilang. Selain itu migration bisa dibalik dengan `migrate:rollback`, sedangkan perubahan manual tidak bisa."

### Q85. Tunjukkan perintah migrasi yang kamu pakai.

```bash
php artisan migrate           # menjalankan migrasi yang belum dijalankan
php artisan migrate:status    # melihat migrasi mana yang sudah/belum jalan
php artisan migrate:rollback  # membatalkan batch terakhir
php artisan migrate:fresh     # hapus semua tabel lalu migrasi ulang (DATA HILANG)
```

> "Kalau saya mengubah isi file migrasi yang **sudah** dijalankan, perubahan itu tidak otomatis berlaku karena Laravel mencatatnya di tabel `migrations`. Saya harus `rollback` dulu, baru `migrate` lagi."

### Q86. Apa itu `down()`?

> "Kebalikan dari `up()`. `up()` membuat perubahan, `down()` membatalkannya. Contohnya di migrasi saya: `up()` menambah kolom, `down()` menghapus kolom itu. Tanpa `down()`, `migrate:rollback` tidak bisa bekerja."

### Q87. Kenapa di `down()` urutannya `dropConstrainedForeignId` dulu baru `dropColumn`?

> "Karena foreign key-nya harus dilepas sebelum kolomnya dihapus, kalau tidak database akan menolak. `dropConstrainedForeignId` memang khusus untuk itu: melepas constraint sekaligus menghapus kolomnya."

### Q88. Apa itu seeder dan factory? Kamu pakai?

> "Seeder adalah pengisi data awal, factory adalah pencetak data palsu. `TransactionFactory` saya pakai untuk testing — setiap kali test dijalankan, database dalam memori diisi data contoh secara otomatis. Untuk aplikasi aslinya tidak ada seeder, karena data transaksi memang harus kosong saat user baru mendaftar."

### Q89. Kenapa tidak boleh ada data transaksi contoh saat user baru daftar?

> "Karena itu bukan datanya. Kalau user baru langsung melihat transaksi orang lain, aplikasinya terasa rusak. Halaman kosong justru benar di sini, dan saya sudah menyiapkan keadaan kosong yang ramah — ada ikon dan tulisan 'Belum ada transaksi' beserta tombol ajakan menambah data."

### 🔴 Q90. Bagaimana kamu memastikan satu user tidak bisa melihat data user lain?

> "Tiga cara yang saya pakai. Pertama, setiap query berangkat dari `$request->user()->transactions()`, jadi hasilnya memang sudah terbatas pada miliknya. Kedua, di daftar transaksi saya tidak pernah menerima parameter user dari URL. Ketiga, untuk hapus, saya cari datanya di dalam relasi user tersebut dengan `findOrFail`, sehingga ID milik orang lain otomatis menghasilkan 404."

### Q91. Kenapa `findOrFail` di dalam relasi itu aman?

> "Karena query-nya sudah punya filter `where user_id = <id user login>`. Jadi `findOrFail(7)` bukan berarti mencari transaksi nomor 7 di seluruh tabel, tapi mencari transaksi nomor 7 **milik user ini**. Kalau ada di tangan orang lain, hasilnya kosong dan langsung 404."

### Q92. Kalau saya buat relasi baru lewat `$user->transactions()->create()`, kolom `user_id` terisi dari mana?

> "Otomatis. Saat `create` dipanggil pada relasi, Laravel menyisipkan nilai foreign key-nya sesuai user pemilik relasi itu. Jadi saya tidak perlu mengirim `user_id` manual — dan memang sengaja begitu, supaya tidak bisa dipalsukan."

---

# BAGIAN 6 — Autentikasi & Keamanan (Q93–Q112)

### 🔴 Q93. Bagaimana sistem loginnya kamu buat?

> "Memakai Laravel Breeze, paket starter resmi dari Laravel. Breeze menyediakan register, login, logout, lupa password, dan reset password lengkap dengan view, controller, route, dan test-nya. Saya memakainya supaya tidak menulis sistem autentikasi dari nol — itu bagian yang risikonya tinggi kalau salah. Yang saya kerjakan adalah menyesuaikan tampilannya dengan desain TrackKas dan menambahkan redirect di halaman depan."

### Q94. Kalau Breeze menyediakan semuanya, apa bagian yang benar-benar kamu kerjakan?

> "Tiga hal utama: seluruh fitur transaksi beserta dashboard dan perhitungannya, rancangan tampilan TrackKas — sidebar, kartu statistik, komponen Blade, palet warna brand — serta pengaturan autentikasi supaya langsung masuk ke dashboard dan halaman depan mengarah ke login."

Jujur soal batas kontribusi. Itu dinilai bagus.

### 🔴 Q95. Di mana password user disimpan? Apakah aman?

> "Di kolom `password` tabel `users`, dalam bentuk hash bcrypt — bukan teks asli. Di model `User` ada cast `'password' => 'hashed'`, jadi setiap kali password diisi, Laravel otomatis meng-hash-nya. Saya sendiri tidak pernah bisa membaca password user."

```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

### 🔴 Q96. Bagaimana proses pengecekan password saat login?

> "Saat user mengirim email dan password, Laravel mengambil data user berdasarkan email, lalu membandingkan password yang dikirim dengan hash-nya memakai `Hash::check()`. Hash-nya tidak pernah dibalik menjadi teks, hanya dicocokkan. Kalau cocok, session-nya ditandai sebagai sudah login."

### 🔴 Q97. Apa itu session?

> "Cara server mengenali bahwa permintaan ini datang dari orang yang sama yang sudah login. Setelah login berhasil, server menyimpan penanda di session, dan browser membawa cookie yang menunjuk ke session itu. Di setiap request berikutnya, Laravel membaca cookie itu dan tahu siapa usernya."

### 🔴 Q98. Apa itu middleware `auth`?

> "Petugas pemeriksa yang berjalan sebelum request masuk ke controller. Tugasnya memastikan user sudah login. Kalau belum, request dialihkan ke halaman login dan tidak pernah sampai ke controller. Saya memasangnya di grup route lewat `Route::middleware(['auth', 'verified'])->group(...)`."

### 🔴 Q99. Apa itu CSRF dan bagaimana kamu melindunginya?

> "CSRF adalah serangan saat situs lain mengirimkan form atas namamu ke situs yang sedang kamu buka, dengan memanfaatkan session yang masih aktif. Perlindungannya pakai token: Laravel membuat token unik per session, dan setiap form POST, PUT, atau DELETE harus membawa token itu. Kalau tokennya tidak ada atau salah, request ditolak."

```blade
<form method="POST" action="{{ route('transaksi.store') }}">
    @csrf
    ...
</form>
```

> "`@csrf` itu mencetak input tersembunyi bernama `_token` berisi token session. Di project latihan sebelumnya saya memakai `{{ csrf_field() }}` — fungsinya sama persis, `@csrf` hanya bentuk singkatan yang lebih baru."

### 🔴 Q100. Bagaimana caramu mengirim method DELETE padahal HTML hanya punya GET dan POST?

> "Pakai method spoofing. HTML form tidak mendukung method selain GET dan POST, jadi saya tetap menulis `method="POST"` tapi menambahkan `@method('DELETE')`. Direktif itu mencetak input tersembunyi `_method` bernilai DELETE, dan Laravel membacanya sehingga route-nya diperlakukan sebagai DELETE."

```blade
<form method="POST" action="{{ route('transaksi.destroy', $transaction) }}"
      onsubmit="return confirm('Hapus transaksi ini?')">
    @csrf
    @method('DELETE')

    <button type="submit">Hapus</button>
</form>
```

### 🔴 Q101. Kenapa CSRF perlu kalau sudah ada login?

> "Justru karena sudah login, risikonya lebih besar. Kalau tidak ada CSRF, situs lain bisa membuatmu menekan tombol hapus tanpa sadar — dan karena kamu sedang login, server menganggapnya permintaan sah darimu. Jadi login dan CSRF melindungi dari dua hal yang berbeda: login melindungi identitas, CSRF melindungi keaslian permintaan."

### 🔴 Q102. Apa itu IDOR? Apakah projectmu rentan?

> "IDOR — insecure direct object reference — adalah celah saat user bisa mengubah nilai ID di URL dan mengakses data milik orang lain hanya dengan mengganti angkanya. TrackKas tidak rentan karena setiap pengambilan data selalu berangkat dari relasi milik user yang login, bukan dari ID mentah. Saya juga sudah menulis test khusus untuk memastikannya."

```php
// tests/Feature/TransactionTest.php
public function test_a_user_cannot_delete_another_users_transaction(): void
{
    $transaction = Transaction::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)
        ->delete(route('transaksi.destroy', $transaction))
        ->assertNotFound();

    $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
}
```

> "Test ini membuat transaksi milik user A, lalu mencoba menghapusnya sebagai user B. Harapannya 404, dan datanya tetap ada."

### 🔴 Q103. Kenapa 404 dan bukan 403?

> "404 berarti 'tidak ditemukan', 403 berarti 'ditemukan tapi kamu tidak boleh'. Saya memilih 404 karena lebih aman: kalau saya membalas 403, penyerang jadi tahu bahwa data dengan ID tersebut memang ada — itu membocorkan informasi. Dengan 404, dari sudut pandangnya, data itu seolah tidak pernah ada."

### Q104. Bagaimana kamu melindungi halaman profil?

> "Halaman profil ada di grup `Route::middleware('auth')->group(...)`. Jadi hanya bisa dibuka user yang sudah login. Untuk edit profil, validasi dan pengecekan email uniknya sudah disediakan Breeze."

### Q105. Bagaimana cara user logout?

> "Lewat tombol Keluar di bagian bawah sidebar. Itu form dengan method POST ke route `logout`, dilindungi `@csrf`. Setelah diproses, session dihapus dan user kembali ke halaman login."

### Q106. Kenapa logout pakai POST, bukan link GET?

> "Karena logout mengubah keadaan — menghapus session. Kalau pakai link GET, siapa pun bisa membuatmu logout hanya dengan menyisipkan gambar berarah ke `/logout` di halaman web. Dengan POST dan CSRF, itu tidak mungkin."

### Q107. Apa itu mass assignment vulnerability?

> "Celah saat Laravel mengisi kolom database dari array input mentah, lalu penyerang menyelipkan kolom yang tidak seharusnya. Contohnya input tersembunyi `is_admin=1`. Pencegahannya `$fillable`: hanya kolom yang terdaftar yang boleh diisi massal."

### Q108. Apa itu SQL injection? Apakah kamu rentan?

> "SQL injection adalah saat input user disisipkan langsung ke perintah SQL sehingga bisa mengubah maksud query. TrackKas aman karena semua query ditulis lewat Eloquent atau query builder, yang otomatis memakai **prepared statement** — nilai input dikirim terpisah dari perintah SQL-nya. Satu-satunya `selectRaw` yang saya pakai tidak berisi input dari user, jadi aman."

### Q109. Bagaimana kamu menampilkan output user supaya aman dari XSS?

> "Semua output di Blade saya tulis dengan kurung kurawal ganda, `{{ ... }}`. Blade otomatis meng-escape isinya, jadi kalau user mengetik tag HTML di kolom catatan, tag itu ditampilkan sebagai teks, tidak dijalankan sebagai kode. Kalau saya memakai `{!! !!}`, escaping-nya dimatikan — dan di TrackKas saya tidak memakainya sama sekali."

### Q110. Kalau user nakal mengetik `<script>alert(1)</script>` di kolom catatan?

> "Yang tersimpan di database adalah teks mentahnya, dan yang tampil di halaman juga teksnya, terlihat berantakan tapi tidak dijalankan. Itu hasil escaping otomatis dari Blade."

### Q111. Kenapa validasi dan `$fillable` perlu keduanya? Bukankah salah satu cukup?

> "Lapisannya berbeda. Validasi memeriksa **isi dan bentuk** data yang datang dari user. `$fillable` memeriksa **kolom mana** yang boleh diisi. Kalau hanya validasi, orang bisa mengirim field tambahan yang tidak saya validasi tapi tetap masuk lewat `create($request->all())`. Kalau hanya fillable, orang bisa mengirim angka nol atau jenis transaksi yang tidak dikenal. Keduanya menjaga hal yang berbeda."

### Q112. Apa yang terjadi kalau session user kedaluwarsa saat dia sedang mengisi form?

> "Saat form dikirim, middleware CSRF akan menemukan token yang sudah tidak valid, dan request ditolak dengan halaman 419. Ini perilaku yang benar: lebih baik menolak daripada menerima permintaan yang tidak bisa dipastikan asalnya."

---

# BAGIAN 7 — Blade, Layout & Komponen (Q113–Q132)

### 🔴 Q113. Apa itu Blade?

> "Mesin template milik Laravel. Semua file `.blade.php` diproses olehnya. Blade menyediakan cara ringkas untuk menulis PHP di dalam HTML: `{{ }}` untuk menampilkan data, `@if`, `@foreach`, dan `@include`. File Blade-nya dikompilasi menjadi PHP biasa lalu disimpan di `storage/framework/views`, jadi tidak ada beban tambahan saat aplikasi berjalan."

### 🔴 Q114. Jelaskan file `dashboard.blade.php`. Mulai dari mana?

> "Baris pertama `<x-app-layout>` — jadi seluruh halaman ini dibungkus kerangka aplikasi yang punya sidebar. Di dalamnya ada `<x-page-header>` untuk judul halaman, lalu grid dua kolom: kolom lebar berisi dua kartu total dan daftar transaksi terbaru, kolom sempit berisi kartu saldo dan dua daftar total per kategori."

### Q115. Apa arti `@props(['title', 'subtitle' => null])` di komponen `page-header`?

> "Itu deklarasi parameter komponen. Jadi komponen ini menerima `title` yang wajib, dan `subtitle` yang opsional dengan nilai default `null`. Kalau ada parameter yang dikirim tapi tidak dideklarasikan, Laravel menganggapnya sebagai HTML attribute biasa dan meneruskannya ke elemen."

### 🔴 Q116. Bagaimana cara memanggil komponen Blade?

```blade
<x-page-header title="Transaksi" subtitle="Riwayat semua transaksi Anda" />
<x-rupiah :value="$transaction->amount" />
```

> "Dengan tag `<x-nama>`. Nama file-nya ada di `resources/views/components/`. Jadi `<x-rupiah>` merujuk ke `components/rupiah.blade.php`. Tanda titik dua di depan `:value` berarti nilainya adalah ekspresi PHP, bukan teks biasa."

### 🔴 Q117. Apa bedanya `title="Transaksi"` dengan `:value="$amount"`?

> "Tanpa titik dua, isinya dianggap teks apa adanya, jadi `title="Transaksi"` ya berisi kata Transaksi. Dengan titik dua di depan, isinya dievaluasi sebagai PHP — jadi `:value="$transaction->amount"` mengirim nilai variabelnya, bukan teks '$transaction->amount'."

### 🔴 Q118. Kenapa kamu membuat komponen `<x-rupiah>`?

> "Karena format Rupiah saya butuh di banyak tempat: kartu dashboard, daftar transaksi, dan total per kategori. Kalau saya menulis `Rp ` dan `number_format` di setiap tempat, saat formatnya berubah saya harus memperbaiki di mana-mana. Komponen ini sumbernya satu:"

```blade
@props(['value' => 0])

Rp {{ number_format((float) $value, 0, ',', '.') }}
```

> "Tiga parameter `number_format`-nya artinya: nol angka di belakang koma, pemisah desimal koma, pemisah ribuan titik. Jadi 5000000 tampil sebagai Rp 5.000.000."

### 🔴 Q119. Apa itu `$slot`?

> "Slot itu tempat untuk isi yang diselipkan saat komponen dipanggil. Di layout `app.blade.php` ada `{{ $slot }}` — di situlah isi halaman dimasukkan. Jadi ketika saya menulis `<x-app-layout> ...isi halaman... </x-app-layout>`, semua yang ada di antaranya berpindah ke posisi `{{ $slot }}`."

### 🔴 Q120. Apa itu `$attributes->merge()`?

```blade
<div {{ $attributes->merge(['class' => 'mb-6']) }}>
```

> "`$attributes` berisi semua HTML attribute yang dikirim saat komponen dipanggil. `merge` menggabungkannya dengan nilai default. Jadi kalau saya memanggil `<x-page-header title="..." class="mt-10" />`, class dari pemanggil dan `mb-6` dari komponen akan digabung, bukan salah satu menimpa yang lain."

### 🔴 Q121. Kenapa kamu punya komponen `partials/form.blade.php`?

> "Karena bentuk form tambah dan form edit itu isi fieldnya identik. Daripada menulis ulang enam field di dua file, saya pisah jadi satu file dan panggil dengan `@include('transaksi.partials.form')`. Kalau nanti saya menambah field, cukup satu tempat."

### Q122. Apa bedanya `@include` dengan komponen `<x-...>`?

> "`@include` hanya menyisipkan file apa adanya, sederhana, cocok untuk potongan yang memang khusus satu halaman. Komponen `<x-...>` lebih lengkap: punya `@props` untuk deklarasi parameter, `$attributes` untuk menerima HTML attribute, dan bisa punya class PHP pendamping. Saya pakai `@include` untuk potongan form, dan komponen untuk hal yang dipakai lintas halaman seperti `x-rupiah` dan `x-app-layout`."

### 🔴 Q123. Bagaimana `<x-app-layout>` bisa tahu harus membuka file mana?

> "Ada class pendamping di `app/View/Components/AppLayout.php`. Class itu yang memberi tahu Laravel bahwa komponen `app-layout` dirender dari view `layouts.app`. Jadi yang menghubungkan nama komponen dengan file layout adalah class itu."

### Q124. Kenapa `layouts/app.blade.php` isinya HTML lengkap?

> "Karena dia dokumen utamanya: ada `<!DOCTYPE html>`, `<head>`, `<body>`, dan penutupnya. Halaman-halaman seperti `dashboard.blade.php` tidak punya tag HTML, karena mereka hanya mengisi `$slot` di dalam kerangka itu. Konsekuensinya, pemanggilan `@vite` dan font cukup ditulis sekali di layout."

### 🔴 Q125. Apa itu `@vite`?

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

> "Direktif yang menyisipkan file CSS dan JS hasil build dari Vite ke halaman. Saat mode pengembangan, dia menyisipkan skrip yang terhubung ke dev server supaya perubahan langsung terlihat tanpa refresh manual. Saat produksi, dia menunjuk ke file yang sudah dikompilasi di `public/build`."
> "Catatan penting: kalau build-nya belum dijalankan, `@vite` akan menampilkan pesan error. Jadi urutan setup-nya: `npm install`, lalu `npm run build`."

### Q126. Apa itu `{{ __('...') }}` di sekeliling teksmu?

> "Itu helper terjemahan. Teks di dalamnya diperlakukan sebagai kunci yang bisa diterjemahkan. Saya menggunakannya konsisten supaya kalau nanti aplikasinya perlu versi bahasa Inggris, tinggal menambahkan file terjemahannya, tanpa mengubah satu pun view. Kalau tidak ada file terjemahan, teks aslinya tetap ditampilkan seperti sekarang."

### 🔴 Q127. Kenapa pakai `@foreach` di view dan bukan di controller?

> "Perannya sudah berbeda. Controller tugasnya menyiapkan data, view tugasnya menampilkan. Saya sengaja tidak menyusun HTML di controller, karena kalau nanti tampilannya diubah, saya tidak perlu menyentuh logikanya."

### Q128. Kenapa di daftar transaksi ada `@if ($transactions->isEmpty())`?

> "Supaya keadaan kosong ditangani dengan baik. Kalau tidak ada transaksi, menampilkan tabel kosong dengan baris judul saja terlihat seperti error. Jadi saya ganti dengan pesan dan tombol ajakan menambah data. Ini yang sering disebut *empty state*."

### Q129. Apa itu `@php(...)` yang ada di dashboard?

```blade
@php($incomeMax = $incomeByCategory->max('total') ?: 1)
```

> "Cara menulis potongan PHP di dalam view. Di situ saya mencari nilai total terbesar untuk dipakai sebagai pembagi saat menghitung lebar bar. Tanda `?: 1` mencegah pembagian dengan nol kalau datanya kosong."

### Q130. Kenapa lebar bar-nya dihitung dengan `round($row->total / $incomeMax * 100)`?

> "Karena saya ingin bar terpanjang memenuhi lebar penuh, dan yang lain proporsional terhadapnya. Jadi saya jadikan total terbesar sebagai 100%, lalu yang lain dihitung sebagai persentasenya. Hasilnya diletakkan di atribut `style="width: ...%"`, karena nilainya berubah-ubah dan Tailwind hanya menyediakan class untuk nilai yang tetap."

### Q131. Kenapa `$loop->iteration` tidak kamu pakai untuk nomor baris?

> "Di daftar transaksi saya tidak menampilkan nomor baris, jadi tidak perlu. Di project latihan sebelumnya saya memakainya untuk kolom No. Kalau dibutuhkan di sini, tinggal ditambahkan — `$loop` otomatis tersedia di dalam `@foreach`."

### Q132. Apa yang terjadi kalau data yang dikirim controller tidak sesuai dengan yang dipakai view?

> "Aplikasinya melempar error 'undefined variable'. Karena itu di daftar transaksi saya menulis `{{ ($type ?? '') === $value }}`, memakai operator null coalescing supaya kalau `$type` tidak ada, halaman tetap tampil normal. Kebiasaan yang saya ambil setelah sempat menemui error itu."

---

# BAGIAN 8 — Tampilan, Tailwind & CSS (Q133–Q146)

### 🔴 Q133. Kamu pakai CSS framework apa?

> "Tailwind CSS versi 3, dipasang lewat PostCSS, dengan plugin `@tailwindcss/forms`. Saya pakai Tailwind karena gaya penulisannya langsung di HTML: setiap class mewakili satu properti CSS. Jadi saya tidak perlu membuat file CSS terpisah dan memikirkan nama class untuk setiap elemen. Untuk project yang tampilannya bervariasi seperti ini, itu lebih cepat daripada menulis CSS manual."

### 🔴 Q134. Apa bedanya Tailwind dengan Bootstrap?

> "Bootstrap menyediakan komponen jadi — ada class `btn`, `card`, `navbar`, dan hasilnya sudah berbentuk tombol atau kartu tertentu. Tailwind hanya menyediakan potongan properti satu per satu. Jadi di Bootstrap saya memilih komponen yang sudah ada, sedangkan di Tailwind saya menyusun tampilannya sendiri dari potongan-potongan kecil. Konsekuensinya Tailwind lebih fleksibel tapi tulisannya lebih panjang."

### 🔴 Q135. Kamu belajar Tailwind dari mana?

Jawab jujur:

> "Belum pernah diajari secara formal, Pak/Bu. Saya belajar dari dokumentasi resminya dan dari membaca kode template yang ada. Saya paham polanya: class Tailwind itu singkatan properti CSS, angka di belakangnya kelipatan 0.25rem, dan prefix seperti `lg:` atau `hover:` membuatnya aktif hanya dalam kondisi tertentu. Yang tidak saya kuasai adalah menghafal seluruh nama class, jadi saya masih sering membuka dokumentasi."

### 🔴 Q136. Tunjukkan satu contoh dan terjemahkan ke CSS biasa.

```html
<div class="rounded-xl border border-gray-100 bg-white px-5 py-4 shadow-sm">
```

```css
div {
    border-radius: 0.75rem;
    border: 1px solid #f3f4f6;
    background-color: #ffffff;
    padding-left: 1.25rem;
    padding-right: 1.25rem;
    padding-top: 1rem;
    padding-bottom: 1rem;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
}
```

> "Jadi sama saja, hanya ditulis di atribut class alih-alih di file CSS. Keuntungannya, saya tidak perlu membuka dua file untuk tahu tampilan satu elemen."

### 🔴 Q137. Kenapa kamu masih menulis CSS biasa di `resources/css/app.css`?

> "Untuk sidebar. Di situ ada CSS biasa dalam `@layer components` yang mengatur lebar sidebar, perubahan lebar saat ditutup, dan penyembunyian label. Saya memilih CSS biasa untuk bagian itu karena ada dua hal yang merepotkan kalau dipaksa pakai class Tailwind: pertama, saya butuh selector berdasarkan keadaan yang diletakkan di `<html data-sidebar>`, jadi harus mempengaruhi elemen anak; kedua, saya butuh aturan di dalam media query untuk layar besar."

```css
.app-sidebar {
    width: 100%;
    padding-left: 1.25rem;
    padding-right: 1.25rem;
}

@media (min-width: 1024px) {
    .app-sidebar {
        width: 270px;
        transition: width 200ms ease;
    }

    html[data-sidebar='collapsed'] .app-sidebar {
        width: 88px;
    }
}
```

> "Jadi pendekatan saya: bagian yang berulang atau butuh logika keadaan, saya taruh di CSS; sisanya Tailwind langsung di HTML."

### 🔴 Q138. Apa itu `@layer components`?

> "Cara menaruh CSS saya di lapisan yang benar dalam urutan Tailwind. Tailwind punya tiga lapisan: base untuk reset gaya, components untuk kelas yang bisa diulang, utilities untuk class seperti `p-4`. Kalau saya menaruh CSS di luar layer, dia bisa berada di posisi yang salah dalam urutan sehingga class Tailwind yang sederajat kalah atau menang secara tidak terduga. Dengan `@layer components`, urutannya konsisten."

### 🔴 Q139. Kenapa palet warnanya `brand-xxx` dan bukan `red-xxx`?

```js
// tailwind.config.js
colors: {
    brand: {
        50: '#fdf3f4',
        600: '#c81e2b',
        700: '#a81823',
        ...
    },
}
```

> "Karena saya butuh warna merah yang sesuai desain TrackKas, bukan merah bawaan Tailwind. Saya mendaftarkannya sebagai palet `brand` dari nuansa 50 sampai 900 di `tailwind.config.js`, jadi saya bisa memakai `bg-brand-600`, `text-brand-600`, `border-brand-300`. Keuntungannya, kalau warna brand berubah, saya hanya mengubah satu tempat di config dan seluruh aplikasi ikut menyesuaikan."

### Q140. Apa fungsi `600`, `700` itu?

> "Tingkat kegelapan. Angka kecil mendekati putih, angka besar mendekati hitam. `brand-50` saya pakai untuk latar lembut, `brand-600` untuk tombol utama, `brand-700` untuk keadaan saat kursor di atas tombol. Jadi menghasilkan efek terang-gelap yang konsisten tanpa saya harus memikirkan kodenya tiap kali."

### Q141. Apa itu `content` di `tailwind.config.js`?

```js
content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
],
```

> "Daftar file yang diperiksa Tailwind untuk mencari nama class yang dipakai. Tailwind hanya menghasilkan CSS untuk class yang benar-benar ditemukan di file-file itu, jadi hasil CSS-nya kecil. Kalau saya menulis class di file yang tidak terdaftar di sini, class itu tidak akan terbentuk. Itu kejadian yang paling sering menjebak pemula."

⚠️ Kalau penguji tanya "kenapa desainmu tidak berubah padahal saya lihat class-nya ada?", jawabannya ada di sini.

### 🔴 Q142. Kenapa ukuran layar berbeda-beda bisa ikut menyesuaikan? (responsive)

> "Saya pakai prefix ukuran layar di Tailwind, yaitu `sm:`, `md:`, `lg:`, dan `xl:`. Aturannya mobile first: class tanpa prefix berlaku untuk semua ukuran, dan class berprefix hanya aktif mulai ukuran itu ke atas. Contohnya `grid-cols-1 xl:grid-cols-12` di dashboard: di layar kecil satu kolom, di layar lebar jadi dua belas kolom yang saya bagi 8 dan 4. Sidebar juga mengikuti pola ini."

### Q143. Coba tunjukkan CSS media query-nya di project.

Ada di `app.css`:

```css
@media (min-width: 1024px) {
    .app-sidebar { width: 270px; }
}
```

> "1024px itu batas `lg` di Tailwind. Di bawah itu, sidebar tampil memenuhi lebar layar di atas konten. Di atas itu, sidebar jadi kolom tetap selebar 270 piksel dan menempel di sisi kiri."

### Q144. Apa itu plugin `@tailwindcss/forms`?

> "Plugin resmi Tailwind yang memberi gaya dasar yang konsisten untuk elemen form: input, select, textarea, checkbox, radio. Tanpa plugin itu, input bawaan browser bentuknya berbeda-beda dan sulit diseragamkan. Dengan plugin ini, saya tinggal menambahkan class warna dan ukuran."

### 🔴 Q145. Ada dua cara Tailwind dalam project, mana yang kamu pakai?

```css
/* resources/css/app.css — versi yang dipakai TrackKas, Tailwind v3 */
@tailwind base;
@tailwind components;
@tailwind utilities;
```

```css
/* versi baru, Tailwind v4 */
@import 'tailwindcss';
```

> "TrackKas memakai Tailwind v3, jadi penulisannya tiga direktif `@tailwind` di atas. Versi 4 menggantinya dengan satu baris `@import 'tailwindcss'` — itu yang ada di project latihan saya yang lain. Saya tahu bedanya karena pernah menemui error ketika kode dari satu versi dipakai di versi lain. Bukti TrackKas pakai v3 juga terlihat dari adanya `tailwind.config.js` dan `postcss.config.js`, yang memang cara kerja v3."

### Q146. Kalau saya minta kamu menghilangkan Tailwind dan pakai CSS biasa, bisa?

> "Bisa, tapi tidak saya sarankan. Seluruh tampilan aplikasi ini dibangun dari class Tailwind. Menggantinya berarti saya harus menulis ulang setiap file view dan memikirkan nama class untuk setiap elemen. Kalau yang diminta adalah bukti bahwa saya paham CSS dasarnya, saya bisa menunjukkan padanannya: satu baris class Tailwind berarti satu atau beberapa baris properti CSS."

---

# BAGIAN 9 — JavaScript, Alpine & Vite (Q147–Q154)

### 🔴 Q147. Apa fungsi JavaScript di projectmu?

> "Cuma satu: tombol buka-tutup sidebar. Selain itu tidak ada JavaScript khusus, karena semua logika masuk dan keluar data ditangani di sisi server lewat Laravel."

### 🔴 Q148. Kenapa pakai Alpine.js dan bukan jQuery atau React?

> "Alpine itu library kecil, ukurannya hanya beberapa kilobyte, dan cara pakainya cukup dengan menempel atribut di HTML — tidak perlu membuat komponen atau file build khusus seperti React. Untuk kebutuhan sebesar tombol buka-tutup sidebar, React jelas berlebihan. jQuery juga bisa, tapi Alpine lebih ringkas dan sintaksnya lebih deklaratif — saya cukup menulis `x-data` untuk keadaan dan `@click` untuk aksinya."

### 🔴 Q149. Tunjukkan kode Alpine-nya dan jelaskan.

```js
Alpine.data('sidebar', () => ({
    collapsed: document.documentElement.dataset.sidebar === 'collapsed',

    toggle() {
        this.collapsed = !this.collapsed;
        document.documentElement.dataset.sidebar = this.collapsed ? 'collapsed' : 'expanded';
        localStorage.setItem('trackkas-sidebar', document.documentElement.dataset.sidebar);
    },
}));
```

```blade
<aside x-data="sidebar">
    <button type="button" @click="toggle()" :aria-expanded="collapsed ? 'false' : 'true'">
```

> "`Alpine.data('sidebar', ...)` mendaftarkan potongan data yang bisa dipakai di HTML dengan menulis `x-data="sidebar"`. Di dalamnya ada satu keadaan, `collapsed`, dan satu aksi, `toggle()`. Saat tombol diklik, `@click` memanggil `toggle()`, lalu keadaan itu dibalik dan disimpan."
> "Yang menarik di sini: `toggle()` tidak menyimpan data di dalam dirinya sendiri, tapi menuliskan keadaan itu ke atribut `data-sidebar` pada elemen `<html>`. Dengan begitu, CSS bisa membaca keadaan yang sama."

### 🔴 Q150. Kenapa keadaannya ditaruh di `<html>`, tidak cukup di dalam elemen sidebar saja?

> "Karena CSS saya perlu menyembunyikan label, menyusutkan lebar, dan memindahkan tombol — semuanya elemen anak di dalam sidebar. Dengan keadaan berada di `<html>`, saya bisa menulis satu selector seperti `html[data-sidebar='collapsed'] .app-sidebar .sidebar-label` untuk mengatur semuanya sekaligus. Kalau keadaannya hanya di dalam sidebar, aturan CSS-nya jadi lebih rumit untuk elemen di luar sidebar."

### 🔴 Q151. Kenapa ada blok `<script>` di dalam `<head>`?

```html
<script>
    try {
        if (localStorage.getItem('trackkas-sidebar') === 'collapsed') {
            document.documentElement.setAttribute('data-sidebar', 'collapsed');
        }
    } catch (error) {}
</script>
```

> "Untuk mencegah kedipan tampilan. Skrip ini berjalan sebelum halaman digambar, jadi kalau user terakhir kali menutup sidebar, halamannya langsung tampil dalam keadaan tertutup. Kalau saya hanya mengandalkan Alpine, sidebar akan muncul terbuka dulu sesaat, baru menutup — itu terlihat seperti kesalahan."
> "`try` dan `catch` saya pakai karena `localStorage` bisa diblokir, misalnya di mode penyamaran. Kalau diblokir, aplikasinya tetap berjalan dan sidebar kembali ke keadaan terbuka."

### 🔴 Q152. Apa itu Vite dan kenapa perlu?

> "Vite adalah alat build. Tugasnya mengubah file CSS dan JS di folder `resources` menjadi file siap pakai di folder `public/build`, yang bisa dibuka browser. Tailwind sendiri tidak berjalan di browser, jadi dia harus dikompilasi dulu. Vite juga menangani hal-hal seperti memperkecil ukuran file saat produksi dan memberi versi pada nama file supaya browser tidak memakai cache lama."

### 🔴 Q153. Perintah apa yang kamu jalankan setiap kali mengubah tampilan?

```bash
npm install       # sekali saja, memasang dependensi JS
npm run dev       # saat mengembangkan, otomatis memperbarui tanpa refresh manual
npm run build     # saat selesai, menghasilkan file untuk produksi
```

> "Kalau saya hanya mengubah file Blade, biasanya tidak perlu build ulang, karena perubahan view tidak melewati Vite. Build ulang perlu kalau saya menambah class Tailwind baru atau menyentuh file CSS dan JS."

### Q154. Kenapa `@tailwindcss/vite` masih ada di `package.json` padahal kamu pakai Tailwind v3?

> "Itu sisa dari template awal project, Pak/Bu. Faktanya project ini berjalan di Tailwind v3, dan buktinya bisa dilihat di dua tempat: `postcss.config.js` memakai `tailwindcss` sebagai plugin PostCSS, dan `app.css` ditulis dengan direktif v3. Kalau diminta, saya bersihkan dependensi yang tidak terpakai itu supaya `package.json`-nya jujur."

---

# BAGIAN 10 — Testing (Q155–Q162)

### 🔴 Q155. Kamu menulis test? Kenapa?

> "Ya. Alasan utamanya dua. Pertama, saya yakin aplikasinya berjalan sesuai harapan — bukan sekadar 'kelihatannya jalan di browser saya'. Kedua, pengamanan seperti pembatasan data per user itu sulit dilihat langsung di browser, karena di browser saya selalu login sebagai satu orang. Test bisa menjalankan dua user sekaligus dan membuktikan user A tidak bisa menyentuh data user B."

### 🔴 Q156. Bagaimana cara menjalankannya?

```bash
php artisan test
```

> "Semua test berjalan di database SQLite di memori, bukan di database MySQL asli. Jadi test-nya cepat dan tidak pernah mengganggu data asli."

### 🔴 Q157. Jelaskan salah satu test-mu.

```php
public function test_the_list_shows_only_the_users_own_transactions(): void
{
    $user = User::factory()->create();

    Transaction::factory()->for($user)->create(['category' => 'Gaji']);
    Transaction::factory()->create(['category' => 'Belanja']);

    $this->actingAs($user)
        ->get(route('transaksi.index'))
        ->assertOk()
        ->assertSee('Gaji')
        ->assertDontSee('Belanja');
}
```

> "Saya membuat satu user, lalu dua transaksi: satu milik user itu, satu milik user lain. Setelah itu saya login sebagai user pertama dan membuka halaman daftar transaksi. Harapannya halaman terbuka normal, kategori 'Gaji' terlihat, dan 'Belanja' tidak terlihat. Test ini membuktikan pembatasan per user bekerja."

### 🔴 Q158. Apa itu `RefreshDatabase`?

> "Trait yang memastikan database bersih sebelum setiap test dijalankan, dengan menjalankan semua migrasi dari awal. Jadi satu test tidak mewarisi data dari test sebelumnya, dan hasilnya bisa diulang dengan hasil yang sama."

### 🔴 Q159. Apa itu factory?

> "Pencetak data palsu untuk keperluan testing. `User::factory()->create()` membuat satu user dengan data acak yang sah. Tanpa factory, saya harus menulis satu-satu nama, email, dan password di setiap test, dan itu panjang serta mudah salah."

### 🔴 Q160. Apa arti `->for($user)`?

> "Menetapkan pemilik relasinya secara ringkas. Kalau tanpa itu, saya harus menulis `['user_id' => $user->id]`. Dengan `for($user)`, Laravel yang mengisi foreign key-nya sesuai relasi yang terdaftar di model."

### 🔴 Q161. Total ada berapa test?

> "Empat file: `ExampleTest` untuk pengalihan halaman depan, `DashboardTest` untuk halaman dashboard dan pembatasan tamu, `TransactionTest` untuk CRUD transaksi dan pembatasan data antar user, serta `ProfileTest` bawaan Breeze untuk pengelolaan profil. Totalnya 36 test dengan 104 pemeriksaan, dan semuanya lulus. Saya bisa jalankan sekarang kalau Bapak/Ibu mau melihat."

### Q162. Kalau ada bug, bagaimana kamu menemukannya?

> "Alurnya: pertama saya bikin test yang menggambarkan perilaku yang seharusnya. Test itu pasti gagal. Lalu saya perbaiki kodenya sampai test-nya lulus. Setelah itu test itu tetap tersimpan, jadi kalau perubahan berikutnya merusak perilaku yang sama, test-nya langsung gagal. Cara itu membuat perbaikan lebih terarah dibanding menebak-nebak."

---

# BAGIAN 11 — 21 Pertanyaan Jebakan (J1–J21)

Bagian ini bukan soal nomor, tapi daftar pertanyaan yang sengaja dipakai penguji untuk menguji kejujuran dan pemahaman. Baca semuanya.

### ⚠️ J1. "Ini kan hasil AI, ya? Kamu paham kodenya?"

Jangan panik, jangan berbohong:

> "Saya memakai AI sebagai alat bantu belajar dan mempercepat penulisan, Pak/Bu. Tapi saya yang menentukan fiturnya, saya yang menguji jalannya satu per satu di browser, dan saya yang memperbaiki bagian yang tidak sesuai. Kalau diizinkan, saya bisa jelaskan alur kode mulai dari route sampai isi database, atau saya bisa ubah kodenya langsung di depan Bapak/Ibu."

Lalu **langsung gambarkan alur request** seperti di bagian 3 peta belajar. Bukti pemahaman lebih kuat dari pengakuan.

### ⚠️ J2. "Coba jelaskan baris ini." (penguji menunjuk baris acak)

Ambil napas. Baca dari dalam ke luar. Contoh: `$request->user()->transactions()->latest('date')->get()`.

> "Ini berantai. Dari dalam: `$request->user()` mengambil user yang login. Lalu `transactions()` memanggil relasi has-many. Lalu `latest('date')` mengurutkan dari tanggal terbaru. Terakhir `get()` menjalankan query-nya dan mengambil hasilnya sebagai kumpulan data."

Cara ini bekerja hampir untuk semua baris Eloquent. Hafalkan urutannya: **dari dalam, lalu baca ke kanan**.

### ⚠️ J3. "Kenapa tidak kamu pakai cara yang lebih sederhana? Contohnya `Transaction::all()`."

> "Kalau `Transaction::all()`, saya akan mengambil transaksi semua user, termasuk milik orang lain. Itu celah privasi. Makanya saya mulai dari relasi user yang login, supaya batas datanya sudah benar sejak awal, bukan disaring belakangan."

### ⚠️ J4. "Buktikan user A tidak bisa melihat data user B."

Demo hidup: buka `TransactionTest.php`, tunjuk test `test_a_user_cannot_delete_another_users_transaction`, lalu jalankan `php artisan test --filter=TransactionTest`. Tunjukkan hasilnya hijau.

### ⚠️ J5. "Kalau saya hapus kolom `user_id`, apa yang rusak?"

> "Beberapa hal. Relasi `hasMany` dan `belongsTo` kehilangan penghubungnya. Semua query yang berangkat dari `$request->user()->transactions()` jadi error. Foreign key-nya juga tidak bisa dibuat. Intinya, aplikasinya kembali jadi daftar global tanpa pemilik — dan itu berarti siapa pun bisa melihat transaksi siapa pun."

### ⚠️ J6. "Function `destroy`-mu menerima `$id`, tapi kalau saya kirim ID yang bukan transaksi milik user ini, apa yang terjadi?"

> "Laravel membalas 404, karena `findOrFail` dijalankan di dalam relasi user tersebut. Datanya tidak ditemukan, dan saya sengaja memakai 404 dan bukan 403 supaya keberadaan data orang lain tidak terbocor."

### ⚠️ J7. "Kamu tahu kelemahan projectmu sendiri?"

Jawab dengan daftar, jangan mengelak. Empat hal:

> "Pertama, belum ada fitur edit, jadi CRUD-nya belum lengkap. Kedua, daftar transaksi mengambil semua data sekaligus tanpa pembagian halaman — akan berat kalau datanya sudah ribuan. Ketiga, belum ada pencarian dan filter rentang tanggal. Keempat, halaman verifikasi email dan konfirmasi password masih tampilan bawaan Breeze."

### ⚠️ J8. "Apa perbedaan `$request->all()` dengan `$request->validate()`?"

> "`all()` mengambil seluruh kiriman mentah dari browser, termasuk field yang tidak saya inginkan. `validate()` memeriksa dan hanya mengembalikan field yang saya daftarkan dan lolos aturan. Jadi `validate()` lebih aman sekaligus lebih tegas."

### ⚠️ J9. "Kalau saya kirim `type=pengeluaran` padahal formnya cuma punya radio pemasukan, bisa?"

> "Kalau lewat browser dengan developer tools atau lewat Postman, bisa dikirim. Tapi akan ditolak validasi `in:pemasukan,pengeluaran`. Field itu akan dianggap bermasalah dan user dikembalikan ke form dengan pesan error."

### ⚠️ J10. "Apa yang terjadi kalau dua user mengirim data bersamaan?"

> "Tidak masalah, karena setiap baris punya ID sendiri dan `user_id` yang jelas. Tidak ada tabrakan data. Yang bisa terasa adalah urutan tampilannya, dan itu sudah saya atasi dengan mengurutkan berdasarkan tanggal lalu ID, sehingga urutannya tetap pasti."

### ⚠️ J11. "Kamu pakai `query()->when()` itu dari mana belajarnya?"

> "Dari dokumentasi Laravel, Pak/Bu. Awalnya saya menulis dengan `if` biasa, tapi setelah tahu ada `when`, saya pindahkan supaya alur query-nya terbaca sebagai satu rangkaian. Kalau versi `if` lebih mudah dibaca, saya bisa kembalikan ke bentuk itu."

### ⚠️ J12. "Kenapa teksnya bahasa Indonesia tapi kodenya bahasa Inggris?"

> "Saya pisahkan: teks yang dilihat user dalam bahasa Indonesia, sedangkan nama variabel, method, dan tabel tetap bahasa Inggris mengikuti konvensi Laravel. Alasannya, konvensi punya peran teknis — misalnya `belongsTo(User::class)` mencari kolom bernama `user_id`. Kalau saya ubah ke bahasa Indonesia, banyak konvensi itu harus saya tulis manual."

### ⚠️ J13. "Kalau saya minta tambah field baru hari ini, bisa?"

> "Bisa. Urutannya: tambah kolom di migrasi, tambah aturan validasi di `store`, tambah field di `transaksi/partials/form.blade.php`, dan tambah kolom di tabel daftar transaksi. Empat tempat, kurang lebih sepuluh menit."

⚠️ Hafalkan urutan empat tempat itu. Pertanyaan ini sering dipakai untuk menguji apakah kamu benar-benar tahu alur datanya.

### ⚠️ J14. "Apa yang terjadi kalau `APP_DEBUG` dimatikan?"

> "Saat dikembangkan, `APP_DEBUG=true` supaya pesan error ditampilkan lengkap beserta jejak kodenya. Untuk aplikasi yang sudah dipakai orang lain, harus dimatikan, karena pesan error yang lengkap bisa membocorkan struktur database dan lokasi file. Di mode itu, pengguna hanya melihat halaman 'Server Error'."

### ⚠️ J15. "Kenapa judul tab browser-mu masih 'Laravel'?"

Kalau penguji menemukannya, jawab langsung tanpa gugup:

> "Betul, Pak/Bu, itu memang belum saya ubah. Nama aplikasinya ada di file `.env` pada baris `APP_NAME`, dan masih bernilai Laravel. Seharusnya `APP_NAME=TrackKas`, lalu `php artisan config:clear`. Itu satu baris perbaikan yang belum saya kerjakan."

⚠️ **Saran: perbaiki ini sebelum sidang.** Buka `.env`, ubah `APP_NAME=Laravel` menjadi `APP_NAME=TrackKas`, lalu jalankan `php artisan config:clear`.

### ⚠️ J16. "Kamu bisa jelaskan apa itu HTTP 419 dan 429?"

> "419 terjadi saat token CSRF tidak valid atau sudah kedaluwarsa — biasanya karena sesi habis. 429 artinya terlalu banyak permintaan. Di project ini 429 muncul di route verifikasi email dan kirim ulang notifikasi, karena Breeze memberi batas enam kali per menit."

### ⚠️ J17. "Ada celah keamanan lain yang kamu sadari?"

> "Untuk skala project ini, perlindungan yang saya sadari sudah terpasang: validasi input, pembatasan data per user, CSRF, escaping output, dan password ber-hash. Yang belum ada adalah pembatasan jumlah percobaan login untuk akun secara khusus, pencatatan aktivitas atau log audit, dan verifikasi email karena masih dinonaktifkan di model User."

### ⚠️ J18. "Bagian mana dari project ini yang paling kamu banggakan?"

> "Bagian dashboard. Di situ saya tidak hanya menampilkan data apa adanya, tapi mengolahnya: menjumlahkan per jenis, menghitung saldo, mengelompokkan per kategori, lalu menampilkan perbandingannya sebagai bar. Bagian itu yang paling banyak saya kerjakan ulang sampai hasilnya masuk akal."

### ⚠️ J19. "Bagian mana yang paling kamu kesulitan?"

> "Bagian membatasi data per user. Awalnya saya mengira cukup memfilter di tampilan. Setelah belajar, saya baru sadar bahwa pengambilan datanya sendiri harus dibatasi di level query — kalau tidak, orang bisa mengganti ID di URL. Makanya sekarang semua query berangkat dari relasi user yang login, dan saya buat test khusus untuk memastikannya."

### ⚠️ J20. "Kalau saya bilang projectmu belum selesai, kamu setuju?"

> "Setuju, Pak/Bu, untuk standar aplikasi yang dipakai publik. Yang selesai adalah alur intinya: pengguna bisa mencatat dan melihat riwayat keuangannya. Yang belum ada saya sudah sebutkan sebelumnya: edit, pencarian, pembagian halaman, verifikasi email, dan pengaturan ulang tampilan beberapa halaman bawaan."

---

### ⚠️ J21. "Apa bedanya dengan Notepad? Kan bisa dicatat di catatan biasa."

Ini pertanyaan yang hampir pasti muncul, dan jawabannya punya dokumen sendiri: **[jawaban-vs-notepad-dan-excel.md](jawaban-vs-notepad-dan-excel.md)**. Baca dokumen itu utuh.

**Versi 30 detik untuk dihafal:**

> "Betul, Pak/Bu, untuk sekadar mencatat Notepad memang cukup. Bedanya ada di **setelah** mencatatnya. Di Notepad, angka totalnya adalah tulisan yang saya ketik sendiri — begitu saya menambah satu baris, angka itu jadi basi dan harus saya hitung ulang manual. Di TrackKas, total tidak saya simpan di database sama sekali; angka itu dihitung ulang dari datanya setiap halaman dibuka, jadi tidak mungkin basi. Jadi perbedaannya bukan di tempat mencatat, tapi di apa yang bisa dilakukan setelah data itu ada."

**Tiga hal yang wajib kamu ingat:**

1. **Akui dulu.** Jangan langsung berdebat. Bilang "untuk mencatat, Notepad memang cukup" — itu membuatmu terdengar tenang, bukan kalah.
2. **Sebut satu pembeda, bukan lima.** Yang terkuat: total di TrackKas adalah hasil hitungan, bukan tulisan yang bisa basi.
3. **Demokan.** Buka Notepad, tambah satu baris, tunjukkan totalnya jadi salah. Lalu tambah satu transaksi di TrackKas dan tunjukkan dashboard-nya menyesuaikan sendiri.

⚠️ **Siapkan diri untuk tingkat lanjutnya.** Kalau penguji naik level dan bilang *"kalau begitu kenapa bukan Excel? Kan ada SUM dan filter"* — jangan jawab *"Excel tidak bisa menjumlahkan"*, itu salah dan kamu akan terbantah. Jawab: **"Excel bisa menghitung, tapi aturannya ada di dalam file dan bisa rusak kalau sel rumusnya terhapus. Di TrackKas, aturannya ada di database dan validasi, jadi datanya tidak bisa jadi tidak sah."** Detail lengkapnya ada di dokumen khusus itu.

---

# BAGIAN 12 — Kalau Kamu Tidak Tahu Jawabannya

Ini bagian yang paling sering bikin nilai jatuh, padahal sebenarnya paling gampang diselamatkan. Ucapkan salah satu dari ini dengan tenang:

**Kalau benar-benar belum pernah dengar:**
> "Mohon maaf, untuk bagian itu saya belum tahu, Pak/Bu. Saya tidak mau menebak dan memberi informasi yang salah. Kalau diizinkan, saya catat dan pelajari setelah ini."

**Kalau tahu sedikit tapi tidak yakin:**
> "Sejauh yang saya pahami, fungsinya seperti ini ... (jelaskan yang kamu tahu). Tapi saya belum yakin detailnya, jadi kalau Bapak/Ibu bisa mengoreksi, saya catat."

**Kalau ditanya hal yang ada di project tapi kamu lupa:**
> "Izinkan saya membukanya sebentar untuk memastikan." — membuka file itu wajar. Yang tidak boleh adalah diam lama sambil menebak.

**Kalau ditanya istilah yang tidak ada di projectmu:**
> "Istilah itu belum saya pakai di TrackKas, Pak/Bu. Yang saya pakai untuk kebutuhan itu adalah ..."

**Kalau penguji mengoreksi dan kamu memang salah:**
> "Terima kasih koreksinya. Betul, seharusnya seperti itu. Saya akan perbaiki setelah ini." — lalu diam, jangan berdebat. Sikap menerima koreksi nilainya lebih tinggi daripada argumen.

**Yang tidak boleh diucapkan:**
- "Itu dari AI, saya nggak tahu."
- "Kan cuma project latihan."
- "Itu salah, tapi tetap jalan kok."
- "Kayaknya ..." berulang-ulang tanpa dasar.

---

# BAGIAN 13 — Skenario Demo Hidup

Penguji biasanya menyuruhmu mendemokan aplikasinya. Latih urutan ini sampai lancar tanpa melihat catatan.

### Persiapan sebelum masuk ruangan

```bash
cd C:/laragon/www/trackkasdev
php artisan migrate          # pastikan struktur database paling baru
npm run build                # pastikan CSS & JS terkompilasi
php artisan serve            # jalankan aplikasi
```

Buka `http://127.0.0.1:8000` di browser. Cek dulu satu kali: halaman login tampil, bisa login, dashboard muncul. Jangan demokan tanpa pernah mencoba run terlebih dahulu.

### Urutan demo (7 langkah, sekitar 4 menit)

1. **Buka halaman depan.** Tunjukkan bahwa `/` mengarah ke login, bukan halaman selamat datang bawaan Laravel.
   > "Halaman depan saya arahkan ke login, karena aplikasinya tidak bisa dipakai tanpa akun."

2. **Daftar akun baru.** Isi nama, email, password, konfirmasi password, klik Daftar.
   > "Setelah mendaftar, user langsung masuk ke dashboard."

3. **Tunjukkan dashboard yang masih kosong.** Tunjuk pesan "Belum ada transaksi".
   > "Saya sengaja membiarkannya kosong untuk user baru, supaya tidak ada data palsu. Keadaan kosongnya saya beri pesan dan tombol ajakan supaya tidak terasa rusak."

4. **Tambah satu pemasukan.** Klik Tambah Transaksi, pilih Pemasukan, isi kategori "Gaji", isi jumlah 5000000, simpan. Tunjuk pesan hijau "Transaksi berhasil ditambahkan."
   > "Pesan ini flash message — muncul sekali dan hilang setelah halaman dimuat ulang."

5. **Tambah satu pengeluaran.** Kategori "Makanan & Minuman", jumlah 25000.

6. **Kembali ke dashboard.** Tunjuk Total Pemasukan Rp 5.000.000, Total Pengeluaran Rp 25.000, Saldo Rp 4.975.000, dan bar Pemasukan per Kategori.
   > "Angka-angka ini hasil perhitungan langsung dari database, bukan angka yang saya tulis manual di tampilan."

7. **Buka daftar transaksi, coba filter, lalu hapus satu.** Tunjukkan konfirmasi sebelum hapus dan pesan "Transaksi berhasil dihapus.", lalu kembali ke dashboard dan tunjukkan angkanya berubah.

### Demo pembuktian keamanan (30 detik, ini yang bikin penguji terkesan)

Buka terminal:

```bash
php artisan test --filter=TransactionTest
```

> "Test ini membuktikan user tidak bisa menghapus transaksi milik user lain. Aplikasinya membalas 404 dan datanya tetap ada."

### Kalau ada yang error saat demo

Jangan panik. Katakan:

> "Mohon tunggu sebentar, sepertinya ada yang perlu saya cek."

Lalu lihat pesan errornya. Langkah paling umum:
- Halaman putih tanpa gaya → `npm run build` belum dijalankan.
- Error "table not found" → `php artisan migrate` belum dijalankan.
- Token tidak cocok atau 419 → muat ulang halaman login.
- Perubahan tidak terlihat → `php artisan config:clear` atau `php artisan view:clear`.

Hafalkan empat penyebab itu. Sembilan dari sepuluh error saat demo berasal dari situ.

---

# BAGIAN 14 — Kartu Contekan Terakhir

Hafalkan **sepuluh kalimat** ini. Kalau kamu bisa mengucapkannya lancar, sebagian besar pertanyaan sudah tercakup.

1. **Alur satu request:** "Route menentukan URL, middleware memeriksa, controller memproses lewat model, lalu mengembalikan view."
2. **Kenapa bukan `all()`:** "Karena `all()` mengambil data semua user. Saya mulai dari relasi user yang login supaya batas datanya benar sejak awal."
3. **Kenapa bukan `$request->all()`:** "Karena itu mengambil semua kiriman browser. Saya pakai `validate()` yang hanya mengembalikan field yang saya daftarkan dan lolos aturan."
4. **Kenapa kurung di `transactions()`:** "Dengan kurung, hasilnya query builder yang belum dijalankan, jadi saya masih bisa menambahkan filter. Tanpa kurung, datanya langsung diambil."
5. **Kenapa `findOrFail`:** "Supaya kalau datanya tidak ada langsung jadi 404, dan karena dijalankan di dalam relasi user, ID milik orang lain otomatis tidak ditemukan."
6. **Kenapa hapus pakai DELETE dan form:** "Karena `GET` hanya untuk menampilkan. Aksi yang mengubah data harus lewat form POST atau DELETE supaya terlindungi CSRF dan tidak bisa terpicu tanpa sengaja."
7. **Apa itu Blade:** "Mesin template Laravel. Kurung kurawal ganda untuk menampilkan data dengan escape otomatis, `@if` dan `@foreach` untuk alur, dan `<x-nama>` untuk memanggil komponen."
8. **Apa itu Tailwind:** "Kumpulan class yang masing-masing mewakili satu properti CSS, ditulis langsung di HTML. Prefix seperti `lg:` dan `hover:` membuatnya aktif hanya pada kondisi tertentu."
9. **Apa fungsi Alpine di projectmu:** "Mengatur buka-tutup sidebar. Keadaannya saya simpan di atribut `data-sidebar` pada elemen `<html>` supaya bisa dibaca CSS."
10. **Apa yang belum selesai:** "Belum ada edit transaksi, belum ada pencarian dan pembagian halaman, dan dua halaman bawaan Breeze belum saya sesuaikan tampilannya."

---

## Penutup

Kelebihanmu di sidang bukan pada banyaknya fitur, tapi pada **kejelasan penjelasan**. Project yang sederhana tapi bisa dijelaskan dari halaman browser sampai perintah SQL-nya akan selalu lebih baik nilainya daripada project besar yang pemiliknya tidak tahu cara kerjanya.

Target realistis: kuasai **Bagian 1 sampai Bagian 6** sampai bisa dijelaskan sambil menunjuk kode. Bagian 7 sampai 10 cukup kamu pahami garis besarnya dan tahu di file mana harus membukanya kalau ditanya.
