<?php

namespace App\Imports;

use App\Models\StandarHarga;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class StandarHargaImport implements OnEachRow, WithHeadingRow, WithChunkReading
{
    protected $tahun;

    private function cleanRekening($value)
    {
        if (!$value) return null;

        $value = str_replace(["\n", "\r"], '', $value);

        $arr = explode(',', $value);
        $arr = array_filter(array_map('trim', $arr));

        return implode(',', $arr);
    }
   
    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function onRow(Row $row)
    {
        $data = $row->toArray();

        if (empty($data['kode_barang'])) {
            return;
        }

        DB::beginTransaction();

        try {

            // ==============================
            // AMBIL DATA
            // ==============================

            $kodeBarang   = trim($data['kode_barang']);
            $kodeKelompok = trim($data['kode_kelompok_barang'] ?? '');

            // ==============================
            // BUAT GROUP (6 SEGMENT)
            // ==============================

            $parent = null;

            if ($kodeKelompok) {

                $parent = StandarHarga::firstOrCreate(
                    [
                        'kode_barang' => $kodeKelompok,
                        'tahun' => $this->tahun
                    ],
                    [
                        'kode_kelompok' => 'SSH',
                        'uraian' => $kodeKelompok . ' - ' . ($data['uraian_kelompok_barang'] ?? ''),
                        'uraian_kelompok' => $data['uraian_kelompok_barang'] ?? null,
                        'spesifikasi' => null,
                        'satuan' => null,
                        'harga' => null,
                        'kode_rekening' => null,
                        'id_standar_harga' => null,
                        'is_group' => 1,
                        'parent_id' => null
                    ]
                );
            }

            // ==============================
            // SIMPAN ITEM (7 SEGMENT)
            // ==============================

            $model = StandarHarga::firstOrNew([
                'kode_barang' => $kodeBarang,
                'tahun' => $this->tahun
            ]);

            $model->kode_kelompok = 'SSH';
            $model->uraian = $data['uraian_barang'] ?? null;
            $model->spesifikasi = $data['spesifikasi'] ?? null;
            $model->satuan = $data['satuan'] ?? null;
            $model->harga = (float) ($data['harga_satuan'] ?? 0);
            $model->kode_rekening = $this->cleanRekening($data['kode_rekening'] ?? null);
            $model->id_standar_harga = $data['id_standar_harga'] ?? null;
            $model->uraian_kelompok = $data['uraian_kelompok_barang'] ?? null;
            $model->parent_id = $parent?->id;
            $model->is_group = 0;

            $model->save();

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

        }
    }
}