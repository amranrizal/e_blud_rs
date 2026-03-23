<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitKerja;      // Panggil Model Unit Kerja
use App\Models\PaguIndikatif;  // Panggil Model Pagu
use App\Models\Program;        // Panggil Model Nomenklatur
use Illuminate\Support\Facades\DB;
use App\Models\Budget;

class PaguIndikatifController extends Controller
{
    // Halaman Utama: Daftar Unit Kerja
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        
        // Ambil data dari tabel m_unit_kerja
        $units = UnitKerja::with(['paguIndikatifs' => function($q) use ($tahun) {
                $q->where('tahun', $tahun);
            }])
            ->orderBy('kode_unit') // Biar urut kodenya
            ->get();

        return view('pagu_indikatif.index', compact('units', 'tahun'));
    }

    // Halaman Detail: Mapping Kegiatan untuk 1 Unit
    public function edit(Request $request, $id)
    {
        $tahun = $request->get('tahun', date('Y'));
        
        // --- INI SUMBER ERROR TADI ---
        // Kita ganti User::findOrFail jadi UnitKerja::findOrFail
        $unit = UnitKerja::findOrFail($id); 

        // Ambil Pagu yang sudah dimapping
        $pagus = PaguIndikatif::with('subKegiatan')
                    ->where('unit_id', $id)
                    ->where('tahun', $tahun)
                    ->get();

        // Ambil Master Sub Kegiatan untuk Dropdown
        $subKegiatans = Program::where('level', 'Sub Kegiatan')
                        ->orderBy('kode_program')
                        ->get();

        return view('pagu_indikatif.edit', compact('unit', 'pagus', 'subKegiatans', 'tahun'));
    }

    // Simpan Mapping Baru
    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required', // ID Unit Kerja
            'sub_kegiatan_id' => 'required',
            'pagu' => 'required|numeric|min:0',
            'tahun' => 'required',
        ]);

        // Cek Duplikasi
        $exists = PaguIndikatif::where('unit_id', $request->unit_id)
                    ->where('sub_kegiatan_id', $request->sub_kegiatan_id)
                    ->where('tahun', $request->tahun)
                    ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Kegiatan ini sudah ada untuk unit tersebut!');
        }

        PaguIndikatif::create([
            'unit_id' => $request->unit_id,
            'sub_kegiatan_id' => $request->sub_kegiatan_id,
            'pagu' => $request->pagu,
            'tahun' => $request->tahun,
        ]);

        return redirect()->back()->with('success', 'Kegiatan & Pagu berhasil ditambahkan');
    }

    // Hapus Mapping
    public function destroy($id)
    {
        PaguIndikatif::find($id)->delete();
        return redirect()->back()->with('success', 'Mapping kegiatan dihapus');
    }

    public function update(Request $request, $id)
    {
        // 1. Cari data berdasarkan ID
        $pagu = \App\Models\PaguIndikatif::find($id); // Sesuaikan Model Anda

        if (!$pagu) {
            return back()->with('error', 'Data tidak ditemukan!');
        }

        // 2. Update Pagu
        $pagu->pagu = $request->pagu; 
        // Jika ada kolom pagu_murni, uncomment baris bawah:
        // $pagu->pagu_murni = $request->pagu; 
        
        $pagu->save();

        return back()->with('success', 'Pagu berhasil diperbarui!');
    }

    
   
}
