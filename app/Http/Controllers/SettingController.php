<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
// [PENTING] Import Model agar relasi ke View terbaca
use App\Models\Pejabat;   
use App\Models\UnitKerja; 

class SettingController extends Controller
{
    // TAMPILKAN HALAMAN PENGATURAN
    public function index()
    {
        // 1. Ambil Data Instansi (Row pertama) - Tetap pakai DB Query Builder OK
        $instansi = DB::table('m_instansi')->first();

        // 2. [BARU] Ambil Daftar Unit Kerja untuk Dropdown Modal
        // Menggunakan Model atau DB Table sama saja, kita pakai Model biar rapi
        $units = UnitKerja::orderBy('nama_unit', 'asc')->get();

        // 3. [UPDATE] Ambil Daftar Pejabat dengan Relasi Unit
        // Kita pakai Model 'Pejabat' dan 'with(unit)' agar di View bisa panggil $p->unit->nama_unit
        $pejabat = Pejabat::with('unit')->orderBy('nama', 'asc')->get();

        return view('settings.index', compact('instansi', 'pejabat', 'units'));
    }

    // UPDATE PROFIL INSTANSI (TIDAK BERUBAH)
    public function updateInstansi(Request $request)
    {
        $data = [
            'nama_instansi' => $request->nama_instansi,
            'kabupaten'     => $request->kabupaten,
            'alamat'        => $request->alamat,
        ];

        // Handle Upload Logo
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img'), $filename);
            
            $data['logo_path'] = $filename;
        }

        // Cek sudah ada data atau belum
        $exists = DB::table('m_instansi')->first();
        if ($exists) {
            DB::table('m_instansi')->where('id', $exists->id)->update($data);
        } else {
            DB::table('m_instansi')->insert($data);
        }

        return redirect()->back()->with('success', 'Profil Instansi Berhasil Diupdate!');
    }

    // SIMPAN PEJABAT BARU
    public function storePejabat(Request $request)
    {
        // [UPDATE] Tambahkan 'unit_id' ke dalam insert
        DB::table('m_pejabat')->insert([
            'unit_id'   => $request->unit_id, // <--- Input dari Dropdown Modal
            'nama'      => $request->nama,
            'nip'       => $request->nip,
            'jabatan'   => $request->jabatan,
            'is_active' => 1
        ]);

        return redirect()->back()->with('success', 'Pejabat Berhasil Ditambahkan!');
    }

    // UPDATE PEJABAT
    public function updatePejabat(Request $request, $id)
    {
        // [UPDATE] Tambahkan 'unit_id' ke dalam update
        DB::table('m_pejabat')->where('id', $id)->update([
            'unit_id'   => $request->unit_id, // <--- Update Unit Kerja
            'nama'      => $request->nama,
            'nip'       => $request->nip,
            'jabatan'   => $request->jabatan,
        ]);

        return redirect()->back()->with('success', 'Data Pejabat Diupdate!');
    }

    // HAPUS PEJABAT (TIDAK BERUBAH)
    public function destroyPejabat($id)
    {
        DB::table('m_pejabat')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Pejabat Dihapus!');
    }
}