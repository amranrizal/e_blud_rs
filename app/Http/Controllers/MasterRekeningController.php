<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rekening; 
use App\Http\Controllers\Controller;
use App\Imports\RekeningImport; // <--- TAMBAHKAN INI
use App\Models\RekeningAudit;
use App\Helpers\RekeningAuditHelper;
use App\Helpers\RekeningLevelHelper;
use Maatwebsite\Excel\Facades\Excel; // <--- TAMBAHKAN INI
use Illuminate\Support\Facades\DB;

class MasterRekeningController extends Controller
{
    // --- TIDAK ADA __CONSTRUCT DI SINI ---

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $akuns = Rekening::orderBy('kode_akun', 'ASC')
            ->paginate($perPage);

        return view('master.rekening.index', compact('akuns', 'perPage'));
    }

    public function create()
    {
        // 🔒 SATPAM: Cek Admin
        if (auth()->user()->role !== 'admin') abort(403);

        // 2. AMBIL DATA UNTUK DROPDOWN PARENT
        // Kita ambil semua rekening, diurutkan kodenya biar rapi
        $allRekening = \App\Models\Rekening::orderBy('kode_akun', 'ASC')->get();

        return view('master.rekening.create', compact('allRekening'));
    }

            // Jangan lupa import Model di paling atas file Controller:
            // use App\Models\Rekening;
            // use Illuminate\Http\Request;

    public function store(Request $request)
    {
        // 🔒 SATPAM
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'kode_akun' => 'required',
            'nama_akun'        => 'required',
            'parent_id'        => 'nullable|exists:m_rekening,id',
        ]);

        try {

            DB::transaction(function () use ($request) {

                $parent = null;
                $finalKode = $request->kode_akun;

                // 🔗 Jika ada parent
                if ($request->filled('parent_id')) {

                    $parent = Rekening::lockForUpdate()
                        ->findOrFail($request->parent_id);

                    // 🚫 Batas level maksimal
                    if ($parent->level === 'Sub Rincian Objek') {
                        abort(403, 'Level Sub Rincian Objek tidak boleh memiliki turunan.');
                    }

                    $finalKode = $parent->kode_akun . '.' . $request->kode_akun;
                }

                // 🔢 Hitung level otomatis
                $level = RekeningLevelHelper::nextLevel($parent?->level);

                // 🚫 Cek duplikat
                if (Rekening::where('kode_akun', $finalKode)->exists()) {
                    abort(422, "Kode $finalKode sudah ada!");
                }

                // 💾 Simpan
                $rekening = Rekening::create([
                    'parent_id' => $request->parent_id,
                    'kode_akun' => $finalKode,
                    'nama_akun' => $request->nama_akun,
                    'level'     => $level,
                ]);

                // 🧾 Audit
                RekeningAuditHelper::log(
                    $rekening,
                    'create',
                    null,
                    $rekening->toArray()
                );
            });

            return redirect()
                ->route('master.rekening.index')
                ->with('success', 'Data Rekening Berhasil Disimpan');

        } catch (\Exception $e) {

            return back()
                ->with('error', $e->getMessage());
        }
    }

        // 1. FUNCTION EDIT (Menampilkan Form)
    public function edit($id)
    {
        // 🔒 SATPAM: Cek Admin
        if (auth()->user()->role !== 'admin') abort(403);

        // Pakai findOrFail biar kalau ID ga ketemu muncul 404 (bukan error script)
        $rekening = \App\Models\Rekening::findOrFail($id);

        // Ambil data untuk dropdown parent (Kecuali dirinya sendiri biar ga loop)
        $allRekening = \App\Models\Rekening::where('id', '!=', $id)
                        ->orderBy('kode_akun', 'ASC')
                        ->get();

        // PENTING: Pastikan file 'resources/views/master/rekening/edit.blade.php' SUDAH ADA.
        // Kalau file ini tidak ada, layar pasti putih/error.
        return view('master.rekening.edit', compact('rekening', 'allRekening'));
    }

    // 2. FUNCTION UPDATE (Menyimpan Data)
    public function update(Request $request, $id)
    {
        // 🔒 SATPAM
        if (auth()->user()->role !== 'admin') abort(403);

        // VALIDASI DASAR
        $request->validate([
            'kode_akun' => 'required|unique:m_rekening,kode_akun,' . $id,
            'nama_akun' => 'required',
        ]);

        DB::transaction(function () use ($request, $id) {

        $rekening = Rekening::lockForUpdate()->findOrFail($id);
        $before = $rekening->toArray();

        // Default: level dihitung dari kode akun
        $newLevel = RekeningLevelHelper::detectLevelFromKode($rekening->kode_akun);

        // 🚫 Jika punya anak → struktur dikunci total
        if ($rekening->children()->exists()) {

            if ($request->kode_akun !== $rekening->kode_akun) {
                abort(403, 'Kode rekening tidak boleh diubah karena memiliki turunan.');
            }

            if ($request->parent_id != $rekening->parent_id) {
                abort(403, 'Rekening parent tidak boleh diubah karena memiliki turunan.');
            }

            // level tetap
            $newLevel = $rekening->level;
        } else {

            // Jika tidak punya anak → boleh pindah parent
            if ($request->parent_id != $rekening->parent_id) {
                $parent = Rekening::find($request->parent_id);
                $newLevel = RekeningLevelHelper::nextLevel($parent?->level);
            }
        }

        $rekening->update([
            'parent_id' => $request->parent_id,
            'kode_akun' => $rekening->kode_akun, // tetap dikunci
            'nama_akun' => $request->nama_akun,
            'level'     => $newLevel,
        ]);

        RekeningAuditHelper::log(
            $rekening,
            'update',
            $before,
            $rekening->fresh()->toArray()
        );
    });

        return redirect()
            ->route('master.rekening.index')
            ->with('success', 'Data rekening berhasil diperbarui');
    }



   public function destroy($id)
    {
        // 🔒 SATPAM
        if (auth()->user()->role !== 'admin') abort(403);

        DB::transaction(function () use ($id) {

            // 🔐 LOCK DATA
            $rekening = \App\Models\Rekening::lockForUpdate()->findOrFail($id);

            // AUDIT DULU
            \App\Helpers\RekeningAuditHelper::log(
                $rekening,
                'delete',
                $rekening->toArray(),
                null
            );

            // SOFT DELETE
            $rekening->delete();
        });

        return back()->with('success', 'Data berhasil dihapus');
    }


    // TAMBAHKAN FUNCTION INI
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new RekeningImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Master Rekening berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {

            $search = trim($request->get('q', ''));
            $searchNoDot = str_replace('.', '', $search);

            $data = \App\Models\Rekening::where(function ($q) use ($search, $searchNoDot) {

                $q->where('kode_akun', 'like', "%{$search}%")
                ->orWhere('nama_akun', 'like', "%{$search}%")
                ->orWhereRaw("REPLACE(kode_akun,'.','') LIKE ?", ["%{$searchNoDot}%"])
                ->orWhere('kode_akun', 'like', "{$search}%");

            })
            ->orderBy('kode_akun')
            ->limit(30)
            ->get();

            return response()->json(
                $data->map(function ($item) {
                    return [
                        'id' => $item->kode_akun,
                        'text' => $item->kode_akun . ' - ' . $item->nama_akun,
                    ];
                })
            );

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function goto(Request $request)
    {
        $id = $request->id;
        $perPage = $request->get('per_page', 10);

        $position = Rekening::orderBy('kode_akun')
            ->pluck('id')
            ->search($id);

        if ($position === false) {
            return redirect()->route('master.rekening.index');
        }

        $page = floor($position / $perPage) + 1;

        return redirect()->route('master.rekening.index', [
            'page' => $page,
            'selected_id' => $id,
            'per_page' => $perPage
        ]);
    }

}