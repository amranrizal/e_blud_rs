<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;      // Model Belanja
use App\Models\Pendapatan;  // Model Pendapatan
use App\Models\Rekening;    // Master Rekening
use App\Models\Pejabat;     // Tanda Tangan
use App\Models\Pembiayaan;  // <--- PENTING: Model Pembiayaan
use App\Models\Instansi;    // Data RS/Instansi
use Illuminate\Support\Facades\DB;
use PDF; // Library DomPDF

class LaporanRbaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Filter Tahun (Default Tahun Ini)
        $tahun = $request->input('tahun', date('Y'));

        // 2. Hitung Ringkasan Pendapatan (Akun 4)
        $totalPendapatan = Pendapatan::where('tahun', $tahun)->sum('jumlah');

        // 3. Hitung Ringkasan Belanja (Akun 5) - Hanya yang Disahkan
        $totalBelanja = Budget::where('tahun', $tahun)
                        ->where('status', 'disahkan')
                        ->sum('total_anggaran');

        $surplusDefisit = $totalPendapatan - $totalBelanja;

        // 4. Hitung Ringkasan Pembiayaan (Akun 6)
        // 6.1 Penerimaan
        $penerimaan = Pembiayaan::where('tahun', $tahun)
                        ->where('kode_akun', 'like', '6.1%')
                        ->sum('jumlah');
        
        // 6.2 Pengeluaran
        $pengeluaran = Pembiayaan::where('tahun', $tahun)
                        ->where('kode_akun', 'like', '6.2%')
                        ->sum('jumlah');
        
        $pembiayaanNetto = $penerimaan - $pengeluaran;

        // 5. Hitung Estimasi SILPA
        $silpaAkhir = $surplusDefisit + $pembiayaanNetto;

        // 6. Return View dengan Data Summary
        return view('laporan.index', compact(
            'tahun',
            'totalPendapatan',
            'totalBelanja',
            'surplusDefisit',
            'penerimaan',
            'pengeluaran',
            'pembiayaanNetto',
            'silpaAkhir'
        ));
    }

    public function cetakRbaFull(Request $request)
    {
        // 1. SETUP TANGGAL & INSTANSI
        $tahun = $request->input('tahun', date('Y'));

        $inputTanggal = $request->input('tanggal_cetak');
        $timestamp = $inputTanggal ? strtotime($inputTanggal) : time();
        $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $tglCetak  = date('d', $timestamp) . ' ' . $bulanIndo[(int)date('m', $timestamp)] . ' ' . date('Y', $timestamp);

        $instansi = DB::table('m_instansi')->first();
        $nama_instansi = $instansi ? $instansi->nama_instansi : 'RSUD CONTOH';
        $kabupaten = $instansi ? $instansi->kabupaten : 'Nama Kota';
        $lokasiTanggal = $kabupaten . ', ' . $tglCetak;

        // 2. AMBIL DATA PENDAPATAN (AKUN 4)
        $pendapatanAll = Pendapatan::where('tahun', $tahun)->get();

        // 3. AMBIL DATA BELANJA (AKUN 5)
        // Hanya yang status 'disahkan'
        $belanjaAll = Budget::where('tahun', $tahun)
                        ->where('status', 'disahkan')
                        ->get();

        // 4. MERGE KODE REKENING (UNTUK MEMBANGUN HIERARKI)
        $usedCodes = [];

        // Ambil Kode dari Pendapatan
        foreach ($pendapatanAll as $item) {
            $parts = explode('.', $item->kode_akun);
            $temp = '';
            foreach ($parts as $part) {
                $temp .= ($temp == '' ? '' : '.') . $part;
                $usedCodes[] = $temp;
            }
        }

        // Ambil Kode dari Belanja
        foreach ($belanjaAll as $item) {
            $parts = explode('.', $item->kode_akun);
            $temp = '';
            foreach ($parts as $part) {
                $temp .= ($temp == '' ? '' : '.') . $part;
                $usedCodes[] = $temp;
            }
        }

        $usedCodes = array_unique($usedCodes);

        // Ambil Master Rekening & Urutkan (Otomatis 4 dulu baru 5)
        $masterRekening = Rekening::whereIn('kode_akun', $usedCodes)
                            ->orderBy('kode_akun', 'asc')
                            ->get();

        // 5. SUSUN DATA LAPORAN (ARRAY DATA RBA)
        $dataRBA = [];
        $totalPendapatan = 0;
        $totalBelanja = 0;

        foreach ($masterRekening as $rek) {

            // Cek Jenis Akun (4 atau 5)
            $prefix = substr($rek->kode_akun, 0, 1);
            $totalPerRekening = 0;

            if ($prefix == '4') {
                // --- LOGIC PENDAPATAN ---
                $transaksiItem = $pendapatanAll->filter(function ($item) use ($rek) {
                    return strpos($item->kode_akun, $rek->kode_akun) === 0;
                });
                $totalPerRekening = $transaksiItem->sum('jumlah');

            } else {
                // --- LOGIC BELANJA ---
                $transaksiItem = $belanjaAll->filter(function ($item) use ($rek) {
                    return strpos($item->kode_akun, $rek->kode_akun) === 0;
                });
                $totalPerRekening = $transaksiItem->sum('total_anggaran');
            }

            if ($totalPerRekening > 0) {
                // Masukkan Induk (Akun/Kelompok/Jenis/Objek) ke Array
                $dataRBA[] = [
                    'jenis'  => 'rekening',
                    'kode'   => $rek->kode_akun,
                    'uraian' => $rek->nama_akun,
                    'level'  => $rek->level,
                    'volume' => 0,
                    'satuan' => '',
                    'harga'  => 0,
                    'total'  => $totalPerRekening,
                    'bold'   => true
                ];

                // Item Detail (Sub Rincian Objek / Rincian Belanja)
                if ($rek->level == 'Sub Rincian Objek' || $rek->level == 'Rincian Objek') {
                    
                    if ($prefix == '4') {
                        // Detail Pendapatan
                        $detailItems = $pendapatanAll->where('kode_akun', $rek->kode_akun);
                        foreach ($detailItems as $item) {
                            $dataRBA[] = [
                                'jenis'  => 'item',
                                'kode'   => '',
                                'uraian' => $item->uraian,
                                'level'  => 'Item',
                                'volume' => $item->volume,
                                'satuan' => $item->satuan,
                                'harga'  => $item->tarif,
                                'total'  => $item->jumlah,
                                'bold'   => false
                            ];
                        }
                    } else {
                        // Detail Belanja
                        $detailItems = $belanjaAll->where('kode_akun', $rek->kode_akun);
                        foreach ($detailItems as $item) {
                            $dataRBA[] = [
                                'jenis'  => 'item',
                                'kode'   => '',
                                'uraian' => $item->uraian,
                                'level'  => 'Item',
                                'volume' => $item->volume,
                                'satuan' => $item->satuan,
                                'harga'  => $item->harga_satuan,
                                'total'  => $item->total_anggaran,
                                'bold'   => false
                            ];
                        }
                    }
                }
            }

            // Hitung Grand Total Per Bab (Akun Level 1)
            if ($rek->level == 'Akun') { 
                if ($prefix == '4') {
                    $totalPendapatan += $totalPerRekening;
                } elseif ($prefix == '5') {
                    $totalBelanja += $totalPerRekening;
                }
            }
        }

        // ================================================================
        // MULAI LOGIC TAMBAHAN: SURPLUS, PEMBIAYAAN & SILPA (DISINI KUNCINYA)
        // ================================================================

        // 1. SURPLUS / DEFISIT
        $surplusDefisit = $totalPendapatan - $totalBelanja;

        // 2. PENERIMAAN PEMBIAYAAN (6.1)
        $penerimaanPembiayaan = Pembiayaan::where('tahun', $tahun)
                                ->where('kode_akun', 'like', '6.1%')
                                ->orderBy('kode_akun')
                                ->get();
        $totalPenerimaanPembiayaan = $penerimaanPembiayaan->sum('jumlah');

        // 3. PENGELUARAN PEMBIAYAAN (6.2)
        $pengeluaranPembiayaan = Pembiayaan::where('tahun', $tahun)
                                ->where('kode_akun', 'like', '6.2%')
                                ->orderBy('kode_akun')
                                ->get();
        $totalPengeluaranPembiayaan = $pengeluaranPembiayaan->sum('jumlah');

        // 4. PEMBIAYAAN NETTO
        $pembiayaanNetto = $totalPenerimaanPembiayaan - $totalPengeluaranPembiayaan;

        // 5. SILPA AKHIR
        $silpaAkhir = $surplusDefisit + $pembiayaanNetto;

        // 6. TERBILANG (Mengacu pada SILPA Akhir)
        $terbilang = ucwords($this->penyebut($silpaAkhir));

        // ================================================================
        // PEJABAT & PDF
        // ================================================================

        // Ambil Pejabat Tanda Tangan
        $ttd = Pejabat::where('jabatan', 'like', '%Direktur%')
                ->orWhere('jabatan', 'like', '%Pimpinan%')
                ->where('is_active', 1)
                ->first();

        // Generate PDF
        $pdf = PDF::loadView('laporan.cetak_rba_full', compact(
            'dataRBA', 
            'tahun', 
            'instansi', 
            'nama_instansi', 
            'ttd', 
            'lokasiTanggal', 
            'terbilang',
            
            // Variabel Keuangan
            'totalPendapatan', 
            'totalBelanja', 
            'surplusDefisit',
            
            // Variabel Pembiayaan (Akun 6) - WAJIB ADA AGAR TIDAK ERROR
            'penerimaanPembiayaan',
            'totalPenerimaanPembiayaan',
            'pengeluaranPembiayaan',
            'totalPengeluaranPembiayaan',
            'pembiayaanNetto',
            'silpaAkhir'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_RBA_Full_'.$tahun.'.pdf');
    }

    // FUNGSI BANTUAN TERBILANG
    public function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai <20) {
            $temp = $this->penyebut($nilai - 10). " Belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai/10)." Puluh". $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai/100) . " Ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai/1000) . " Ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai/1000000) . " Juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai/1000000000) . " Milyar" . $this->penyebut(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut($nilai/1000000000000) . " Trilyun" . $this->penyebut(fmod($nilai,1000000000000));
        }     
        return $temp;
    }
}