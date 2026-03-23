<?php

namespace App\Imports;

use App\Models\Program;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;


class ProgramImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
   public function model(array $row)
    {
        $kode = trim($row['kode_program'] ?? '');
        $nama = trim($row['nama_program'] ?? '');
        $level = trim($row['level'] ?? '');

        if (!$kode || !$nama) {
            return null;
        }

        $parentId = null;

        $segments = explode('.', $kode);

        // kegiatan
        if ($level === 'Kegiatan') {

            $kodeInduk = implode('.', array_slice($segments, 0, 3));

            $parentId = Program::where('kode_program', $kodeInduk)->value('id');

        }

        // sub kegiatan
        if ($level === 'Sub Kegiatan') {

            $kodeInduk = implode('.', array_slice($segments, 0, 5));

            $parentId = Program::where('kode_program', $kodeInduk)->value('id');

        }

        return Program::updateOrCreate(
            ['kode_program' => $kode],
            [
                'nama_program' => $nama,
                'parent_id' => $parentId,
                'level' => $level
            ]
        );
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }


}