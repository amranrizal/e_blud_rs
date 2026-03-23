<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembiayaan;
use App\Models\Rekening; // Asumsi nama model m_rekening adalah Rekening
use Auth; // Atau sesuaikan helper user aktif Anda

class PembiayaanController extends Controller
{
    public function index()
    {
        // Ambil data pembiayaan, bisa difilter per unit/tahun jika perlu
        $data = Pembiayaan::with('rekening')->orderBy('kode_akun')->get();
        return view('pembiayaan.index', compact('data'));
    }

    public function create()
    {
        // FILTER PENTING: Hanya ambil akun level rincian objek untuk Akun 6
        // Sesuaikan 'level' dengan struktur m_rekening Anda.
        // Logic: Ambil kode_akun yang depannya '6'
        $akuns = Rekening::where('kode_akun', 'like', '6.%')
                ->whereRaw('LENGTH(kode_akun) > 5') // Pastikan mengambil level anak (bukan induk)
                ->orderBy('kode_akun')
                ->get();

        return view('pembiayaan.create', compact('akuns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_akun' => 'required',
            'uraian' => 'required',
            'volume' => 'required|numeric',
            'harga_satuan' => 'required|numeric',
        ]);

        // Hitung total otomatis
        $jumlah = $request->volume * $request->harga_satuan;

        Pembiayaan::create([
            'unit_id' => 1, // Ganti dengan Auth::user()->unit_id atau session
            'tahun' => date('Y'), // Atau session tahun anggaran
            'kode_akun' => $request->kode_akun,
            'uraian' => $request->uraian,
            'volume' => $request->volume,
            'satuan' => $request->satuan,
            'harga_satuan' => $request->harga_satuan,
            'jumlah' => $jumlah
        ]);

        return redirect()->route('pembiayaan.index')->with('success', 'Data Pembiayaan disimpan');
    }

    // Menampilkan Form Edit
    public function edit($id)
    {
        $pembiayaan = \App\Models\Pembiayaan::findOrFail($id);
        
        // Ambil daftar akun 6 untuk dropdown
        $akuns = \App\Models\Rekening::where('kode_akun', 'like', '6%')
                ->orderBy('kode_akun', 'asc')
                ->get();

        return view('pembiayaan.edit', compact('pembiayaan', 'akuns'));
    }

    // Proses Simpan Perubahan
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_akun' => 'required',
            'uraian' => 'required',
            'volume' => 'required|numeric',
            'harga_satuan' => 'required|numeric',
        ]);

        $item = \App\Models\Pembiayaan::findOrFail($id);

        // Hitung ulang total
        $jumlah = $request->volume * $request->harga_satuan;

        $item->update([
            'kode_akun' => $request->kode_akun,
            'uraian'    => $request->uraian,
            'volume'    => $request->volume,
            'satuan'    => $request->satuan,
            'harga_satuan' => $request->harga_satuan,
            'jumlah'    => $jumlah
        ]);

        return redirect()->route('pembiayaan.index')->with('success', 'Data Pembiayaan berhasil diperbarui!');
    }
    
    // Tambahkan method edit/update/destroy sesuai kebutuhan standar CRUD
    public function destroy($id)
    {
        $item = \App\Models\Pembiayaan::findOrFail($id);
        $item->delete();
        return redirect()->route('pembiayaan.index')->with('success', 'Data berhasil dihapus');
    }
}
