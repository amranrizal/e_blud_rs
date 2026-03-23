<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Budget;
use App\Models\UnitKerja;
use App\Models\PaguIndikatif;
use App\Models\Rekening;
use App\Models\StandarHarga;
use App\Models\Pejabat;

class BudgetController extends Controller
{
    /* =========================================================
     * HALAMAN 1 : DASHBOARD RKA
     * ========================================================= */
    public function index(Request $request)
    {

        $tahun = $request->get('tahun', date('Y'));
        $user  = auth()->user();

        $units  = [];
        $unitId = null;

        if ($user->role === 'admin') {
            $units  = UnitKerja::orderBy('nama_unit')->get();
            $unitId = $request->get('unit_id', optional($units->first())->id);

        } elseif ($user->role === 'pimpinan') {
            // pimpinan boleh lihat semua unit (read-only)
            $units  = UnitKerja::orderBy('nama_unit')->get();
            $unitId = $request->get('unit_id', optional($units->first())->id);

        } else {
            // user biasa
            if (!$user->unitKerja) {
                abort(403, 'Unit Kerja belum disetting.');
            }
            $unitId = $user->unitKerja->id;
        }


        $selectedUnit = $unitId ? UnitKerja::find($unitId) : null;
        $kegiatans = collect();
        $pejabats  = collect();

        if ($selectedUnit) {
            $pejabats = Pejabat::where('unit_id', $unitId)
                ->where('is_active', 1)
                ->get();
            $kegiatans = DB::table('pagu_indikatifs as pagu')
                ->join('m_program as sub', 'pagu.sub_kegiatan_id', '=', 'sub.id')
                ->join('m_program as keg', 'sub.parent_id', '=', 'keg.id')
                ->join('m_program as prog', 'keg.parent_id', '=', 'prog.id')
                ->where('pagu.unit_id', $unitId)
                ->where('pagu.tahun', $tahun)
                ->orderBy('prog.kode_program')
                ->orderBy('keg.kode_program')
                ->orderBy('sub.kode_program')
                ->select(
                    'pagu.id as pagu_id',
                    'pagu.pagu as pagu_nilai',
                    'pagu.sub_kegiatan_id',
                    'pagu.status_validasi',
                    'pagu.catatan_revisi',
                    'prog.id as prog_id',
                    'prog.kode_program as prog_kode',
                    'prog.nama_program as prog_nama',
                    'keg.id as keg_id',
                    'keg.kode_program as keg_kode',
                    'keg.nama_program as keg_nama',
                    'sub.id as sub_id',
                    'sub.kode_program as sub_kode',
                    'sub.nama_program as sub_nama'
                )
                ->get()
                ->map(function ($item) {

                $terpakai = \App\Models\Budget::where('pagu_indikatif_id', $item->pagu_id)
                    ->sum('total_anggaran');

                $item->terpakai = $terpakai;
                $item->sisa     = $item->pagu_nilai - $terpakai;
                $item->persen   = $item->pagu_nilai > 0
                    ? ($terpakai / $item->pagu_nilai) * 100
                    : 0;

                return $item;
            });
        }

        $programIndikators = collect();
        if ($kegiatans->count() > 0) {

            $programIds = $kegiatans->pluck('prog_id')->unique();
            $programIndikators = \App\Models\Indikator::whereIn('m_program_id', $programIds)
                        ->where('jenis', 'outcome')
                        ->where('tahun', $tahun)
                        ->get()
                        ->groupBy('m_program_id');
        }
        $kegiatanIndikators = collect();

        if ($kegiatans->count() > 0) {

            $kegiatanIds = $kegiatans->pluck('keg_id')->unique();

            $kegiatanIndikators = \App\Models\Indikator::whereIn('m_program_id', $kegiatanIds)
                ->where('jenis', 'output')
                ->where('tahun', $tahun)
                ->get()
                ->groupBy('m_program_id');
        }
        $subIndikators = collect();

        if ($kegiatans->count() > 0) {

            $subIds = $kegiatans->pluck('sub_id')->unique();

            $subIndikators = \App\Models\Indikator::whereIn('m_program_id', $subIds)
                ->where('jenis', 'output') // atau jenis khusus kalau mau bedakan
                ->get()
                ->groupBy('m_program_id');
        }


        $kegiatans = $kegiatans->map(function ($item) use ($programIndikators, $kegiatanIndikators, $subIndikators) {

            $item->program_has_indikator =
                isset($programIndikators[$item->prog_id]);

            $item->kegiatan_has_indikator =
                isset($kegiatanIndikators[$item->keg_id]);
            
            $item->sub_has_indikator =
                isset($subIndikators[$item->sub_id]);

            return $item;
        });

        $currentYear = now()->year;

        // range: 1 tahun ke belakang, 3 tahun ke depan
        $tahunList = range($currentYear - 1, $currentYear + 3);


        return view('budget.index', compact(
            'units',
            'selectedUnit',
            'kegiatans',
            'tahun',
            'pejabats',
            'programIndikators',
            'kegiatanIndikators',
            'subIndikators',
            'tahunList'

        ));
    }

    /* =========================================================
     * HALAMAN 2 : INPUT RINCIAN BELANJA
     * ========================================================= */
    public function create($id)
    {
        $paguInfo = PaguIndikatif::with('budgets.rekening')
            ->with(['subKegiatan' => function ($q) {
                $q->select('id','nama_program','kode_program');
            }])
            ->findOrFail($id);


        $tahun = $paguInfo->tahun;

        $terpakai = $paguInfo->budgets()->sum('total_anggaran');

        $sisaPagu = $paguInfo->pagu - $terpakai;

        $rincian = Budget::with('rekening')
            ->where('pagu_indikatif_id', $paguInfo->id)
            ->whereHas('rekening', function ($q) {
                $q->where('kode_akun', 'like', '5%');
            })
            ->orderBy('kode_akun')
            ->orderBy('created_at')
            ->get();


        $rekenings = Rekening::where('level', 'Sub Rincian Objek')
            ->where('kode_akun', 'like', '5%') // 🔥 hanya kelompok Belanja
            ->orderBy('kode_akun')
            ->get();

        $sshItems = StandarHarga::whereIn('kode_kelompok', ['SSH', 'SBU'])
            ->where('tahun', $tahun)
            ->orderBy('uraian')
            ->get();

        return view('budget.create', compact(
            'paguInfo',
            'tahun',
            'rincian',
            'rekenings',
            'sshItems',
            'sisaPagu'
        ));
    }


    /* =========================================================
     * SIMPAN RINCIAN BELANJA
     * ========================================================= */
    public function store(Request $request)
    {
        $request->validate([
            'pagu_indikatif_id' => 'required|exists:pagu_indikatifs,id',
            'id_rekening'       => 'required|exists:m_rekening,id',
            'uraian'            => 'required|string',
            'satuan'            => 'required|string',
            'volume'            => 'required|numeric|min:1',
            'harga_satuan'      => 'nullable|numeric|min:0',
            'standar_harga_id'  => 'nullable|exists:standar_hargas,id',
            'keterangan_harga'  => 'nullable|string'
        ]);

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Ambil Header Pagu
        |--------------------------------------------------------------------------
        */

        $pagu = PaguIndikatif::findOrFail($request->pagu_indikatif_id);

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Lock Governance (Status)
        |--------------------------------------------------------------------------
        */

        if (auth()->user()->role !== 'admin') {
            if (in_array($pagu->status_validasi, ['submitted', 'valid'])) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'RKA sedang diverifikasi / sudah disahkan'
                ], 403);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ Validasi Rekening (HARUS BELANJA / KODE 5)
        |--------------------------------------------------------------------------
        */

        $rekening = Rekening::findOrFail($request->id_rekening);

        if (!str_starts_with($rekening->kode_akun, '5')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya rekening Belanja (kode 5) yang diperbolehkan.'
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ Tentukan Mode SSH atau Manual
        |--------------------------------------------------------------------------
        */

        $standarHargaId = $request->standar_harga_id;
        $isManual       = false;
        $hargaSatuan    = 0;

        if ($standarHargaId) {

            // 🔒 Pastikan SSH tahun sama
            $ssh = StandarHarga::where('id', $standarHargaId)
                ->where('tahun', $pagu->tahun)
                ->firstOrFail();

            $isManual    = false;
            $hargaSatuan = $ssh->harga;

            // 🔒 Cegah manipulasi harga
            if ($request->harga_satuan && $request->harga_satuan != $ssh->harga) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Harga SSH tidak boleh diubah.'
                ], 422);
            }

        } else {

            $isManual = true;

            if (!$request->keterangan_harga) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Keterangan harga wajib diisi untuk item manual.'
                ], 422);
            }

            $hargaSatuan = $request->harga_satuan;
        }

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ Simpan Dalam Transaction + Lock Pagu
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $request,
            $pagu,
            $hargaSatuan,
            $isManual,
            $standarHargaId
        ) {

            // 🔥 Hitung total (support parameter SBU)
            $parameter = $request->input('parameter', []);

            $totalBaru = (float)$request->volume * (float)$hargaSatuan;

            if (!empty($parameter)) {
                foreach ($parameter as $val) {
                    if ($val !== null && $val !== '') {
                        $totalBaru *= (float)$val;
                    }
                }
            }


            // 🔒 Lock Header
            $paguLocked = PaguIndikatif::where('id', $pagu->id)
                ->lockForUpdate()
                ->first();

            $terpakai = $paguLocked->budgets()
                ->sum('total_anggaran');

            if ($totalBaru > ($paguLocked->pagu - $terpakai)) {
                abort(422, 'Over budget, sisa pagu tidak mencukupi');
            }

            /*
            |--------------------------------------------------------------------------
            | 6️⃣ Simpan Detail Budget
            |--------------------------------------------------------------------------
            */

            Budget::create([
                'pagu_indikatif_id' => $paguLocked->id,
                'unit_id'           => $paguLocked->unit_id,
                'sub_kegiatan_id'   => $paguLocked->sub_kegiatan_id,
                'kode_akun'         => $request->id_rekening,
                'standar_harga_id'  => $standarHargaId,
                'is_manual'         => $isManual,
                'uraian'            => $request->uraian,
                'satuan'            => $request->satuan,
                'harga_satuan'      => $hargaSatuan,
                'volume'            => $request->volume,
                'total_anggaran'    => $totalBaru,
                'tahun'             => $paguLocked->tahun,
                'keterangan_harga'  => $isManual ? $request->keterangan_harga : null,
                'status'            => 'draft',
            ]);
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Rincian belanja berhasil disimpan'
        ]);
    }



    /* =========================================================
     * UPDATE RINCIAN BELANJA
     * ========================================================= */
  public function update(Request $request, $id)
{
    $request->validate([
        'id_rekening'       => 'required|exists:m_rekening,id',
        'uraian'            => 'required|string',
        'satuan'            => 'required|string',
        'volume'            => 'required|numeric|min:1',
        'harga_satuan'      => 'nullable|numeric|min:0',
        'standar_harga_id'  => 'nullable|exists:standar_hargas,id',
        'keterangan_harga'  => 'nullable|string'
    ]);

    $budget = Budget::findOrFail($id);
    $pagu   = PaguIndikatif::findOrFail($budget->pagu_indikatif_id);

    // 🔒 Lock Governance
    if (auth()->user()->role !== 'admin') {
        if (in_array($pagu->status_validasi, ['submitted', 'valid'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'RKA sedang diverifikasi / sudah disahkan'
            ], 403);
        }
    }

    // 🔎 Validasi rekening belanja (kode 5)
    $rekening = Rekening::findOrFail($request->id_rekening);

    if (!str_starts_with($rekening->kode_akun, '5')) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Rekening tidak valid untuk RKA Belanja.'
        ], 422);
    }

    // 🔁 Mode SSH / Manual
    $standarHargaId = $request->standar_harga_id;
    $isManual       = false;
    $hargaSatuan    = 0;

    if ($standarHargaId) {

        $ssh = StandarHarga::where('id', $standarHargaId)
            ->where('tahun', $pagu->tahun)
            ->firstOrFail();

        $hargaSatuan = (float) $ssh->harga;
        $isManual    = false;

        if ($request->harga_satuan &&
            (float)$request->harga_satuan !== (float)$ssh->harga) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Harga SSH tidak boleh diubah.'
            ], 422);
        }

    } else {

        $isManual = true;

        if (!$request->keterangan_harga) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Keterangan harga wajib diisi untuk item manual.'
            ], 422);
        }

        $hargaSatuan = (float) $request->harga_satuan;
    }

    DB::transaction(function () use (
        $request,
        $budget,
        $pagu,
        $hargaSatuan,
        $isManual,
        $standarHargaId
    ) {

        $volume = (float) $request->volume;
        $harga  = (float) $hargaSatuan;

        $parameter = $request->input('parameter', []);

        // 🔢 Hitung total bersih
        $totalBaru = $volume * $harga;

        if (!empty($parameter)) {
            foreach ($parameter as $val) {
                if ($val !== null && $val !== '') {
                    $totalBaru *= (float) $val;
                }
            }
        }

        $parameterJson = !empty($parameter)
            ? json_encode($parameter)
            : null;

        // 🔒 Lock header
        $paguLocked = PaguIndikatif::where('id', $pagu->id)
            ->lockForUpdate()
            ->first();

        $terpakai = $paguLocked->budgets()
            ->where('id', '!=', $budget->id)
            ->sum('total_anggaran');

        if ($totalBaru > ($paguLocked->pagu - $terpakai)) {
            abort(422, 'Over budget, sisa pagu tidak mencukupi');
        }

        // 💾 Update
        $budget->update([
            'kode_akun'         => $request->id_rekening,
            'standar_harga_id'  => $standarHargaId,
            'is_manual'         => $isManual,
            'uraian'            => $request->uraian,
            'satuan'            => $request->satuan,
            'harga_satuan'      => $harga,
            'volume'            => $volume,
            'total_anggaran'    => $totalBaru,
            'parameter_json'    => $parameterJson,
            'keterangan_harga'  => $isManual ? $request->keterangan_harga : null,
        ]);
    });

    return response()->json([
        'status'  => 'success',
        'message' => 'Rincian berhasil diperbarui'
    ]);
}

    /* =========================================================
     * HAPUS RINCIAN BELANJA
     * ========================================================= */
    public function destroy($id)
    {
        $budget = Budget::findOrFail($id);
        $pagu   = PaguIndikatif::findOrFail($budget->pagu_indikatif_id);

        if (auth()->user()->role !== 'admin') {
            if (in_array($pagu->status_validasi, ['submitted', 'valid'])) {
                return back()->with('error', 'RKA sedang diverifikasi / sudah disahkan');
            }
        }

        $budget->delete();

        return back()->with('success', 'Rincian berhasil dihapus');
    }

}
