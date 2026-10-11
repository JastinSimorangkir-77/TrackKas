# Kalau Penguji Bertanya: "Apa Bedanya dengan Notepad?"

**"Kan bisa dicatat di Notepad atau catatan HP."**

Ini pertanyaan pembunuh yang hampir pasti muncul, dan banyak mahasiswa jatuh di sini karena mulai membela diri dengan menyebutkan daftar fitur. Padahal yang sebenarnya diuji bukan fiturnya.

---

## 1. Apa yang sebenarnya sedang diuji

Penguji **tidak sedang mencari daftar fitur**. Dia sedang menguji tiga hal:

| Yang sebenarnya diuji | Artinya |
|---|---|
| **Apakah kamu tahu masalah apa yang kamu selesaikan?** | Kalau kamu tidak bisa membedakan alatmu dari Notepad, berarti kamu belum memikirkan masalahnya |
| **Apakah kamu tahu batas kemampuan alatmu?** | Mahasiswa yang mengklaim aplikasinya bisa segalanya lebih mencurigakan daripada yang tahu kelemahannya |
| **Apakah kamu tenang saat dipertanyakan?** | Reaksi panik saat ditanya "kenapa repot-repot?" langsung terlihat |

**Dan ini yang paling penting:** kalau kamu mulai berdebat "Notepad tidak bisa ini-itu", kamu sedang berada di posisi bertahan. Cara keluar dari posisi itu bukan menambah argumen, tapi **mengakui dulu sebagian** — lalu memindahkan percakapan ke tempat yang lebih kuat.

**Kesalahan yang harus dihindari:**
- ❌ "Ya kan bisa ganti warna, Pak." → penguji akan bilang "di Notepad juga bisa atur font".
- ❌ "Ada tampilan sidebar-nya." → tidak ada hubungannya dengan nilai guna.
- ❌ Diam dan gugup.
- ❌ "Soalnya tugasnya disuruh bikin aplikasi." → jawaban paling melemahkan.

---

## 2. Jawaban 30 detik — hafalkan ini

> "Betul, Pak/Bu. Untuk sekadar mencatat, Notepad memang cukup — saya sendiri juga sering pakai. Bedanya ada di **setelah** mencatatnya.
>
> Di Notepad, catatannya teks mati. Kalau saya isi sepuluh transaksi, total pemasukan dan pengeluaran harus saya jumlahkan sendiri, dan begitu saya menambah satu baris baru, angka total di bawahnya jadi **basi** — saya harus menghitung ulang dari awal. Salah ketik satu digit pun tidak ada yang memberi tahu.
>
> Di TrackKas, jumlah itu **dihitung ulang setiap halaman dibuka**. Saya tidak menyimpan angka totalnya di mana pun; saya menyimpan transaksinya, dan ringkasannya dihasilkan dari data itu. Jadi totalnya tidak mungkin basi.
>
> Jadi perbedaannya bukan di tempat mencatat, Pak/Bu, tapi di **apa yang bisa dilakukan setelah data itu ada**: dihitung, disaring, dikelompokkan, dan ditampilkan ulang."

**Tiga hal yang membuat jawaban ini kuat:**
1. Kamu **mengakui** kelebihannya dulu — itu menunjukkan kamu tidak defensif.
2. Kamu menyebut **satu** pembeda konkret (total tidak mungkin basi), bukan lima yang dangkal.
3. Kalimat terakhir memindahkan perdebatan dari "menyimpan" ke "mengolah" — dan di situ jawabannya jelas.

---

## 3. Kalau ditanya lanjut: delapan pembeda

Siapkan ini. Tapi **jangan dibacakan semua** — sebut dua atau tiga saja yang paling relevan dengan pertanyaan lanjutannya.

| # | Aspek | Notepad / catatan biasa | TrackKas |
|---|---|---|---|
| 1 | **Perhitungan** | Dihitung manual, angkanya bisa basi | `sum('amount')` dihitung ulang setiap kali halaman dibuka |
| 2 | **Struktur data** | Teks bebas, tidak bisa diolah mesin | Setiap transaksi punya kolom bertipe: `type`, `date`, `amount`, `category` |
| 3 | **Pemeriksaan input** | Menerima apa pun, termasuk angka salah | Validasi menolak input tidak sah, dengan batas minimal dua lapis |
| 4 | **Penyaringan** | Cari manual dengan mata atau Ctrl+F | Satu query: `where('type', 'pemasukan')` |
| 5 | **Pengelompokan** | Tidak bisa, harus dikelompokkan manual | `groupBy('category')` + total per kategori |
| 6 | **Akses** | Terikat satu file di satu perangkat | Diakses lewat browser dari HP maupun laptop |
| 7 | **Keamanan** | Siapa pun yang memegang perangkat bisa membaca | Butuh login, dan setiap query dibatasi pada pemiliknya |
| 8 | **Riwayat waktu** | Tidak terekam | `created_at` dan `updated_at` otomatis dari Laravel |

### Penjelasan singkat tiap pembeda

**1. Perhitungan — ini pembeda terkuat.**
Di Notepad, "Total: Rp 5.000.000" itu **tulisan yang kamu ketik sendiri**. Artinya:
- Begitu kamu menambah satu transaksi, angka itu salah sampai kamu hitung ulang manual.
- Kalau kamu lupa satu baris saat menjumlahkan, tidak ada yang memberi tahu.
- Setelah 100 transaksi, menjumlahkan ulang manual jadi pekerjaan berat — dan itu pekerjaan yang harus diulang setiap kali ada data baru.

Di TrackKas, tidak ada satu pun angka total yang saya simpan di database. Yang saya simpan hanya transaksinya. Total itu **hasil hitungan**:

```php
$income  = auth()->user()->transactions()->where('type', 'pemasukan')->sum('amount');
$expense = auth()->user()->transactions()->where('type', 'pengeluaran')->sum('amount');
```

> "Jadi kalau ada yang bertanya kok totalnya berubah padahal saya tidak mengedit angka totalnya — memang begitu seharusnya. Angka itu bukan data, angka itu hasil hitungan dari data."

**Kalimat ini bagus banget untuk diucapkan di sidang.** Bisa kamu tunjukkan langsung: hapus satu transaksi di daftar, lalu buka dashboard dan tunjukkan totalnya langsung menyesuaikan tanpa kamu sentuh.

**2. Struktur data.**
Di Notepad, `5000000` dan `pemasukan` dan `2026-09-20` semuanya teks, tidak bisa dibedakan mesin. Di TrackKas, kolom `amount` bertipe `decimal(15,2)`, kolom `date` bertipe `date`, kolom `type` hanya menerima dua nilai. Artinya **database ikut menjaga datanya**, bukan hanya percaya pada ketikan manusia.

**3. Pemeriksaan input.**
Ini ada di dua lapis. Lapis pertama validasi di controller:

```php
$request->validate([
    'type'   => ['required', 'in:pemasukan,pengeluaran'],
    'amount' => ['required', 'numeric', 'min:0'],
    'date'   => ['required', 'date'],
]);
```

Lapis kedua tipe kolom di database. Contoh nyatanya: kalau di Notepad kamu salah ketik nilai jadi `500000000` (kelebihan satu nol), tidak ada yang menegur — kamu baru sadar sebulan kemudian saat totalnya aneh. Di TrackKas, angkanya tetap masuk tapi **langsung terlihat** di dashboard karena saldonya melonjak sendiri.

**4 & 5. Penyaringan dan pengelompokan.**
Coba saja catat 100 transaksi di Notepad, lalu jawab pertanyaan ini: *"berapa total pengeluaran saya untuk kategori Makanan bulan September?"* Di Notepad itu butuh waktu lama dan sangat mungkin salah. Di TrackKas itu tiga baris query, dan sudah ada di halaman dashboard.

**6. Akses.**
Notepad itu file di satu komputer. Kalau kamu ada di luar rumah dan ingin mencatat pengeluaran, datanya tidak ada di HP-mu. Dan kalau file itu rusak atau HP-nya hilang, datanya hilang.

**7. Keamanan — dan ini yang sering dilupakan orang.**
Catatan keuangan itu **data sensitif**. Di Notepad, siapa pun yang membuka laptopmu bisa membaca seluruh isi catatanmu. Di TrackKas, harus login dulu; dan bukan hanya butuh login, tapi setiap pengambilan data dibatasi pada pemiliknya:

```php
$request->user()->transactions()->findOrFail($id)->delete();
```

> "Jadi bukan hanya 'harus login', Pak/Bu. Query-nya sendiri sudah terbatas pada data milik user yang login. Kalau saya mengganti ID di URL menjadi milik orang lain, hasilnya 404."

**8. Riwayat waktu.**
Setiap baris di TrackKas otomatis punya `created_at` dan `updated_at` dari `$table->timestamps()`. Artinya bisa diketahui kapan data itu dicatat. Notepad tidak punya itu.

---

## 4. Jangan berhenti di Notepad — lawan berikutnya lebih berat: Excel

Kalau gurunya teliti, setelah kamu menjawab soal Notepad, pertanyaan berikutnya pasti naik:

> **"Kalau begitu, kenapa bukan Excel? Kan ada rumus SUM, ada filter, ada pivot."**

**Ini pertanyaan yang lebih sulit, dan kamu harus siap.** Excel sudah bisa menjawab poin 1, 4, dan 5 di atas. Jadi jangan menjawab dengan "Excel tidak bisa menjumlahkan" — itu salah dan kamu akan terbantah.

**Jawaban yang benar:**

> "Betul, Pak/Bu, Excel sudah bisa menjumlahkan dan memfilter. Jadi bedanya bukan di perhitungan.
>
> Bedanya di **siapa yang menjaga aturannya**. Di Excel, aturan itu ada di rumus yang saya tulis sendiri di dalam file. Kalau satu sel rumusnya terhapus, atau ada baris yang disisipkan saat menyalin data, seluruh perhitungannya bisa salah tanpa saya sadar. Excel juga tidak menolak saya mengetik 'pemasuakan' di kolom jenis, atau mengetik nama di kolom jumlah.
>
> Di TrackKas, aturan itu **ada di luar file**: di validasi dan di tipe kolom database. Jadi datanya tidak bisa jadi tidak sah, siapa pun yang mengisinya. Kalau saya kirim `type` berisi teks selain dua nilai yang sah, data itu ditolak sebelum masuk.
>
> Tambahan lagi, aplikasi web bisa diakses dari HP tanpa harus membuka file, dan setiap akun punya datanya sendiri — jadi bisa dipakai lebih dari satu orang tanpa saling menimpa."

**Lima pembeda terhadap Excel, ringkas:**

| | Excel / Spreadsheet | TrackKas |
|---|---|---|
| Aturan data | Rumus yang bisa terhapus/tergeser | Validasi + tipe kolom database |
| Salah ketik jenis | Diterima apa adanya | Ditolak |
| Akses | File, biasanya satu perangkat | Browser, HP maupun laptop |
| Multi-user | Satu file, rawan tertimpa | Tiap akun terpisah, dibatasi di query |
| Integritas relasi | Tidak ada | Foreign key + `cascadeOnDelete` |

**Dan kalau penguji membuat versi sederhana dari versi sederhana: "kenapa tidak Google Forms + Spreadsheet?"**

> "Itu juga bisa, Pak/Bu. Tapi itu memindahkan masalahnya ke tempat lain: datanya harus diolah manual di spreadsheet, dan tetap tidak ada validasi maupun pembatasan antar pengguna. Kalau tujuan saya hanya menyimpan catatan, itu cukup. TrackKas saya buat untuk sampai ke tahap mengolah dan menampilkan kembali."

---

## 5. Demo pembuktian 60 detik — ini yang membuat jawabanmu kredibel

Kata-kata saja tidak cukup. **Buktikan.** Lakukan ini kalau penguji menantang:

**Langkah 1 (15 detik).** Buka Notepad. Ketik tiga baris:

```
20 Sep  Gaji          +5000000
21 Sep  Makan         -25000
22 Sep  Transport     -15000

Total: Rp 4.960.000   <- kamu hitung manual, dan tulis manual
```

**Langkah 2 (15 detik).** Katakan: *"Sekarang saya tambah satu transaksi baru."* Ketik baris keempat:

```
23 Sep  Kopi          -18000
```

> "Perhatikan, total di bawahnya sekarang **salah**. Saya harus menghitung ulang manual. Dan tidak ada yang memberi tahu saya bahwa angka itu sudah tidak berlaku lagi."

**Langkah 3 (30 detik).** Pindah ke TrackKas. Tambah satu transaksi baru di halaman Tambah Transaksi, lalu buka dashboard.

> "Di TrackKas, saya tidak pernah mengedit angka totalnya. Saya hanya menambah transaksi. Dan totalnya menyesuaikan sendiri, karena dihitung dari datanya saat halaman ini dibuka."

**Ini pembuktian yang meyakinkan** karena penguji melihatnya sendiri, bukan mendengar klaimmu. Dan kalau butuh lebih kuat lagi: hapus satu transaksi di halaman daftar, lalu buka dashboard — angkanya berubah lagi tanpa kamu sentuh.

---

## 6. Kalau penguji mendesak: "Jadi, Notepad cukup dong?"

Jangan mengelak. Jawab dengan jujur dan tenang:

> "Untuk kebutuhan mencatat sederhana, cukup, Pak/Bu. Saya kira tidak ada yang akan bilang tidak.
>
> Yang berubah adalah ketika catatannya sudah banyak dan saya mulai butuh menjawab pertanyaan seperti 'bulan ini saya habis berapa untuk makan?' atau 'pengeluaran terbesar saya apa?'. Di titik itu catatan teks berhenti membantu, karena saya tidak bisa mengolahnya.
>
> Jadi saya tidak bilang Notepad salah, Pak/Bu. Saya bilang alatnya beda untuk kebutuhan yang beda."

**Jawaban ini justru meninggikan nilaimu**, karena menunjukkan kamu bisa menilai alatmu sendiri secara wajar. Mahasiswa yang mengklaim aplikasinya menggantikan segalanya terdengar tidak berpengalaman.

---

## 7. Kalau ditanya: "Lha, kan sudah ada aplikasi kayak gitu?"

Jawaban jujur dan tetap kuat:

> "Betul, Pak/Bu, banyak. Ada yang khusus untuk UMKM seperti BukuWarung, ada juga fitur perencanaan keuangan di aplikasi bank digital.
>
> Justru itu bukti bahwa masalahnya nyata, bukan saya karang-karangkan. Kalau mencatat di Notepad sudah cukup, aplikasi-aplikasi itu tidak akan punya pengguna.
>
> Dan untuk project ini, saya membangunnya sebagai penerapan materi: struktur database relasional, satu relasi antar tabel, alur CRUD penuh, validasi, dan pembatasan data per pengguna."

**Perhatikan bagian terakhir.** Untuk **sidang akademik**, alasan "saya membangunnya untuk menerapkan materi" itu **sah dan jujur**. Sidang bukan ajang pitching startup. Jadi sampaikan dua-duanya: nilai gunanya **dan** tujuan pembelajarannya.

⚠️ Tapi jangan **mengawali** dengan alasan pembelajaran. Kalau kalimat pertamamu adalah "ini tugas sekolah", nilainya jatuh. **Buka dengan masalahnya, tutup dengan tujuan pembelajarannya.**

---

## 8. Satu hal yang harus kamu akui sendiri (dan ini bikin kamu terlihat tajam)

Semua pembeda di atas benar, tapi ada satu kelemahan yang **jujur harus kamu sadari**, dan justru berkaitan langsung dengan pertanyaan Notepad:

**Kolom `category` di TrackKas masih teks bebas.**

Nilai dari kolom itu diambil langsung dari input user:

```php
'category' => ['required', 'string', 'max:255'],
```

Artinya user bisa mengetik "Makanan", "makanan", "Makanan ", dan "Makanan & Minuman". Dari sisi sistem, itu **empat kategori berbeda**, dan pengelompokan di dashboard akan menampilkannya sebagai empat baris terpisah.

Jadi kalau penguji bertanya *"bagaimana kamu mencegah data tidak konsisten seperti di Notepad?"*, jawaban jujurnya:

> "Untuk jenis transaksi sudah aman, karena dibatasi aturan `in:pemasukan,pengeluaran`. Tapi untuk kategori saya harus jujur: masih teks bebas, jadi penulisan yang berbeda masih bisa dianggap kategori berbeda. Perbaikannya nanti adalah memindahkan kategori ke tabelnya sendiri, lalu di form diubah jadi dropdown pilihan — sehingga tidak ada lagi penulisan bebas.
> Saya sadar itu masih kekurangan, Pak/Bu, dan itu bagian yang akan saya kerjakan."

**Ini jawaban yang bagus banget.** Kamu menunjukkan bahwa kamu tahu kelemahan sistemmu **bahkan pada hal detail yang mungkin tidak dilihat penguji**. Itu tanda kamu benar-benar mengerjakan projectmu.

---

## 9. Kartu hafalan — empat kalimat inti

Hafalkan empat kalimat ini. Kalau kamu bisa mengucapkannya dengan tenang, pertanyaan ini beres.

1. **"Untuk sekadar mencatat, Notepad cukup. Bedanya ada di setelah mencatatnya."**
   — mengakui dulu, lalu membuka arah pembicaraan yang lebih kuat.

2. **"Di Notepad, totalnya adalah tulisan yang bisa basi. Di TrackKas, totalnya dihitung ulang dari datanya setiap kali halaman dibuka."**
   — pembeda terkuat, dan bisa langsung kamu demokan.

3. **"Notepad menerima apa pun. TrackKas menolak data yang tidak sah, dan aturannya ada di database, bukan cuma di tampilan."**
   — menjawab "kenapa ada validasi?"

4. **"Kalau Excel sudah bisa menjumlahkan, yang belum ada di sana adalah aturannya — dan aturan itu ada di luar file, sehingga datanya tidak bisa jadi tidak sah."**
   — penyangga kalau pertanyaannya naik ke tingkat Excel.

---

## 10. Yang jangan kamu ucapkan

| Jangan | Kenapa |
|---|---|
| "Ya kan bisa ganti warna / ada sidebar-nya" | Itu kosmetik, bukan nilai guna. Penguji langsung mengabaikannya |
| "Notepad tidak bisa dipakai buat hitung" | Salah. Kalkulator ada di mana-mana, dan kamu akan terbantah |
| "Di Notepad tidak bisa diakses online" | Benar, tapi lemah — itu cuma soal lokasi file, bukan nilai |
| "Karena tugasnya disuruh membuat aplikasi" | Mengatakan projectmu tidak punya alasan, hanya kewajiban |
| "Bisa dicatet di HP" (mengalihkan ke catatan HP) | Sama saja, itu tetap catatan teks |
| "Iya sih Pak, sama aja" | Menyerah. Kamu sudah punya jawabannya — ucapkan |

---

## 11. Ringkasan satu layar

| Pertanyaan | Inti jawabanmu |
|---|---|
| "Bedanya apa dengan Notepad?" | "Notepad cukup untuk mencatat. Bedanya di mengolah datanya setelah tercatat." |
| "Notepad kan bisa dijumlahkan manual" | "Bisa, tapi totalnya tulisan yang bisa basi, dan tidak ada yang memberitahu kalau salah." |
| "Kalau begitu pakai Excel saja?" | "Excel bisa menghitung, tapi aturan datanya ada di dalam file dan bisa rusak. Di TrackKas aturannya ada di database." |
| "Jadi Notepad cukup dong?" | "Untuk kebutuhan sederhana, cukup. Yang berubah saat catatannya banyak dan butuh diolah." |
| "Kan sudah banyak aplikasi seperti ini" | "Betul, dan itu bukti masalahnya nyata. Saya membangunnya sebagai penerapan materi." |
| "Apa kelemahan sistemmu?" | "Kategori masih teks bebas, jadi penulisan berbeda dianggap kategori berbeda. Perbaikannya pakai tabel kategori dan dropdown." |

---

## Penutup

Pertanyaan ini sebenarnya **bukan ujian tentang Notepad**. Ini ujian apakah kamu tahu:

- **Masalah apa** yang kamu selesaikan
- **Untuk siapa** aplikasimu berguna
- **Di mana batas** kemampuan aplikasimu

Karena itu, cara menjawab yang paling aman adalah: **akui dulu, sebutkan satu pembeda yang bisa kamu buktikan, lalu demokan.** Jangan menghafal tujuh alasan dan membacakannya semua — penguji akan mendengar itu sebagai hafalan, bukan pemahaman.

Satu pembeda yang kamu buktikan sendiri di depan penguji lebih kuat daripada sepuluh alasan yang diucapkan.
