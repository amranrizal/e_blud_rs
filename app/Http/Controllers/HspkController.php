<?php

namespace App\Http\Controllers;

use App\Models\Hspk;
use App\Models\HspkItem;
use App\Models\StandarHarga;
use App\Models\Rekening;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HspkController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST HSPK
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        $datas = Hspk::with('rekening')
            ->where('tahun', $tahun)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $rekeningBelanja = Rekening::where('level', 'Sub Rincian Objek')
            ->orderBy('kode_akun')
            ->get();

        return view('hspk.index', compact(
            'datas',
            'tahun',
            'rekeningBelanja'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE MASTER HSPK
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'uraian' => 'required|string',
            'satuan' => 'required|string',
            'rekening_id' => 'required|exists:m_rekening,id',
            'tahun' => 'required|digits:4',
        ]);

        Hspk::create([
            'uraian' => $request->uraian,
            'satuan' => $request->satuan,
            'rekening_id' => $request->rekening_id,
            'tahun' => $request->tahun,
        ]);

        return back()->with('success', 'HSPK berhasil dibuat');
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL HSPK
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $hspk = Hspk::with(['items.ssh', 'rekening'])->findOrFail($id);

        $sshList = StandarHarga::where('kode_kelompok', 'SSH')
            ->orderBy('uraian')
            ->get();

        return view('hspk.show', compact('hspk', 'sshList'));
    }

    /*
    |--------------------------------------------------------------------------
    | TAMBAH ITEM SSH
    |--------------------------------------------------------------------------
    */

    public function tambahItem(Request $request, $id)
    {
        $request->validate([
            'standar_harga_id' => 'required|exists:standar_hargas,id',
            'koefisien' => 'required|numeric|min:0.0001',
        ]);

        $hspk = Hspk::findOrFail($id);

        // Simpan item
        if ($hspk->items()
                ->where('standar_harga_id', $request->standar_harga_id)
                ->exists()) {

            return back()->with('error', 'SSH sudah ada dalam HSPK ini.');
        }

        HspkItem::create([
            'hspk_id' => $hspk->id,
            'standar_harga_id' => $request->standar_harga_id,
            'koefisien' => $request->koefisien,
        ]);

        // 🔒 LOCK SSH (cara aman)
        \App\Models\StandarHarga::where('id', $request->standar_harga_id)
            ->update(['is_locked' => true]);

        $hspk->hitungTotal();

        return back()->with('success', 'Komponen ditambahkan');
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS ITEM
    |--------------------------------------------------------------------------
    */

    public function hapusItem($id)
    {
        $item = HspkItem::findOrFail($id);
        $hspk = $item->hspk;

        $item->delete();

        $hspk->hitungTotal();

        return back()->with('success', 'Komponen dihapus');
    }

    public function __construct()
    {

        $this->middleware(function ($request, $next) {

            if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
                abort(403, 'Anda tidak memiliki akses ke modul HSPK.');
            }

            return $next($request);

        })->only(['store','tambahItem','hapusItem']);
    }


}
