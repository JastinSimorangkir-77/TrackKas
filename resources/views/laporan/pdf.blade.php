{{--
    Isi laporan yang dirender dompdf menjadi berkas PDF.

    Sengaja dibuat polos: dompdf tidak mengenal flexbox atau grid, jadi halaman
    ini memakai tabel dengan garis tipis dan gaya seperlunya saja. Tidak ada
    Vite, font dari CDN, atau layout aplikasi di sini. Huruf yang dipakai juga
    huruf bawaan PDF (Helvetica), supaya berkasnya kecil.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">

    <title>{{ $filename }}</title>

    <style>
        @page {
            margin: 32px 36px;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #000000;
        }

        h1 {
            font-size: 16px;
            margin: 0;
        }

        h2 {
            font-size: 13px;
            margin: 16px 0 5px 0;
        }

        p {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table.info td {
            padding: 1px 0;
            vertical-align: top;
        }

        table.data th {
            border: 1px solid #808080;
            background-color: #eeeeee;
            font-size: 11px;
            padding: 4px 5px;
            text-align: left;
        }

        table.data td {
            border: 1px solid #808080;
            padding: 4px 5px;
            vertical-align: top;
        }

        table.data tr {
            page-break-inside: avoid;
        }

        tr.total td {
            font-weight: bold;
        }

        .right {
            text-align: right;
        }

        .kosong {
            padding: 10px 5px;
            text-align: center;
        }

        .kaki {
            margin-top: 16px;
            font-size: 11px;
            color: #555555;
        }
    </style>
</head>
<body>
    <h1>Laporan Keuangan</h1>

    <p>{{ $period->label() }}: {{ $period->rangeLabel() }}</p>

    {{-- Identitas pemilik laporan --}}
    <table class="info">
        <tr>
            <td width="80">Nama</td>
            <td width="280">{{ $user->name }}</td>
            <td width="60">Dibuat</td>
            <td>{{ $generatedAt->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>{{ $user->email }}</td>
            <td>Jumlah</td>
            <td>{{ $jumlah }} transaksi</td>
        </tr>
        <tr>
            <td>Jenis laporan</td>
            <td colspan="3">{{ $scope->label() }}</td>
        </tr>
    </table>

    {{-- Angka utama periode, ditulis sebagai baris tabel biasa --}}
    <h2>Ringkasan</h2>

    <table class="data">
        <tbody>
            @if ($scope->isCombined())
                <tr>
                    <td>Saldo awal periode</td>
                    <td class="right">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total pemasukan</td>
                    <td class="right">Rp {{ number_format($income, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total pengeluaran</td>
                    <td class="right">Rp {{ number_format($expense, 0, ',', '.') }}</td>
                </tr>
                <tr class="total">
                    <td>Saldo akhir periode</td>
                    <td class="right">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                </tr>
            @else
                {{--
                    Laporan sepihak hanya memuat satu angka: total jenis yang
                    dipilih. Baris saldo tidak ikut, karena laporan ini sengaja
                    tidak memuat transaksi jenis lainnya, sehingga saldo akan
                    terbaca seperti saldo rekening padahal bukan.
                --}}
                <tr class="total">
                    <td>{{ $scope->totalLabel() }}</td>
                    <td class="right">Rp {{ number_format($scopeTotal, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Rincian transaksi, lengkap dengan saldo berjalan --}}
    <h2>Rincian Transaksi</h2>

    {{--
        Kolom Jenis dan Saldo hanya muncul di laporan gabungan: begitu hanya satu
        jenis yang dilaporkan, kolom Jenis hanya mengulang hal yang sama di setiap
        baris, dan saldo berjalan tidak bisa dihitung dari separuh transaksi.
    --}}
    <table class="data">
        <thead>
            <tr>
                <th width="75">Tanggal</th>
                <th width="145">Kategori &amp; Catatan</th>

                @if ($scope->isCombined())
                    <th width="80">Jenis</th>
                @endif

                <th width="90" class="right">Jumlah</th>

                @if ($scope->isCombined())
                    <th width="90" class="right">Saldo</th>
                @endif
            </tr>
        </thead>

        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row->date->format('d/m/Y') }}</td>

                    <td>
                        {{ $row->category }}

                        @if ($row->note)
                            <br>{{ $row->note }}
                        @endif
                    </td>

                    @if ($scope->isCombined())
                        <td>{{ ucfirst($row->type) }}</td>
                    @endif

                    <td class="right">
                        {{ $row->type === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($row->amount, 0, ',', '.') }}
                    </td>

                    @if ($scope->isCombined())
                        <td class="right">Rp {{ number_format($row->balance, 0, ',', '.') }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $scope->isCombined() ? 5 : 3 }}" class="kosong">Tidak ada transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{--
        Rekap per kategori, hanya kalau periodenya memang punya transaksi, dan
        hanya untuk jenis yang memang dilaporkan. Tabel pemasukan tetap dibuat
        walaupun periodenya tidak punya pemasukan, supaya bentuk laporannya tidak
        berubah-ubah setiap kali isinya berbeda.
    --}}
    @if ($rows->isNotEmpty())
        @if ($scope->keeps('pemasukan'))
            <h2>Pemasukan per Kategori</h2>

            <table class="data">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th width="90" class="right">Jumlah</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($incomeByCategory as $row)
                        <tr>
                            <td>{{ $row->category }}</td>
                            <td class="right">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="kosong">Belum ada pemasukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        @if ($scope->keeps('pengeluaran'))
            <h2>Pengeluaran per Kategori</h2>

            <table class="data">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th width="90" class="right">Jumlah</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($expenseByCategory as $row)
                        <tr>
                            <td>{{ $row->category }}</td>
                            <td class="right">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="kosong">Belum ada pengeluaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    @endif

    <p class="kaki">
        Laporan ini dibuat otomatis oleh TrackKas pada {{ $generatedAt->format('d/m/Y H:i') }} WIB.
    </p>
</body>
</html>
