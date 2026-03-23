<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendapatan;
use App\Models\Rekening; // Untuk Dropdown Akun Pendapatan
use App\Models\UnitKerja;
use Illuminate\Support\Facades\DB;

class PendapatanController extends Controller
{
    // MENAMPILKAN DATA (INDEX)
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        
        // Ambil data pendapatan tahun terpilih
        $data = Pendapatan::where('tahun', $tahun)
                ->orderBy('kode_akun', 'asc')
                ->get();

        // Hitung Total Target
        $totalPendapatan = $data->sum('jumlah');

        return view('pendapatan.index', compact('data', 'tahun', 'totalPendapatan'));
    }

    // FORM TAMBAH (CREATE)
    public function create()
    {
        // Ambil hanya akun PENDAPATAN (Kode awal '4') dan level 'Sub Rincian Objek' (Level Input)
        // Sesuaikan level dengan struktur database m_rekening Anda
        $rekenings = Rekening::where('kode_akun', 'like', '4.%')
                    ->where('level', 'Sub Rincian Objek') 
                    ->orderBy('kode_akun', 'asc')
                    ->get();
        
        // Ambil Unit Kerja (Jika perlu input per unit)
        $units = UnitKerja::all();

        return view('pendapatan.create', compact('rekenings', 'units'));
    }

    // SIMPAN DATA (STORE)
    public function store(Request $request)
    {
        $request->validate([
            'tahun'     => 'required|digits:4',
            'kode_akun' => 'required',
            'uraian'    => 'required',
            'volume'    => 'required|numeric',
            'tarif'     => 'required|numeric',
        ]);

        // Hitung Total Otomatis
        $jumlah = $request->volume * $request->tarif;

        Pendapatan::create([
            'unit_id'   => $request->unit_id, // Nullable
            'tahun'     => $request->tahun,
            'kode_akun' => $request->kode_akun,
            'uraian'    => $request->uraian,
            'volume'    => $request->volume,
            'satuan'    => $request->satuan,
            'tarif'     => $request->tarif,
            'jumlah'    => $jumlah,
        ]);

        return redirect()->route('pendapatan.index', ['tahun' => $request->tahun])
                         ->with('success', 'Target Pendapatan berhasil ditambahkan.');
    }

    // FORM EDIT
    public function edit($id)
    {
        $item = Pendapatan::findOrFail($id);
        
        $rekenings = Rekening::where('kode_akun', 'like', '4.%')
                    ->where('level', 'Sub Rincian Objek')
                    ->orderBy('kode_akun', 'asc')
                    ->get();

        return view('pendapatan.edit', compact('item', 'rekenings'));
    }

    // UPDATE DATA
    public function update(Request $request, $id)
    {
        $request->validate([
            'uraian' => 'required',
            'volume' => 'required|numeric',
            'tarif'  => 'required|numeric',
        ]);

        $item = Pendapatan::findOrFail($id);
        
        // Hitung Ulang Total
        $jumlah = $request->volume * $request->tarif;

        $item->update([
            'kode_akun' => $request->kode_akun, // Jika akun boleh diubah
            'uraian'    => $request->uraian,
            'volume'    => $request->volume,
            'satuan'    => $request->satuan,
            'tarif'     => $request->tarif,
            'jumlah'    => $jumlah,
        ]);

        return redirect()->route('pendapatan.index', ['tahun' => $item->tahun])
                         ->with('success', 'Data berhasil diperbarui.');
    }

    // HAPUS DATA
    public function destroy($id)
    {
        $item = Pendapatan::findOrFail($id);
        $tahun = $item->tahun;
        $item->delete();

        return redirect()->route('pendapatan.index', ['tahun' => $tahun])
                         ->with('success', 'Data berhasil dihapus.');
    }
}