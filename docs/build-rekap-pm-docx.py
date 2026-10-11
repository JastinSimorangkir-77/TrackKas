"""
Membuat dokumen rekap project TrackKas untuk Product Manager (.docx).

Jalankan dari akar project:
    python docs/build-rekap-pm-docx.py

Hasil: docs/TrackKas-Rekap-Project-untuk-PM.docx
"""

import sys

from docx import Document
from docx.enum.section import WD_SECTION
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.shared import Pt, RGBColor, Cm

# Path keluaran bisa diganti lewat argumen pertama, berguna kalau berkas aslinya
# sedang terbuka di Word (Windows mengunci berkas yang sedang dibuka).
OUT = sys.argv[1] if len(sys.argv) > 1 else "docs/TrackKas-Rekap-Project-untuk-PM.docx"

BRAND = RGBColor(0xB3, 0x1B, 0x22)
GREY = RGBColor(0x55, 0x55, 0x55)

# ---------------------------------------------------------------- helpers


def shade(cell, hex_fill):
    """Beri warna latar pada satu sel tabel."""
    el = OxmlElement("w:shd")
    el.set(qn("w:val"), "clear")
    el.set(qn("w:color"), "auto")
    el.set(qn("w:fill"), hex_fill)
    cell._tc.get_or_add_tcPr().append(el)


def add_page_number(paragraph):
    """Sisipkan field nomor halaman ke dalam paragraf."""
    run = paragraph.add_run()
    begin = OxmlElement("w:fldChar")
    begin.set(qn("w:fldCharType"), "begin")
    instr = OxmlElement("w:instrText")
    instr.set(qn("xml:space"), "preserve")
    instr.text = "PAGE"
    end = OxmlElement("w:fldChar")
    end.set(qn("w:fldCharType"), "end")
    run._r.append(begin)
    run._r.append(instr)
    run._r.append(end)


def table(doc, header, rows, widths=None, font=9):
    """Buat tabel bergaris dengan baris judul berwarna."""
    t = doc.add_table(rows=1, cols=len(header))
    try:
        t.style = "Light Grid Accent 1"
    except KeyError:
        t.style = "Table Grid"

    t.alignment = WD_TABLE_ALIGNMENT.CENTER

    for i, text in enumerate(header):
        cell = t.rows[0].cells[i]
        cell.text = ""
        run = cell.paragraphs[0].add_run(text)
        run.bold = True
        run.font.size = Pt(font)
        run.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
        shade(cell, "B31B22")

    for row in rows:
        cells = t.add_row().cells
        for i, text in enumerate(row):
            cells[i].text = ""
            run = cells[i].paragraphs[0].add_run(str(text))
            run.font.size = Pt(font)

    if widths:
        for row in t.rows:
            for i, w in enumerate(widths):
                row.cells[i].width = Cm(w)

    doc.add_paragraph()
    return t


def para(doc, text, italic=False, size=None, color=None, space_after=6):
    p = doc.add_paragraph()
    run = p.add_run(text)
    run.italic = italic
    if size:
        run.font.size = Pt(size)
    if color:
        run.font.color.rgb = color
    p.paragraph_format.space_after = Pt(space_after)
    return p


def bullets(doc, items):
    for it in items:
        doc.add_paragraph(it, style="List Bullet")


def bullets_bold_lead(doc, items):
    """Butir dengan bagian awal ditebalkan: ('Judul', 'penjelasan')."""
    for lead, rest in items:
        p = doc.add_paragraph(style="List Bullet")
        r = p.add_run(lead)
        r.bold = True
        p.add_run(rest)


def steps(doc, items):
    for it in items:
        doc.add_paragraph(it, style="List Number")


def note(doc, text):
    p = doc.add_paragraph()
    run = p.add_run(text)
    run.italic = True
    run.font.size = Pt(9)
    run.font.color.rgb = GREY
    p.paragraph_format.space_after = Pt(10)


# ---------------------------------------------------------------- dokumen

doc = Document()

# Gaya dasar: Calibri 10.5 supaya dokumennya ringkas dibaca
normal = doc.styles["Normal"]
normal.font.name = "Calibri"
normal.font.size = Pt(10.5)
normal.paragraph_format.space_after = Pt(6)

section = doc.sections[0]
section.top_margin = Cm(2)
section.bottom_margin = Cm(2)
section.left_margin = Cm(2.2)
section.right_margin = Cm(2.2)

footer = section.footer.paragraphs[0]
footer.alignment = WD_ALIGN_PARAGRAPH.CENTER
fr = footer.add_run("TrackKas - Rekapitulasi Project untuk Product Manager  |  ")
fr.font.size = Pt(8)
fr.font.color.rgb = GREY
add_page_number(footer)
for r in footer.runs:
    r.font.size = Pt(8)
    r.font.color.rgb = GREY

props = doc.core_properties
props.title = "TrackKas - Rekapitulasi Project untuk Product Manager"
props.author = "Tim TrackKas"
props.subject = "Rekap status produk, fitur, keterbatasan, dan rekomendasi roadmap"
props.language = "id-ID"

# ---------------------------------------------------------------- halaman judul

title = doc.add_paragraph()
title.alignment = WD_ALIGN_PARAGRAPH.CENTER
tr = title.add_run("TrackKas")
tr.bold = True
tr.font.size = Pt(34)
tr.font.color.rgb = BRAND
title.paragraph_format.space_after = Pt(0)

sub = doc.add_paragraph()
sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
sr = sub.add_run("Rekapitulasi Project untuk Product Manager")
sr.font.size = Pt(15)
sub.paragraph_format.space_after = Pt(2)

sub2 = doc.add_paragraph()
sub2.alignment = WD_ALIGN_PARAGRAPH.CENTER
s2 = sub2.add_run("Aplikasi pencatatan keuangan pribadi berbasis web")
s2.italic = True
s2.font.size = Pt(11)
s2.font.color.rgb = GREY
sub2.paragraph_format.space_after = Pt(24)

info = doc.add_table(rows=0, cols=2)
for label, value in [
    ("Jenis dokumen", "Rekap status produk (bukan dokumen teknis)"),
    ("Nama produk", "TrackKas"),
    ("Kategori produk", "Personal finance / pencatatan keuangan pribadi"),
    ("Platform", "Web (responsif, dapat dipakai di HP maupun desktop)"),
    ("Bahasa antarmuka", "Indonesia"),
    ("Mata uang", "Rupiah (IDR) - belum multi-currency"),
    ("Tahap saat ini", "MVP fungsional, berjalan di lingkungan lokal (belum di-deploy)"),
    ("Basis kode", "trackkasdev (Laravel)"),
    ("Tanggal rekap", "8 Oktober 2026"),
]:
    cells = info.add_row().cells
    r1 = cells[0].paragraphs[0].add_run(label)
    r1.bold = True
    r1.font.size = Pt(10)
    cells[1].paragraphs[0].add_run(value).font.size = Pt(10)
    cells[0].width = Cm(4.5)
    cells[1].width = Cm(11.5)

doc.add_page_break()

# ---------------------------------------------------------------- 1

doc.add_heading("1. Ringkasan Eksekutif", level=1)

para(
    doc,
    "TrackKas adalah aplikasi web pencatat keuangan pribadi. Pengguna mencatat setiap pemasukan "
    "dan pengeluaran sebagai satu transaksi, lalu aplikasi menghitung sendiri saldo, total pemasukan, "
    "total pengeluaran, rekap per kategori, statistik 12 bulan terakhir, dan laporan berkala dalam "
    "bentuk PDF. Setiap akun hanya melihat datanya sendiri.",
)

para(
    doc,
    "Secara status, produk ini berada di tahap MVP fungsional: seluruh alur inti dari daftar akun "
    "sampai mengunduh laporan sudah bisa dipakai, dilindungi login, dan ditutup oleh 69 pengujian "
    "otomatis yang semuanya lulus. Yang belum ada adalah hal-hal pelengkap yang justru paling terasa "
    "di pemakaian sehari-hari: mengubah transaksi yang sudah dicatat, kategori terkelola, dan "
    "pagination pada daftar transaksi.",
)

doc.add_heading("Poin utama untuk keputusan", level=2)
bullets_bold_lead(
    doc,
    [
        ("Siap dipakai secara internal. ", "Semua fitur inti berfungsi dan teruji; cocok untuk uji coba terbatas dengan pengguna nyata."),
        ("Belum siap rilis publik. ", "Aplikasi masih berjalan lokal (belum ada server produksi, domain, maupun konfigurasi email untuk reset password)."),
        ("Keputusan teknis yang membatasi produk: ", "daftar transaksi memuat seluruh baris sekaligus tanpa pagination, sehingga dibatasi 2.000 transaksi per akun."),
        ("Yang sudah kuat dan layak dijaga: ", "validasi di sisi server, pemisahan data antar akun, dan perhitungan saldo yang selalu dihitung ulang dari data mentah sehingga tidak mungkin basi."),
    ],
)

# ---------------------------------------------------------------- 2

doc.add_heading("2. Profil Produk", level=1)

doc.add_heading("2.1 Masalah yang dipecahkan", level=2)
para(
    doc,
    "Mencatat keuangan dengan cara manual (buku catatan, Notepad, atau spreadsheet) gagal di satu titik: "
    "setelah mencatat, angkanya cepat menjadi basi. Total di buku catatan adalah tulisan yang harus dihitung "
    "ulang sendiri setiap kali ada baris baru; di spreadsheet, rumus bisa terhapus atau bergeser dan datanya "
    "tetap bisa masuk walau tidak sah.",
)

doc.add_heading("2.2 Nilai jual utama (differentiator)", level=2)
bullets_bold_lead(
    doc,
    [
        ("Saldo dan total selalu dihitung ulang dari data. ", "Angka ringkasan tidak disimpan di database, melainkan dihitung setiap halaman dibuka. Total tidak mungkin basi."),
        ("Aturan data ditegakkan di server. ", "Transaksi yang tidak sah tidak bisa masuk ke database, bukan sekadar dicegah di tampilan."),
        ("Isolasi data per akun. ", "Semua query diambil dari relasi milik user yang sedang login, sehingga akun lain tidak bisa melihat, mengubah, maupun menghapus data kita."),
        ("Laporan siap kirim. ", "Rekap harian, mingguan, bulanan, dan tahunan bisa langsung diunduh sebagai PDF lengkap dengan saldo awal, saldo akhir, dan saldo berjalan per baris."),
    ],
)

doc.add_heading("2.3 Target pengguna", level=2)
table(
    doc,
    ["Persona", "Kebutuhan utama", "Skenario pemakaian"],
    [
        ("Individu / pekerja", "Tahu uang pergi ke mana setiap bulan", "Mencatat harian, menutup bulan dengan laporan bulanan PDF"),
        ("Mahasiswa / perantau", "Mengontrol pengeluaran di batas anggaran", "Catat pengeluaran harian, lihat rekap kategori terbesar"),
        ("Pelaku usaha kecil", "Memisahkan uang usaha dan pribadi", "Satu akun untuk usaha, memakai laporan bulanan sebagai bukti"),
        ("Pengguna awam", "Tidak mau belajar aplikasi keuangan rumit", "Satu klik tambah transaksi, angka langsung terlihat di dashboard"),
    ],
    widths=[4.0, 5.5, 6.5],
)
note(
    doc,
    "Catatan: persona di atas adalah asumsi awal berdasarkan ruang lingkup fitur yang sudah dibangun, "
    "bukan hasil riset pengguna. Riset awal disarankan menjadi salah satu langkah sebelum rilis publik.",
)

# ---------------------------------------------------------------- 3

doc.add_heading("3. Fitur yang Sudah Ada", level=1)
note(
    doc,
    "Status: Selesai = berfungsi penuh dan tercakup pengujian otomatis. Dasar = berfungsi, "
    "tetapi masih kasar atau minim pengaturan. Belum = belum dikerjakan.",
)

doc.add_heading("3.1 Akun dan autentikasi", level=2)
table(
    doc,
    ["Fitur", "Perilaku pengguna", "Status"],
    [
        ("Daftar akun", "Pengguna membuat akun baru dengan nama, email, dan password.", "Selesai"),
        ("Login / logout", "Masuk dan keluar dari sesi.", "Selesai"),
        ("Verifikasi email", "Akun harus memverifikasi email sebelum bisa membuka dashboard dan transaksi.", "Selesai"),
        ("Lupa & reset password", "Alur bawaan tersedia (minta tautan, ganti password lewat tautan).", "Dasar"),
        ("Ganti password", "Ganti password dari halaman profil.", "Selesai"),
        ("Hapus akun", "Pengguna bisa menghapus akunnya sendiri; seluruh transaksinya ikut terhapus.", "Selesai"),
    ],
    widths=[4.5, 9.0, 2.5],
)
note(
    doc,
    "Reset password sudah tersedia sebagai alur, tetapi pengiriman emailnya memerlukan konfigurasi SMTP "
    "agar berfungsi di server produksi. Tanpa itu, pengguna yang lupa password tidak bisa masuk kembali.",
)

doc.add_heading("3.2 Dashboard", level=2)
table(
    doc,
    ["Fitur", "Perilaku pengguna", "Status"],
    [
        ("Total pemasukan & pengeluaran", "Dua kartu ringkasan berisi total sepanjang waktu.", "Selesai"),
        ("Saldo", "Kartu saldo = total pemasukan dikurangi total pengeluaran.", "Selesai"),
        ("Transaksi terbaru", "Lima transaksi terakhir ditampilkan; tombol Lihat Semua ke daftar lengkap.", "Selesai"),
        ("Statistik 12 bulan", "Tabel per bulan (pemasukan, pengeluaran, selisih) dengan penanda bulan ini.", "Selesai"),
        ("Rekap per kategori", "Dua panel kategori (pemasukan & pengeluaran) dengan batang proporsi.", "Selesai"),
        ("Kartu Tentang TrackKas", "Penjelasan singkat produk dan batas penyimpanan akun.", "Selesai"),
        ("Empty state", "Saat belum ada data, dashboard menampilkan ajakan menambah transaksi.", "Selesai"),
    ],
    widths=[4.5, 9.0, 2.5],
)

doc.add_heading("3.3 Transaksi", level=2)
table(
    doc,
    ["Fitur", "Perilaku pengguna", "Status"],
    [
        ("Tambah transaksi", "Form berisi jenis (pemasukan/pengeluaran), tanggal, kategori, jumlah, dan catatan opsional.", "Selesai"),
        ("Daftar transaksi", "Tabel seluruh transaksi milik akun, terbaru di atas.", "Selesai"),
        ("Filter jenis", "Pilihan Semua / Pemasukan / Pengeluaran.", "Selesai"),
        ("Hapus transaksi", "Hapus satu baris dengan konfirmasi lebih dahulu.", "Selesai"),
        ("Batas penyimpanan", "Akun dibatasi 2.000 transaksi; form ditutup dan diganti pesan bila penuh.", "Selesai"),
        ("Pesan konfirmasi", "Notifikasi setelah berhasil menambah atau menghapus.", "Selesai"),
        ("Pagination & pencarian", "Memuat daftar per halaman; mencari berdasarkan kata kunci.", "Belum"),
        ("Urut / sortir kolom", "Mengurutkan tabel berdasarkan tanggal, kategori, atau jumlah.", "Belum"),
    ],
    widths=[4.5, 9.0, 2.5],
)

doc.add_heading("3.4 Laporan PDF", level=2)
table(
    doc,
    ["Fitur", "Perilaku pengguna", "Status"],
    [
        ("Pilihan periode", "Harian, mingguan (Senin-Minggu), bulanan, atau tahunan.", "Selesai"),
        ("Tanggal acuan", "Pengguna memilih tanggal; aplikasi menentukan rentang periodenya.", "Selesai"),
        ("Ringkasan periode", "Saldo awal periode, total pemasukan, total pengeluaran, saldo akhir.", "Selesai"),
        ("Rincian transaksi", "Setiap baris lengkap dengan saldo berjalan, sehingga tidak perlu dihitung manual.", "Selesai"),
        ("Rekap per kategori", "Pemasukan dan pengeluaran per kategori di dalam berkas.", "Selesai"),
        ("Nama berkas otomatis", "Contoh: Laporan-Keuangan-Bulanan-2026-10-01.pdf.", "Selesai"),
        ("Periode kosong tetap berjalan", "Laporan tanpa transaksi tetap bisa diunduh dan menyatakan tidak ada data.", "Selesai"),
        ("Pratinjau di web", "Melihat laporan di browser sebelum mengunduh.", "Belum"),
        ("Ekspor CSV / Excel", "Mengunduh data mentah untuk diolah lebih lanjut.", "Belum"),
    ],
    widths=[4.5, 9.0, 2.5],
)

doc.add_heading("3.5 Profil dan tampilan", level=2)
table(
    doc,
    ["Fitur", "Perilaku pengguna", "Status"],
    [
        ("Edit profil", "Mengubah nama dan email.", "Selesai"),
        ("Halaman profil", "Ringkasan akun dan akses cepat ke pengaturan.", "Selesai"),
        ("Sidebar dapat dikecilkan", "Menu kiri bisa dilipat; pilihan diingat browser.", "Selesai"),
        ("Desain responsif", "Layout menyesuaikan layar HP maupun desktop.", "Selesai"),
        ("Antarmuka berbahasa Indonesia", "Seluruh teks aplikasi dan laporan berbahasa Indonesia.", "Selesai"),
    ],
    widths=[4.5, 9.0, 2.5],
)

# ---------------------------------------------------------------- 4

doc.add_heading("4. Alur Pengguna Utama", level=1)

doc.add_heading("4.1 Pengguna baru", level=2)
steps(
    doc,
    [
        "Membuka aplikasi, langsung diarahkan ke halaman masuk.",
        "Mendaftar dengan nama, email, dan password.",
        "Memverifikasi email melalui tautan yang dikirim.",
        "Masuk ke dashboard yang masih kosong, dengan ajakan menambah transaksi pertama.",
        "Mencatat transaksi pertama, lalu melihat angka di dashboard berubah.",
    ],
)
note(
    doc,
    "Titik kritis: langkah verifikasi email bergantung pada pengiriman email yang belum dikonfigurasi. "
    "Untuk demo atau uji coba internal, verifikasi email perlu diaktifkan lebih dahulu.",
)

doc.add_heading("4.2 Mencatat transaksi (alur tersering)", level=2)
steps(
    doc,
    [
        "Dari dashboard atau halaman Transaksi, klik Tambah Transaksi.",
        "Memilih jenis (pemasukan / pengeluaran) dan mengisi tanggal, kategori, jumlah, serta catatan bila perlu.",
        "Klik Simpan Transaksi.",
        "Aplikasi memeriksa keabsahan data di server; bila ada yang salah, pengguna dikembalikan ke form dengan pesannya.",
        "Bila sah, data disimpan untuk akun tersebut dan daftar transaksi diperbarui dengan pesan konfirmasi.",
    ],
)

doc.add_heading("4.3 Memeriksa kondisi keuangan", level=2)
steps(
    doc,
    [
        "Membuka dashboard.",
        "Membaca kartu total pemasukan, total pengeluaran, dan saldo.",
        "Melihat lima transaksi terakhir dan tabel 12 bulan terakhir untuk menilai tren.",
        "Melihat dua panel kategori untuk mengetahui ke mana uang paling banyak pergi.",
    ],
)

doc.add_heading("4.4 Mengunduh laporan", level=2)
steps(
    doc,
    [
        "Membuka halaman Transaksi.",
        "Pada panel Unduh Laporan PDF, memilih periode dan tanggal acuan.",
        "Klik Unduh PDF.",
        "Aplikasi menyusun dan mengunduh berkas, sekaligus menampilkan peringatan bila pilihan periode tidak dikenali.",
    ],
)

# ---------------------------------------------------------------- 5

doc.add_heading("5. Arsitektur dan Teknologi", level=1)
note(
    doc,
    "Bagian ini ditujukan untuk memberi gambaran singkat. Rincian teknis penuh ada di dokumentasi pengembang.",
)

table(
    doc,
    ["Lapisan", "Teknologi", "Versi", "Relevansi untuk produk"],
    [
        ("Kerangka kerja", "Laravel", "13.33.0", "Menyediakan routing, validasi, dan database sehingga pengembangan cepat"),
        ("Bahasa", "PHP", "8.3", "Banyak tersedia di hosting Indonesia"),
        ("Database (produksi)", "MySQL", "-", "Data tersimpan terpusat di server"),
        ("Database (pengujian)", "SQLite", "-", "Pengujian otomatis berjalan cepat tanpa server database"),
        ("Tampilan", "Blade + Tailwind CSS", "Tailwind 3.4", "Tampilan konsisten, responsif, tanpa file CSS terpisah"),
        ("Interaksi", "Alpine.js", "3.17", "Interaksi ringan seperti tombol lipat sidebar"),
        ("Aset & build", "Vite", "8.3", "Menggabungkan dan mengoptimalkan CSS serta JavaScript"),
        ("Autentikasi", "Laravel Breeze", "2.4", "Login, daftar, verifikasi email, reset password memakai paket resmi"),
        ("Laporan PDF", "dompdf", "3.1", "Menghasilkan berkas PDF laporan di sisi server"),
        ("Pengujian", "PHPUnit", "12.5", "69 pengujian otomatis, 286 pemeriksaan, semuanya lulus"),
        ("Gaya kode", "Laravel Pint", "1.32", "Menjaga konsistensi penulisan kode antar pengembang"),
    ],
    widths=[3.4, 3.6, 2.0, 7.0],
)

doc.add_heading("5.1 Alur satu permintaan pengguna", level=2)
para(
    doc,
    "Aplikasi mengikuti pola standar Model-View-Controller: permintaan pengguna masuk lewat daftar "
    "alamat (route), diperiksa oleh penjaga login, diproses oleh pengontrol (controller) yang memvalidasi "
    "data, disimpan atau diambil lewat model ke database, lalu disusun menjadi tampilan (Blade).",
)
note(
    doc,
    "Konsekuensi produk: validasi dan aturan bisnis berada di server, sehingga tidak bisa dilewati "
    "pengguna melalui manipulasi tampilan browser.",
)

# ---------------------------------------------------------------- 6

doc.add_heading("6. Model Data", level=1)

para(doc, "Struktur data inti hanya terdiri atas dua tabel yang saling terhubung.")

doc.add_heading("6.1 Tabel users", level=2)
bullets(
    doc,
    [
        "id - nomor unik akun",
        "name - nama pengguna",
        "email - alamat email, unik per akun",
        "email_verified_at - waktu verifikasi email",
        "password - password terenkripsi",
        "remember_token - untuk fitur ingat saya",
        "created_at / updated_at - waktu pembuatan dan perubahan",
    ],
)

doc.add_heading("6.2 Tabel transactions", level=2)
table(
    doc,
    ["Kolom", "Isi", "Aturan"],
    [
        ("id", "Nomor unik transaksi", "Otomatis"),
        ("user_id", "Pemilik transaksi", "Menunjuk ke users; ikut terhapus bila akun dihapus"),
        ("type", "Jenis transaksi", "Hanya pemasukan atau pengeluaran"),
        ("category", "Nama kategori", "Teks bebas, maksimal 50 karakter"),
        ("amount", "Jumlah uang", "Angka desimal; 0 sampai 999.999.999.999,99"),
        ("date", "Tanggal transaksi", "Wajib berupa tanggal yang sah"),
        ("note", "Catatan tambahan", "Opsional, maksimal 255 karakter"),
        ("created_at / updated_at", "Jejak waktu", "Otomatis"),
    ],
    widths=[3.6, 5.4, 7.0],
)

doc.add_heading("6.3 Relasi", level=2)
bullets(
    doc,
    [
        "Satu akun (user) memiliki banyak transaksi - satu-ke-banyak.",
        "Satu transaksi hanya dimiliki oleh satu akun.",
        "Semua tampilan data diambil melalui kepemilikan akun yang sedang login, sehingga pemisahan data bersifat wajib, bukan sekadar tampilan.",
    ],
)

# ---------------------------------------------------------------- 7

doc.add_heading("7. Aturan Bisnis yang Sudah Diterapkan", level=1)
table(
    doc,
    ["Aturan", "Nilai", "Alasan"],
    [
        ("Batas transaksi per akun", "2.000 transaksi", "Daftar transaksi memuat seluruh baris sekaligus; batas menjaga halaman tetap cepat"),
        ("Jumlah minimal", "Rp 0", "Nominal negatif tidak masuk akal dalam pencatatan ini"),
        ("Jumlah maksimal", "Rp 999.999.999.999,99", "Dibatasi sedikit di bawah kapasitas penyimpanan agar pengguna mendapat pesan yang jelas"),
        ("Panjang kategori", "50 karakter", "Menjaga daftar kategori tetap terbaca"),
        ("Panjang catatan", "255 karakter", "Keterangan singkat, bukan deskripsi panjang"),
        ("Jenis transaksi", "pemasukan / pengeluaran", "Mencegah nilai di luar dua jenis tersebut"),
        ("Visibilitas data", "Hanya milik akun sendiri", "Data keuangan bersifat pribadi"),
        ("Akses setelah login", "Dashboard & transaksi wajib login dan email terverifikasi", "Mengurangi akun sampah dan spam"),
        ("Periode laporan", "Harian, mingguan (Senin-Minggu), bulanan, tahunan", "Siklus pelaporan yang umum di Indonesia"),
        ("Maksimal 12 bulan statistik", "12 bulan terakhir", "Menjaga tabel dashboard tetap ringan dan mudah dibaca"),
        ("Pemilik laporan PDF", "Identitas akun yang mengunduh", "Laporan dapat dipakai sebagai dokumen pribadi"),
    ],
    widths=[4.2, 3.8, 8.0],
)

# ---------------------------------------------------------------- 8

doc.add_heading("8. Kualitas dan Kesiapan Teknis", level=1)

table(
    doc,
    ["Pemeriksaan", "Hasil", "Arti untuk produk"],
    [
        ("Pengujian otomatis", "69 lulus dari 69", "Tidak ada fungsi inti yang rusak saat ini"),
        ("Jumlah pemeriksaan", "286 assertion", "Kedalaman pemeriksaan cukup, bukan sekadar halaman terbuka"),
        ("Contoh cakupan", "Tambah/hapus transaksi, batas akun, filter, dashboard, statistik bulanan, keempat periode laporan, izin antar akun", "Alur kritis terlindungi dari regresi"),
        ("Keamanan antar akun", "Diuji: pengguna tidak bisa melihat atau menghapus transaksi akun lain", "Pemenuhan kebutuhan dasar privasi data keuangan"),
        ("Status deployment", "Belum dideploy", "Perlu persiapan server, domain, dan konfigurasi lingkungan"),
    ],
    widths=[3.6, 4.4, 8.0],
)

note(
    doc,
    "Catatan teknis yang berdampak produk: pengujian otomatis berjalan di atas SQLite, sementara aplikasi "
    "dijalankan di atas MySQL. Bagian yang bergantung pada SQL sudah dibuat menyesuaikan kedua database, "
    "namun pengujian pada lingkungan yang identik dengan produksi (MySQL) tetap perlu dilakukan sebelum rilis.",
)

# ---------------------------------------------------------------- 9

doc.add_heading("9. Keterbatasan yang Diketahui (Known Gaps)", level=1)
note(
    doc,
    "Daftar ini sengaja dibuat terbuka. Prioritas dinilai dari dampaknya terhadap pengalaman pengguna: "
    "Tinggi = menghambat pemakaian harian, Sedang = sering diminta pengguna, Rendah = penyempurnaan.",
)

table(
    doc,
    ["Keterbatasan", "Dampak bagi pengguna", "Prioritas"],
    [
        ("Tanpa pagination dan pencarian", "Akun dengan banyak transaksi memuat semua sekaligus; mencari satu transaksi lama melelahkan", "Tinggi"),
        ("Batas keras 2.000 transaksi per akun", "Pengguna jangka panjang bisa mencapai dinding dan harus menghapus data lamanya", "Tinggi"),
        ("Belum ada laporan berpratinjau di web", "Pengguna harus mengunduh berkas dulu untuk memastikan isinya", "Sedang"),
        ("Belum ada ekspor CSV / Excel", "Data tidak bisa diolah lebih lanjut atau dipindahkan ke alat lain", "Sedang"),
        ("Belum ada anggaran dan target tabungan", "Aplikasi mencatat masa lalu, tetapi belum membantu merencanakan masa depan", "Sedang"),
        ("Belum ada transaksi berulang", "Pengeluaran rutin (sewa, langganan) harus diinput manual setiap periode", "Sedang"),
        ("Kategori tidak menyarankan pilihan sebelumnya", "Pengguna mengetik ulang nama kategori setiap kali mencatat", "Sedang"),
        ("Satu mata uang (Rupiah)", "Tidak dapat dipakai untuk transaksi mata uang asing", "Rendah"),
        ("Belum ada berbagi keuangan keluarga", "Satu akun hanya untuk satu orang; tidak ada akun bersama", "Rendah"),
        ("Belum ada aplikasi mobile khusus / mode offline", "Bergantung pada browser dan koneksi internet", "Rendah"),
        ("Belum ada telemetri penggunaan", "Tim produk belum punya data nyata untuk mengambil keputusan berbasis angka", "Sedang"),
        ("Belum ada konfigurasi email produksi", "Verifikasi email dan reset password tidak berjalan di server nyata", "Tinggi"),
    ],
    widths=[4.4, 8.0, 2.2],
)

# ---------------------------------------------------------------- 10

doc.add_heading("10. Risiko Project", level=1)
table(
    doc,
    ["Risiko", "Tingkat", "Dampak bila terjadi", "Mitigasi yang disarankan"],
    [
        ("Batas 2.000 transaksi tercapai sebelum pagination dibangun", "Sedang", "Pengguna aktif terpaksa berhenti mencatat atau menghapus data lama", "Bangun pagination lebih dahulu, lalu naikkan atau lepaskan batasnya"),
        ("Deployment terlambat karena belum ada lingkungan produksi", "Sedang", "Produk tidak bisa diuji pengguna nyata; umpan balik tertunda", "Siapkan server, domain, MySQL, dan SMTP sebelum target rilis"),
        ("Pertumbuhan data tanpa pagination memperlambat halaman", "Sedang", "Pengguna merasa aplikasi lambat, terutama di koneksi seluler", "Pagination, batasi kolom yang dimuat, dan pantau waktu muat halaman Transaksi"),
        ("Tidak ada data penggunaan untuk keputusan", "Rendah", "Roadmap disusun berdasarkan asumsi, bukan perilaku nyata", "Tambahkan analitik ringan sejak versi uji coba"),
        ("Ketergantungan pada satu pengembang", "Sedang", "Kecepatan pengembangan dan perawatan terbatas", "Jaga dokumentasi pengembang dan pengujian otomatis tetap mutakhir"),
    ],
    widths=[4.0, 1.8, 4.6, 4.6],
)

# ---------------------------------------------------------------- 11

doc.add_heading("11. Rekomendasi Roadmap", level=1)
note(
    doc,
    "Usulan ini disusun dari keterbatasan yang teridentifikasi. Urutan fase mengutamakan perbaikan "
    "yang langsung dirasakan pengguna, baru kemudian penambahan kemampuan baru.",
)

doc.add_heading("Fase 1 - Rapikan dasar pemakaian harian", level=2)
table(
    doc,
    ["Inisiatif", "Nilai untuk pengguna", "Estimasi usaha"],
    [
        ("Konfirmasi dan ringkasan sebelum menyimpan", "Mengurangi salah input sejak awal", "Kecil"),
        ("Pagination dan pencarian", "Daftar tetap cepat dan mudah ditelusuri", "Sedang"),
        ("Konfigurasi email produksi", "Verifikasi dan reset password benar-benar berjalan", "Kecil"),
        ("Pratinjau laporan di web", "Memastikan isi laporan sebelum mengunduh", "Kecil"),
    ],
    widths=[5.4, 6.6, 3.0],
)

doc.add_heading("Fase 2 - Tingkatkan retensi dan wawasan", level=2)
table(
    doc,
    ["Inisiatif", "Nilai untuk pengguna", "Estimasi usaha"],
    [
        ("Anggaran bulanan per kategori", "Peringatan sebelum pengeluaran melewati batas", "Besar"),
        ("Grafik tren visual", "Tren keuangan lebih cepat dipahami daripada tabel", "Sedang"),
        ("Ekspor CSV / Excel", "Data bisa diolah lanjut oleh pengguna", "Kecil"),
        ("Transaksi berulang", "Pengeluaran rutin tercatat otomatis", "Sedang"),
        ("Analitik penggunaan dasar", "Keputusan roadmap berbasis data nyata", "Kecil"),
    ],
    widths=[5.4, 6.6, 3.0],
)

doc.add_heading("Fase 3 - Perluas jangkauan", level=2)
table(
    doc,
    ["Inisiatif", "Nilai untuk pengguna", "Estimasi usaha"],
    [
        ("Anggaran bersama / keuangan keluarga", "Satu wadah untuk kebutuhan rumah tangga", "Besar"),
        ("Impor mutasi bank", "Berhenti mencatat manual dari rekening koran", "Besar"),
        ("Mode gelap dan penyesuaian tampilan", "Kenyamanan pemakaian malam hari", "Kecil"),
        ("Multi-mata uang", "Melayani pengguna dengan penghasilan lintas negara", "Besar"),
        ("Aplikasi mobile / PWA dengan mode offline", "Mencatat tanpa bergantung koneksi", "Besar"),
    ],
    widths=[5.4, 6.6, 3.0],
)

# ---------------------------------------------------------------- 12

doc.add_heading("12. Usulan Metrik Keberhasilan", level=1)
para(
    doc,
    "Aplikasi saat ini belum mencatat data penggunaan, sehingga metrik berikut baru bisa diukur "
    "setelah analitik dasar atau minimal pencatatan aktivitas ditambahkan. Angka target bersifat "
    "awal dan perlu dikalibrasi setelah uji coba terbatas.",
)
table(
    doc,
    ["Metrik", "Definisi", "Target awal"],
    [
        ("Aktivasi", "Persentase pengguna baru yang mencatat minimal satu transaksi dalam 24 jam", "60%"),
        ("Retensi minggu ke-2", "Persentase pengguna yang kembali mencatat pada minggu kedua", "40%"),
        ("Keterlibatan", "Rata-rata jumlah transaksi per pengguna aktif per bulan", ">= 20"),
        ("Penggunaan nilai inti", "Persentase pengguna yang pernah mengunduh minimal satu laporan PDF", "25%"),
        ("Kecepatan halaman Transaksi", "Waktu muat halaman pada akun berisi banyak transaksi (persentil ke-95)", "< 1 detik"),
        ("Tingkat kesalahan", "Persentase permintaan yang berakhir dengan galat server", "< 1%"),
        ("Kualitas rilis", "Seluruh pengujian otomatis lulus sebelum setiap rilis", "100%"),
    ],
    widths=[3.8, 8.6, 2.6],
)

# ---------------------------------------------------------------- 13

doc.add_heading("13. Lampiran", level=1)

doc.add_heading("13.1 Peta halaman aplikasi", level=2)
table(
    doc,
    ["Halaman", "Alamat", "Akses"],
    [
        ("Halaman masuk", "/login", "Belum login"),
        ("Pendaftaran", "/register", "Belum login"),
        ("Lupa password", "/forgot-password", "Belum login"),
        ("Verifikasi email", "/verify-email", "Setelah daftar"),
        ("Dashboard", "/dashboard", "Wajib login + email terverifikasi"),
        ("Daftar transaksi", "/transaksi", "Wajib login + email terverifikasi"),
        ("Tambah transaksi", "/transaksi/create", "Wajib login + email terverifikasi"),
        ("Unduh laporan PDF", "/laporan/pdf", "Wajib login + email terverifikasi"),
        ("Profil", "/profile", "Wajib login"),
        ("Keluar", "/logout", "Wajib login"),
    ],
    widths=[4.4, 5.2, 5.4],
)

doc.add_heading("13.2 Arti istilah produk", level=2)
table(
    doc,
    ["Istilah", "Arti"],
    [
        ("Pemasukan", "Uang yang masuk ke akun pengguna."),
        ("Pengeluaran", "Uang yang keluar dari akun pengguna."),
        ("Saldo", "Total pemasukan dikurangi total pengeluaran, dihitung ulang setiap halaman dibuka."),
        ("Saldo awal periode", "Saldo sebelum periode laporan dimulai, menjadi titik tolak laporan."),
        ("Saldo akhir periode", "Saldo pada akhir periode laporan: saldo awal ditambah pemasukan dikurangi pengeluaran."),
        ("Saldo berjalan", "Saldo setelah setiap baris transaksi; memudahkan membaca laporan tanpa menjumlahkan manual."),
        ("Periode laporan", "Rentang waktu laporan: harian, mingguan (Senin-Minggu), bulanan, atau tahunan."),
        ("Empty state", "Tampilan khusus saat belum ada data, berisi penjelasan dan ajakan bertindak."),
        ("Pagination", "Pemecahan daftar panjang menjadi beberapa halaman agar tetap cepat dibuka."),
    ],
    widths=[4.0, 11.0],
)

doc.add_heading("13.3 Ringkasan status: apa adanya", level=2)
bullets_bold_lead(
    doc,
    [
        ("Sudah jadi dan teruji: ", "autentikasi, dashboard, pencatatan dan penghapusan transaksi, filter, empat periode laporan PDF, pengaturan profil."),
        ("Sudah jadi tetapi perlu pengaturan: ", "verifikasi email dan reset password (butuh SMTP produksi)."),
        ("Belum dikerjakan sama sekali: ", "pagination, pencarian, ekspor data, anggaran, transaksi berulang, deployment produksi, analitik."),
        ("Perlu keputusan Product Manager: ", "apakah batas 2.000 transaksi dinaikkan setelah pagination dibangun."),
    ],
)

doc.save(OUT)
print("Tersimpan:", OUT)
