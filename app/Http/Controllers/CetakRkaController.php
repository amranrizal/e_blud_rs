<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Program;
use App\Models\Rekening;
use App\Models\Pejabat; 
use Illuminate\Support\Facades\DB;
use PDF; 

class CetakRkaController extends Controller
{
    public function printRKA($id, Request $request)
    {
        // ==========================================
        // 0. AMBIL DATA INSTANSI (GLOBAL SETTING)
        // ==========================================
        // Kita ambil di awal karena data ini dipakai di Header & Tanda Tangan
        $instansi = DB::table('m_instansi')->first();

        // Siapkan Default Data jika tabel kosong (Error Handling)
        $nama_instansi_db = $instansi ? $instansi->nama_instansi : 'DATA INSTANSI BELUM DISET';
        $kabupaten_db     = $instansi ? $instansi->kabupaten : 'Nama Kota';

        // ==========================================
        // 1. LOGIKA PENCARIAN ID (SMART CHECK)
        // ==========================================
        $cekPagu = DB::table('pagu_indikatifs')->where('id', $id)->first();
        if ($cekPagu) {
            $sub_kegiatan_id = $cekPagu->sub_kegiatan_id;
        } else {
            $sub_kegiatan_id = $id;
        }

        // ==========================================
        // 2. DATA HEADER (PROGRAM & KEGIATAN)
        // ==========================================
        $subKegiatan = Program::with('parent.parent')->find($sub_kegiatan_id);

        if (!$subKegiatan) {
            return abort(404, 'Data Sub Kegiatan tidak ditemukan.');
        }

        $kegiatan = $subKegiatan->parent; 
        $program  = $kegiatan ? $kegiatan->parent : null;

        $headerData = [
            'urusan'        => 'Urusan Pemerintahan Wajib',
            'bidang'        => 'Kesehatan', // Idealnya ambil dari relasi Program -> Bidang
            
            // [FIX 1] Unit Organisasi ambil dari Database Instansi
            'unit'          => $nama_instansi_db, 
            
            'program'       => $program ? $program->nama_program : '-',
            'kegiatan'      => $kegiatan ? $kegiatan->nama_program : '-',
            'sub_kegiatan'  => $subKegiatan->nama_program,
            'kode_sub'      => $subKegiatan->kode_program,
        ];

        // ==========================================
        // 3. DATA RINCIAN BELANJA
        // ==========================================
        $transaksi = Budget::where('sub_kegiatan_id', $sub_kegiatan_id)->get();

        $allKodes = [];
        foreach ($transaksi as $item) {
            $parts = explode('.', $item->kode_akun);
            $temp = '';
            foreach ($parts as $part) {
                $temp .= ($temp == '' ? '' : '.') . $part;
                $allKodes[] = $temp;
            }
        }
        $allKodes = array_unique($allKodes);

        $masterRekening = Rekening::whereIn('kode_akun', $allKodes)
                            ->orderBy('kode_akun', 'asc')
                            ->get();

        $dataRKA = [];
        $totalPagu = 0;

        foreach ($masterRekening as $rek) {
            $transaksiItem = $transaksi->where('kode_akun', $rek->kode_akun);
            $totalPerRekening = 0; 

            if ($transaksiItem->isEmpty()) {
                $totalPerRekening = $transaksi->filter(function ($val) use ($rek) {
                    return strpos($val->kode_akun, $rek->kode_akun . '.') === 0;
                })->sum('total_anggaran');
            }

            $dataRKA[] = [
                'is_header' => true,
                'kode'      => $rek->kode_akun,
                'uraian'    => $rek->nama_akun,
                'volume'    => '',
                'satuan'    => '',
                'harga'     => '',
                'total'     => $totalPerRekening > 0 ? $totalPerRekening : '', 
                'level'     => $rek->level, 
                'bold'      => true
            ];

            foreach ($transaksiItem as $item) {
                $dataRKA[] = [
                    'is_header' => false,
                    'kode'      => '',
                    'uraian'    => $item->uraian,
                    'volume'    => $item->volume,
                    'satuan'    => $item->satuan,
                    'harga'     => $item->harga_satuan,
                    'total'     => $item->total_anggaran,
                    'level'     => 'Rincian',
                    'bold'      => false
                ];
                $totalPagu += $item->total_anggaran;
            }
        }

        $terbilang = ucwords($this->penyebut($totalPagu));

        // ==========================================
        // 4. DATA DINAMIS (TTD & TANGGAL)
        // ==========================================
        
        // A. Ambil Pejabat (Dari Modal atau Default)
        $id_pejabat_selected = $request->input('pejabat_id');
        
        if($id_pejabat_selected) {
            $ttd = Pejabat::find($id_pejabat_selected);
        } else {
            $ttd = Pejabat::where('is_active', 1)->first(); 
        }

        // B. Logic Tanggal & Tempat
        // Priority: 1. Input Modal -> 2. Data DB Instansi -> 3. String Default
        
        // [FIX 2] Nama Kota default ambil dari $kabupaten_db
        $input_tempat  = $request->input('tempat_ttd');
        $nama_kota_final = $input_tempat ? $input_tempat : $kabupaten_db;
        
        // Tanggal
        $input_tanggal = $request->input('tanggal_ttd') ?? date('Y-m-d');
        $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $time = strtotime($input_tanggal);
        
        // Variable ini yang dikirim ke View (Solusi Error Undefined)
        $tglCetak = date('d', $time) . ' ' . $bulanIndo[(int)date('m', $time)] . ' ' . date('Y', $time);
        
        // Gabungan untuk layout: "Bandung, 20 Oktober 2024"
        $lokasiTanggal = $nama_kota_final . ', ' . $tglCetak;

        // C. QR Code
        $qrContent = "RKA SAH | ID: " . $id . " | TTD: " . ($ttd->nama ?? 'Unknown') . " | " . date('YmdHis');

        // ==========================================
        // 5. RENDER VIEW
        // ==========================================
        
        $dataView = compact(
            'headerData', 
            'dataRKA', 
            'totalPagu', 
            'terbilang', 
            'instansi', 
            'ttd', 
            'lokasiTanggal', // Format: "Nama Kota, 20 Mei 2024"
            'tglCetak',      // Format: "20 Mei 2024" (Solusi error sebelumnya)
            'nama_kota_final', // Format: "Nama Kota" (jika butuh dipisah)
            'qrContent'
        );

        return view('laporan.cetak_rka', $dataView);
    }

    private function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->penyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut($nilai/10)." puluh". $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut($nilai/100) . " ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut($nilai/1000) . " ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai/1000000) . " juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut($nilai/1000000000) . " milyar" . $this->penyebut(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut($nilai/1000000000000) . " trilyun" . $this->penyebut(fmod($nilai,1000000000000));
        }     
        return $temp;
    }
}