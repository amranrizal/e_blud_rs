<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitKerja; // Pastikan Model ini benar
use App\Http\Controllers\Controller; 

class UnitController extends Controller
{
    // 1. INDEX (Boleh dilihat semua user)
    public function index()
    {
        $units = UnitKerja::all(); 
        return view('master.unit.index', compact('units'));
    }

    // 2. CREATE (Hanya Admin)
    public function create()
    {
        if (auth()->user()->role !== 'admin') abort(403);
        return view('master.unit.create');
    }

    // 3. STORE (Hanya Admin)
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $request->validate([
            // SESUAI REQUEST: Tabel 'm_unit_kerja'
            'kode_unit' => 'required|unique:m_unit_kerja,kode_unit', 
            'nama_unit' => 'required',
        ]);

        UnitKerja::create($request->all());

        return redirect()->route('master.unit.index')->with('success', 'Unit Kerja berhasil ditambahkan');
    }

    // 4. EDIT (Hanya Admin)
    public function edit($id)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $unit = UnitKerja::find($id);
        return view('master.unit.edit', compact('unit'));
    }

    // 5. UPDATE (Hanya Admin)
    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $request->validate([
            // Validasi Unique kecuali punya diri sendiri ($id)
            'kode_unit' => 'required|unique:m_unit_kerja,kode_unit,'.$id, 
            'nama_unit' => 'required',
        ]);

        $unit = UnitKerja::find($id);
        $unit->update($request->all());

        return redirect()->route('master.unit.index')->with('success', 'Unit Kerja berhasil diperbarui');
    }

    // 6. DESTROY (Hanya Admin)
    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $unit = UnitKerja::find($id);
        
        // Opsional: Cek apakah Unit ini punya User? Kalau ada jangan dihapus
        // if($unit->users()->count() > 0) { return back()->with('error', 'Gagal hapus...'); }

        $unit->delete();

        return back()->with('success', 'Unit Kerja berhasil dihapus');
    }
}