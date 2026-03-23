<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IndikatorKinerja;
use Illuminate\Support\Facades\DB;

class IndikatorKinerjaController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'pagu_indikatif_id' => 'required|exists:pagu_indikatifs,id', // Sesuaikan nama tabel
            'jenis'      => 'required|in:Capaian Program,Keluaran,Hasil',
            'tolok_ukur' => 'required|string',
            'target'     => 'required|string',
            'satuan'     => 'required|string',
        ]);

        try {
            IndikatorKinerja::create([
                'pagu_indikatif_id' => $request->pagu_indikatif_id,
                'jenis'      => $request->jenis,
                'tolok_ukur' => $request->tolok_ukur,
                'target'     => $request->target,
                'satuan'     => $request->satuan,
            ]);

            return back()->with('success', 'Indikator Kinerja berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $indikator = IndikatorKinerja::findOrFail($id);
        $indikator->delete();

        return back()->with('success', 'Indikator berhasil dihapus.');
    }

    // ==========================================
    // TAMBAHKAN FUNCTION BARU INI DI BAWAH SINI
    // ==========================================
    
    public function updateFromRka(Request $request)
    {
        // 1. Validasi ID Pagu (dari hidden input modal)
        $request->validate([
            'id_bl_indikator' => 'required',
        ]);

        $paguId = $request->id_bl_indikator;

        try {
            DB::beginTransaction();

            // Mapping data dari Form Modal ke Database
            $listIndikator = [
                // 1. Capaian Program
                [
                    'jenis' => 'Capaian Program',
                    'tolok_ukur' => $request->capaian_tolok_ukur,
                    'target' => $request->capaian_target,
                    'satuan' => $request->capaian_satuan ?? '%'
                ],
                // 2. Masukan (Input)
                [
                    'jenis' => 'Masukan',
                    'tolok_ukur' => $request->masukan_tolok_ukur,
                    'target' => $request->masukan_target, // Nominal Uang
                    'satuan' => 'Rupiah'
                ],
                // 3. Keluaran (Output)
                [
                    'jenis' => 'Keluaran',
                    'tolok_ukur' => $request->keluaran_tolok_ukur,
                    'target' => $request->keluaran_target,
                    'satuan' => $request->keluaran_satuan ?? 'Dokumen'
                ],
                // 4. Hasil (Outcome)
                [
                    'jenis' => 'Hasil',
                    'tolok_ukur' => $request->hasil_tolok_ukur,
                    'target' => $request->hasil_target,
                    'satuan' => $request->hasil_satuan ?? '%'
                ],
            ];

            // Loop simpan/update
            foreach ($listIndikator as $item) {
                // Gunakan updateOrCreate agar tidak duplikat
                IndikatorKinerja::updateOrCreate(
                    [
                        'pagu_indikatif_id' => $paguId, 
                        'jenis'             => $item['jenis']
                    ],
                    [
                        'tolok_ukur' => $item['tolok_ukur'],
                        'target'     => $item['target'],
                        'satuan'     => $item['satuan'],
                    ]
                );
            }

            DB::commit();

            // Return JSON karena request dari AJAX
            return response()->json([
                'status' => 'success', 
                'message' => 'Indikator Kinerja berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error', 
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }
}