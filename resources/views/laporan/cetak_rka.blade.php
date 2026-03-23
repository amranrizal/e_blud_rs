<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak RKA 2.2.1</title>
    <style>
        /* CSS Khusus Cetak / PDF */
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px; /* Ukuran font standar dinas */
            line-height: 1.3;
        }
        
        /* Layout Header */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .logo {
            width: 60px;
        }
        .judul-laporan {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }

        /* Tabel Metadata (Organisasi, dll) */
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .meta-table td {
            vertical-align: top;
            padding: 2px;
        }

        /* Tabel Utama RKA */
        .rka-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .rka-table th {
            border: 1px solid #000;
            background-color: #f0f0f0;
            padding: 5px;
            text-align: center;
            font-weight: bold;
        }
        .rka-table td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }

        /* Styling Angka */
        .angka {
            text-align: right;
            white-space: nowrap; /* Biar Rp tidak turun baris */
        }
        .tengah {
            text-align: center;
        }

        /* Indenting / Menjorok ke dalam berdasarkan Level */
        .level-Akun { padding-left: 0px; font-weight: bold; }
        .level-Kelompok { padding-left: 10px; font-weight: bold; }
        .level-Jenis { padding-left: 20px; font-weight: bold; }
        .level-Objek { padding-left: 30px; font-weight: bold; }
        .level-Rincian { padding-left: 40px; font-style: italic; } /* Level 5 */
        .level-Input { padding-left: 55px; } /* Data Inputan User */

        /* Page Break Handling */
        thead { display: table-header-group; }
        tfoot { display: table-row-group; }
        tr { page-break-inside: avoid; }
        
        /* Footer Tanda Tangan */
        .ttd-table {
            width: 100%;
            margin-top: 30px;
        }
        .ttd-box {
            width: 40%;
            float: right;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- 1. HEADER LOGO & JUDUL (DINAMIS) -->
    <table class="header-table">
        <tr>
            <td width="100" class="tengah" style="vertical-align: middle;">
                @php
                    // 1. Ambil nama file dari database (atau default logo.png)
                    $imgName = $instansi->logo_path ?? 'logo.png';
                    
                    // 2. Tentukan Lokasi Fisik File di Komputer
                    $path = public_path('img/' . $imgName);
                    
                    // 3. Konversi ke Base64 (Jurus Anti-Gagal DomPDF)
                    $src = '';
                    if (file_exists($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $src = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                @endphp

                @if($src)
                    <!-- Tampilkan Gambar Base64 -->
                    <img src="{{ $src }}" width="80" alt="Logo">
                @else
                    <!-- Debugging: Kalau masih silang merah, baca pesan ini di PDF -->
                    <div style="border: 1px dashed red; padding: 5px; font-size: 9px; color: red;">
                        File tidak ditemukan di:<br>
                        {{ $path }}
                    </div>
                @endif
            </td>
            <td class="judul-laporan" style="vertical-align: middle;">
                <span style="font-size: 16px; text-transform: uppercase;">{{ $instansi->kabupaten ?? 'PEMERINTAH KABUPATEN' }}</span><br>
                <span style="font-size: 18px; font-weight: 900; text-transform: uppercase;">{{ $instansi->nama_instansi ?? 'NAMA INSTANSI' }}</span><br>
                <span style="font-size: 10px; font-weight: normal;">{{ $instansi->alamat ?? 'Alamat Instansi' }}</span><br>
                <hr style="margin-top: 5px; margin-bottom: 2px; border: 1px solid #000;">
                <span style="font-size: 14px; font-weight: bold; text-decoration: underline;">RENCANA KERJA DAN ANGGARAN (RKA)</span>
            </td>
        </tr>
    </table>

    <!-- 2. METADATA (Program/Kegiatan) -->
    <table class="meta-table">
        <tr>
            <td width="150"><strong>Unit Organisasi</strong></td>
            <td width="10">:</td>
            <td>{{ $headerData['unit'] }}</td>
        </tr>
        <tr>
            <td><strong>Program</strong></td>
            <td>:</td>
            <td>{{ $headerData['program'] }}</td>
        </tr>
        <tr>
            <td><strong>Kegiatan</strong></td>
            <td>:</td>
            <td>{{ $headerData['kegiatan'] }}</td>
        </tr>
        <tr>
            <td><strong>Sub Kegiatan</strong></td>
            <td>:</td>
            <td>{{ $headerData['sub_kegiatan'] }}</td>
        </tr>
        <tr>
            <td><strong>Alokasi Tahun -1</strong></td>
            <td>:</td>
            <td>Rp 0,00</td> <!-- Bisa dibuat dinamis nanti -->
        </tr>
        <tr>
            <td><strong>Alokasi Tahun +1</strong></td>
            <td>:</td>
            <td>Rp 0,00</td> <!-- Bisa dibuat dinamis nanti -->
        </tr>
    </table>

    <!-- 3. TABEL UTAMA RKA -->
    <table class="rka-table">
        <thead>
            <tr>
                <th rowspan="2" width="15%">KODE REKENING</th>
                <th rowspan="2" width="40%">URAIAN</th>
                <th colspan="3">RINCIAN PERHITUNGAN</th>
                <th rowspan="2" width="15%">JUMLAH<br>(Rp)</th>
            </tr>
            <tr>
                <th>Vol</th>
                <th>Sat</th>
                <th>Harga</th>
            </tr>
            <tr style="background-color: #ddd; font-size: 9px;">
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6 = (3x5)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataRKA as $row)
                @if($row['is_header'])
                    <!-- BARIS HEADER REKENING (Master) -->
                    <tr>
                        <td class="tengah"><b>{{ $row['kode'] }}</b></td>
                        <td class="level-{{ $row['level'] }}">{{ $row['uraian'] }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <!-- Biasanya header rekening tidak menampilkan total, kecuali level tertentu -->
                        <td class="angka"></td> 
                    </tr>
                @else
                    <!-- BARIS RINCIAN BELANJA (Inputan User) -->
                    <tr>
                        <td></td> <!-- Kode kosong untuk rincian -->
                        <td class="level-Input">{{ $row['uraian'] }}</td>
                        <td class="tengah">{{ number_format($row['volume'], 0, ',', '.') }}</td>
                        <td class="tengah">{{ $row['satuan'] }}</td>
                        <td class="angka">{{ number_format($row['harga'], 0, ',', '.') }}</td>
                        <td class="angka">{{ number_format($row['total'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="5" style="text-align: right; padding-right: 10px;">JUMLAH TOTAL</td>
                <td class="angka">{{ number_format($totalPagu, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- 4. TERBILANG -->
    <div style="border: 1px solid #000; padding: 5px; margin-bottom: 20px; font-style: italic;">
        <strong>Terbilang:</strong> 
        ( {{ $terbilang }} Rupiah )
    </div>

        <!-- 5. TANDA TANGAN (ENTERPRISE STYLE WITH QR) -->
        <table class="ttd-table" style="width: 100%; page-break-inside: avoid; margin-top: 20px;">
            <tr>
                <!-- Kolom Kiri Kosong -->
                <td width="50%"></td>

                <!-- Kolom Kanan (Pejabat Teknis) -->
                <!-- Tambahkan align="center" agar aman di semua PDF Reader -->
                <td class="ttd-box" width="50%" align="center" style="text-align: center;">
                    
                    <!-- Tempat & Tanggal (DINAMIS) -->
                    <div style="margin-bottom: 10px;">
                        {{-- [PERBAIKAN] Menggunakan variabel gabungan dari Controller --}}
                        {{ $lokasiTanggal }}
                    </div>

                    <!-- Jabatan -->
                    <div style="font-weight: bold; margin-bottom: 10px;">
                        {{ $ttd->jabatan ?? 'Pejabat Pelaksana Teknis' }}
                    </div>

                    <!-- AREA QR CODE -->
                    <!-- Note: Hindari display:flex di PDF (dompdf sering error), gunakan text-align center biasa -->
                    <div style="padding: 10px;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode($qrContent) }}" 
                            alt="QR Validasi" 
                            style="width: 90px; height: 90px; border: 1px solid #ccc; padding: 2px;">
                        
                        <br>
                        <span style="font-size: 9px; color: #555; display: block; margin-top: 4px;">
                            <i>Dokumen ini ditandatangani secara elektronik</i>
                        </span>
                    </div>

                    <!-- Nama & NIP -->
                    <div style="margin-top: 5px;">
                        <strong><u>{{ $ttd->nama ?? '( ........................... )' }}</u></strong><br>
                        NIP. {{ $ttd->nip ?? '-' }}
                    </div>
                </td>
            </tr>
        </table>

</body>
</html>