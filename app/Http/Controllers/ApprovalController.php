<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PaguIndikatif;
use App\Models\PaguIndikatifAudit;

class ApprovalController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | 1️⃣ USER: AJUKAN (draft → submitted)
    |--------------------------------------------------------------------------
    */

    public function ajukan($id)
    {
        $pagu = PaguIndikatif::findOrFail($id);

        if (!in_array(auth()->user()->role, ['user','admin'])) {
            abort(403);
        }

        // Cek apakah ada detail
        $cekItem = Budget::where('pagu_indikatif_id', $pagu->id)->exists();

        if (!$cekItem) {
            return back()->with('error', 'Rincian belanja masih kosong.');
        }

        DB::transaction(function () use ($pagu) {

            $statusLama = $pagu->status_validasi;

            $pagu->update([
                'status_validasi' => 'submitted',
                'catatan_revisi'  => null
            ]);

            PaguIndikatifAudit::create([
                'pagu_indikatif_id' => $pagu->id,
                'status_lama'       => $statusLama,
                'status_baru'       => 'submitted',
                'catatan'           => null,
                'user_id'           => auth()->id(),
            ]);

            Budget::where('pagu_indikatif_id', $pagu->id)
                ->update(['status' => 'diajukan']);
        });

        return back()->with('success', 'RKA berhasil diajukan.');
    }


    /*
    |--------------------------------------------------------------------------
    | 2️⃣ ADMIN: SETUJUI (submitted → valid)
    |--------------------------------------------------------------------------
    */

    public function setujui($id)
    {
        if (!in_array(auth()->user()->role, ['admin', 'pimpinan'])) {
            abort(403);
        }

        $pagu = PaguIndikatif::findOrFail($id);

        DB::transaction(function () use ($pagu) {

            $statusLama = $pagu->status_validasi;

            $pagu->update([
                'status_validasi' => 'valid',
                'validator_id'    => auth()->id(),
                'tgl_validasi'    => now()
            ]);

            PaguIndikatifAudit::create([
                'pagu_indikatif_id' => $pagu->id,
                'status_lama'       => $statusLama,
                'status_baru'       => 'valid',
                'catatan'           => null,
                'user_id'           => auth()->id(),
            ]);

            Budget::where('pagu_indikatif_id', $pagu->id)
                ->update(['status' => 'disahkan']);
        });

        return back()->with('success', 'RKA telah disahkan.');
    }


    /*
    |--------------------------------------------------------------------------
    | 3️⃣ ADMIN: TOLAK (submitted → tolak)
    |--------------------------------------------------------------------------
    */

    public function tolak(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'catatan' => 'required|string'
        ]);

        $pagu = PaguIndikatif::findOrFail($id);

        DB::transaction(function () use ($pagu, $request) {

            $statusLama = $pagu->status_validasi;

            $pagu->update([
                'status_validasi' => 'tolak',
                'catatan_revisi'  => $request->catatan,
                'validator_id'    => auth()->id(),
                'tgl_validasi'    => now()
            ]);

            PaguIndikatifAudit::create([
                'pagu_indikatif_id' => $pagu->id,
                'status_lama'       => $statusLama,
                'status_baru'       => 'tolak',
                'catatan'           => $request->catatan,
                'user_id'           => auth()->id(),
            ]);

            Budget::where('pagu_indikatif_id', $pagu->id)
                ->update(['status' => 'draft']);
        });

        return back()->with('warning', 'RKA dikembalikan untuk revisi.');
    }

    /*
    |--------------------------------------------------------------------------
    | 4️⃣ ADMIN: VALID → DRAFT
    |--------------------------------------------------------------------------
    */

    public function batalDraft($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $pagu = PaguIndikatif::findOrFail($id);

        if ($pagu->status_validasi !== 'valid') {
            return back()->with('error', 'Status tidak bisa dikembalikan.');
        }

        DB::transaction(function () use ($pagu) {

            $statusLama = $pagu->status_validasi;

            $pagu->update([
                'status_validasi' => 'draft',
                'validator_id'    => null,
                'tgl_validasi'    => null
            ]);

            PaguIndikatifAudit::create([
                'pagu_indikatif_id' => $pagu->id,
                'status_lama'       => $statusLama,
                'status_baru'       => 'draft',
                'catatan'           => 'Dikembalikan ke draft oleh admin',
                'user_id'           => auth()->id(),
            ]);

            Budget::where('pagu_indikatif_id', $pagu->id)
                ->update(['status' => 'draft']);
        });

        return back()->with('success', 'Status berhasil dikembalikan ke Draft.');
    }


    /*
    |--------------------------------------------------------------------------
    | 5️⃣ ADMIN: VALID → REVISI ADMIN
    |--------------------------------------------------------------------------
    */

    public function revisiAdmin(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'catatan' => 'required|string'
        ]);

        $pagu = PaguIndikatif::findOrFail($id);

        if ($pagu->status_validasi !== 'valid') {
            return back()->with('error', 'Status tidak valid.');
        }

        DB::transaction(function () use ($pagu, $request) {

            $statusLama = $pagu->status_validasi;

            $pagu->update([
                'status_validasi' => 'revisi_admin',
                'catatan_revisi'  => $request->catatan,
                'validator_id'    => null,
                'tgl_validasi'    => null,
            ]);

            PaguIndikatifAudit::create([
                'pagu_indikatif_id' => $pagu->id,
                'status_lama'       => $statusLama,
                'status_baru'       => 'revisi_admin',
                'catatan'           => $request->catatan,
                'user_id'           => auth()->id(),
            ]);

            Budget::where('pagu_indikatif_id', $pagu->id)
                ->update(['status' => 'draft']);
        });

        return back()->with('success', 'Masuk ke Revisi Admin.');
    }

}
