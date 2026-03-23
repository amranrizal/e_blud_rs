<?php

namespace App\Http\Controllers;

use App\Models\Asb;
use App\Models\Rekening;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class AsbController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        $datas = Asb::with('rekening')
            ->where('tahun', $tahun)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $rekeningBelanja = Rekening::where('level', 'Sub Rincian Objek')
            ->orderBy('kode_akun')
            ->get();

        return view('asb.index', compact(
            'datas',
            'tahun',
            'rekeningBelanja'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => [
                'required',
                Rule::unique('asb')
                    ->where(fn ($q) => $q->where('tahun', $request->tahun))
            ],
            'uraian' => 'required',
            'satuan' => 'required',
            'tarif' => 'required|numeric',
            'rekening_id' => 'required|exists:m_rekening,id',
            'tahun' => 'required|digits:4'
        ]);

        $rekening = Rekening::find($request->rekening_id); // ✅ benar

        if (!$rekening) {
            return back()
                ->withInput()
                ->with('error', 'Rekening tidak ditemukan.');
        }

        if ($rekening->children()->exists()) {
            return back()
                ->withInput()
                ->with('error', 'Hanya Sub Rincian Objek yang boleh dipilih.');
        }

        Asb::create($request->all());

        return redirect()->route('asb.index')
            ->with('success', 'ASB berhasil disimpan.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $asb = Asb::findOrFail($id);

        $request->validate([
            'kode' => 'required|unique:asb,kode,' . $asb->id,
            'uraian' => 'required|string',
            'satuan' => 'required|string',
            'tarif' => 'required|numeric|min:0',
            'rekening_id' => 'required|exists:m_rekening,id',
        ]);

        $asb->update($request->all());

        return back()->with('success', 'ASB berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $asb = Asb::findOrFail($id);
        $asb->delete();

        return back()->with('success', 'ASB berhasil dihapus.');
    }
}
