<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Renja;
use App\Models\UnitKerja;
use App\Models\Program;

class RenjaController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        $renjas = Renja::with(['unit','subKegiatan'])
                ->where('tahun',$tahun)
                ->get();

        return view('renja.index', compact('renjas','tahun'));
    }

    public function create()
    {
        $units = \App\Models\UnitKerja::orderBy('kode_unit')->get();

       $subKegiatans = Program::with('parent.parent')
                        ->where('level','Sub Kegiatan')
                        ->orderBy('kode_program')
                        ->get();

        return view('renja.create', compact('units','subKegiatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required',
            'sub_kegiatan_id' => 'required',
            'indikator_kinerja' => 'required',
            'target' => 'required',
            'satuan' => 'required',
            'pagu_rencana' => 'required|numeric'
        ]);

        \App\Models\Renja::create([
            'unit_id' => $request->unit_id,
            'sub_kegiatan_id' => $request->sub_kegiatan_id,
            'tahun' => date('Y'),
            'indikator_kinerja' => $request->indikator_kinerja,
            'target' => $request->target,
            'satuan' => $request->satuan,
            'pagu_rencana' => $request->pagu_rencana
        ]);

        return redirect()->route('renja.index')
                ->with('success','RENJA berhasil disimpan');
    }

    public function edit($id)
    {
        $renja = Renja::findOrFail($id);

        $units = UnitKerja::orderBy('kode_unit')->get();

        $subKegiatans = Program::where('level','Sub Kegiatan')
                        ->orderBy('kode_program')
                        ->get();

        return view('renja.edit', compact('renja','units','subKegiatans'));
    }

    public function update(Request $request, $id)
    {
        $renja = Renja::findOrFail($id);

        $renja->update([
            'unit_id' => $request->unit_id,
            'sub_kegiatan_id' => $request->sub_kegiatan_id,
            'indikator_kinerja' => $request->indikator_kinerja,
            'target' => $request->target,
            'satuan' => $request->satuan,
            'pagu_rencana' => $request->pagu_rencana
        ]);

        return redirect()->route('renja.index')
                ->with('success','RENJA berhasil diperbarui');
    }

    public function destroy($id)
    {
        $renja = \App\Models\Renja::findOrFail($id);

        $renja->delete();

        return redirect()->route('renja.index')
                ->with('success','RENJA berhasil dihapus');
    }
}