<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan RBA Full</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; }
        .header-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 10px; }
        .judul-laporan { text-align: center; font-weight: bold; }
        
        .rba-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .rba-table th { border: 1px solid #000; background: #ddd; padding: 5px; text-align: center; font-weight: bold; }
        .rba-table td { border: 1px solid #000; padding: 4px; vertical-align: top; }
        
        .angka { text-align: right; white-space: nowrap; }
        .tengah { text-align: center; }

        /* Indentasi Hierarki */
        .pad-Akun { font-weight: bold; background: #eee; font-size: 11px; }
        .pad-Kelompok { padding-left: 10px; font-weight: bold; }
        .pad-Jenis { padding-left: 20px; font-weight: bold; }
        .pad-Objek { padding-left: 30px; }
        .pad-Rincian { padding-left: 40px; } 
        .pad-Sub { padding-left: 50px; font-style: italic; } 
        .pad-Item { padding-left: 65px; color: #333; } 

        /* Warna Baris Khusus */
        .bg-subtotal { background-color: #f8f9fa; font-weight: bold; }
        .bg-surplus { background-color: #fff3e0; font-weight: bold; }
        .bg-silpa { background-color: #d1ecf1; font-weight: bold; font-size: 11px; }
        .bg-header-pembiayaan { background-color: #e3e6f0; font-weight: bold; }

        .ttd-table { width: 100%; margin-top: 20px; page-break-inside: avoid; }
    </style>
</head>
<body>

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td width="80" class="tengah">
                @php
                    $path = public_path('img/' . ($instansi->logo_path ?? 'logo.png'));
                    $src = file_exists($path) ? 'data:image/png;base64,'.base64_encode(file_get_contents($path)) : '';
                @endphp
                @if($src) <img src="{{ $src }}" width="60"> @endif
            </td>
            <td class="judul-laporan">
                <span style="font-size:14px;">{{ $instansi->kabupaten ?? 'PEMERINTAH DAERAH' }}</span><br>
                <span style="font-size:16px;">{{ $nama_instansi }}</span><br>
                <span style="font-size:9px; font-weight:normal">{{ $instansi->alamat ?? '' }}</span><br>
                <hr>
                RINCIAN RENCANA BISNIS DAN ANGGARAN (RBA)<br>
                TAHUN ANGGARAN {{ $tahun }}
            </td>
        </tr>
    </table>

    <!-- TABEL DATA -->
    <table class="rba-table">
        <thead>
            <tr>
                <th width="12%">KODE</th>
                <th>URAIAN</th>
                <th width="5%">VOL</th>
                <th width="8%">SAT</th>
                <th width="12%">TARIF/HARGA</th>
                <th width="15%">JUMLAH (Rp)</th>
            </tr>
        </thead>
        <tbody>
            {{-- 1. LOOP PENDAPATAN DAN BELANJA (YANG SUDAH ADA) --}}
            @forelse($dataRBA as $row)
                @php
                    $cssClass = '';
                    if($row['jenis'] == 'rekening') {
                        if($row['level'] == 'Akun') $cssClass = 'pad-Akun';
                        elseif($row['level'] == 'Kelompok') $cssClass = 'pad-Kelompok';
                        elseif($row['level'] == 'Jenis') $cssClass = 'pad-Jenis';
                        elseif($row['level'] == 'Objek') $cssClass = 'pad-Objek';
                        elseif($row['level'] == 'Rincian Objek') $cssClass = 'pad-Rincian';
                        else $cssClass = 'pad-Sub';
                    } else {
                        $cssClass = 'pad-Item';
                    }
                @endphp

                <tr style="{{ $row['bold'] ? 'font-weight:bold' : '' }}">
                    <td style="vertical-align: top;">{{ $row['kode'] }}</td>
                    <td class="{{ $cssClass }}">{{ $row['uraian'] }}</td>
                    <td class="tengah">{{ $row['volume'] > 0 ? number_format($row['volume'], 0, ',', '.') : '' }}</td>
                    <td class="tengah">{{ $row['satuan'] }}</td>
                    <td class="angka">{{ $row['harga'] > 0 ? number_format($row['harga'], 2, ',', '.') : '' }}</td>
                    <td class="angka">{{ number_format($row['total'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="tengah py-3">Data Pendapatan & Belanja Belum Tersedia</td>
                </tr>
            @endforelse

            {{-- 2. REKAPITULASI PENDAPATAN & BELANJA --}}
            <tr class="bg-subtotal">
                <td colspan="5" style="text-align:right; padding-right:10px;">TOTAL PENDAPATAN</td>
                <td class="angka">{{ number_format($totalPendapatan, 2, ',', '.') }}</td>
            </tr>
            <tr class="bg-subtotal">
                <td colspan="5" style="text-align:right; padding-right:10px;">TOTAL BELANJA</td>
                <td class="angka">{{ number_format($totalBelanja, 2, ',', '.') }}</td>
            </tr>
            <tr class="bg-surplus">
                <td colspan="5" style="text-align:right; padding-right:10px;">SURPLUS / (DEFISIT)</td>
                <td class="angka" style="{{ $surplusDefisit < 0 ? 'color:red;' : 'color:green;' }}">
                    {{ $surplusDefisit < 0 ? '(' : '' }}
                    {{ number_format(abs($surplusDefisit), 2, ',', '.') }}
                    {{ $surplusDefisit < 0 ? ')' : '' }}
                </td>
            </tr>

            {{-- ========================================================== --}}
            {{-- 3. BAGIAN PEMBIAYAAN (AKUN 6) --}}
            {{-- ========================================================== --}}
            
            {{-- Judul Besar Pembiayaan --}}
            <tr class="pad-Akun">
                <td>6</td>
                <td colspan="5">PEMBIAYAAN</td>
            </tr>

            {{-- 3.1 PENERIMAAN PEMBIAYAAN --}}
            <tr class="bg-header-pembiayaan">
                <td>6.1</td>
                <td colspan="5">PENERIMAAN PEMBIAYAAN</td>
            </tr>
            @if($penerimaanPembiayaan->count() > 0)
                @foreach($penerimaanPembiayaan as $item)
                <tr>
                    <td>{{ $item->kode_akun }}</td>
                    <td class="pad-Item">{{ $item->uraian }}</td>
                    <td class="tengah">{{ $item->volume }}</td>
                    <td class="tengah">{{ $item->satuan }}</td>
                    <td class="angka">{{ number_format($item->harga_satuan, 2, ',', '.') }}</td>
                    <td class="angka">{{ number_format($item->jumlah, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="6" class="tengah">- Tidak ada data penerimaan pembiayaan -</td></tr>
            @endif
            <tr style="font-weight: bold;">
                <td colspan="5" style="text-align: right; padding-right:10px">Jumlah Penerimaan Pembiayaan</td>
                <td class="angka">{{ number_format($totalPenerimaanPembiayaan, 2, ',', '.') }}</td>
            </tr>

            {{-- 3.2 PENGELUARAN PEMBIAYAAN --}}
            <tr class="bg-header-pembiayaan">
                <td>6.2</td>
                <td colspan="5">PENGELUARAN PEMBIAYAAN</td>
            </tr>
            @if($pengeluaranPembiayaan->count() > 0)
                @foreach($pengeluaranPembiayaan as $item)
                <tr>
                    <td>{{ $item->kode_akun }}</td>
                    <td class="pad-Item">{{ $item->uraian }}</td>
                    <td class="tengah">{{ $item->volume }}</td>
                    <td class="tengah">{{ $item->satuan }}</td>
                    <td class="angka">{{ number_format($item->harga_satuan, 2, ',', '.') }}</td>
                    <td class="angka">{{ number_format($item->jumlah, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                 <tr><td colspan="6" class="tengah">- Tidak ada data pengeluaran pembiayaan -</td></tr>
            @endif
            <tr style="font-weight: bold;">
                <td colspan="5" style="text-align: right; padding-right:10px">Jumlah Pengeluaran Pembiayaan</td>
                <td class="angka">{{ number_format($totalPengeluaranPembiayaan, 2, ',', '.') }}</td>
            </tr>

            {{-- 3.3 PEMBIAYAAN NETTO --}}
            <tr class="bg-subtotal">
                <td colspan="5" style="text-align:right; padding-right:10px;">PEMBIAYAAN NETTO</td>
                <td class="angka">{{ number_format($pembiayaanNetto, 2, ',', '.') }}</td>
            </tr>

            {{-- 4. SILPA AKHIR (GRAND TOTAL) --}}
            <tr class="bg-silpa">
                <td colspan="5" style="text-align:right; padding-right:10px; text-transform:uppercase;">
                    Sisa Lebih Pembiayaan Anggaran (SILPA)
                </td>
                <td class="angka" style="{{ $silpaAkhir < 0 ? 'color:red;' : '' }}">
                    {{ number_format($silpaAkhir, 2, ',', '.') }}
                </td>
            </tr>

        </tbody>
    </table>

    <div style="border:1px solid #000; padding:5px; margin-top:10px; font-style:italic;">
        {{-- Kita update terbilangnya ke SILPA Akhir karena ini angka final --}}
        <strong>Terbilang (SILPA):</strong> {{ $terbilang }} Rupiah
    </div>

    <!-- TANDA TANGAN -->
    <table class="ttd-table">
        <tr>
            <td width="60%"></td>
            <td align="center">
                {{ $lokasiTanggal }}<br>
                {{ $ttd->jabatan ?? 'Pemimpin BLUD' }}
                <br><br><br><br>
                <strong><u>{{ $ttd->nama ?? '.....................' }}</u></strong><br>
                NIP. {{ $ttd->nip ?? '-' }}
            </td>
        </tr>
    </table>

</body>
</html>