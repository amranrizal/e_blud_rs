<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Program;
use Illuminate\Http\Request;

class IndikatorProgramController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
                'm_program_id' => 'required',
                'tolok_ukur' => 'required',
                'target' => 'required|numeric',
                'satuan' => 'required'
            ]);
    
        $node = \App\Models\Program::findOrFail($request->m_program_id);

        $allowedJenis = [
            'Program' => 'outcome',
            'Kegiatan' => 'output',
            'Sub Kegiatan' => 'output',
        ];

        if (isset($allowedJenis[$node->level]) && 
            $request->jenis !== $allowedJenis[$node->level]) {

            return response()->json([
                'status' => 'error',
                'message' => $node->level . ' hanya boleh memiliki indikator ' . $allowedJenis[$node->level]
            ], 422);
        }

        // Validasi role
        if (!in_array(auth()->user()->role, ['admin', 'super admin'])) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses'
            ], 403);
        }

        Indikator::create([
            'm_program_id' => $request->m_program_id,
            'jenis' => $request->jenis,
            'tolok_ukur' => $request->tolok_ukur,
            'target' => $request->target,
            'satuan' => $request->satuan,
            'tahun' => now()->year,
            'created_by' => auth()->id()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Indikator Program berhasil ditambahkan'
        ]);
    }

    public function update(Request $request, $id)
    {
        $indikator = Indikator::findOrFail($id);

        if (!in_array(strtolower(auth()->user()->role), ['admin','super admin'])) {
            return response()->json(['message' => 'Tidak punya akses'], 403);
        }

        $request->validate([
            'tolok_ukur' => 'required',
            'target' => 'required|numeric',
            'satuan' => 'required'
        ]);

        $indikator->update([
            'tolok_ukur' => $request->tolok_ukur,
            'target' => $request->target,
            'satuan' => $request->satuan
        ]);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $indikator = Indikator::findOrFail($id);

        if (!in_array(strtolower(auth()->user()->role), ['admin','super admin'])) {
            return response()->json(['message' => 'Tidak punya akses'], 403);
        }

        $indikator->delete();

        return response()->json(['status' => 'success']);
    }
}
