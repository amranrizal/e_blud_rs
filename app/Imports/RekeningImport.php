<?php

namespace App\Imports;

use App\Models\Rekening;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class RekeningImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    public function model(array $row)
    {
        $rawKode = trim($row['kode_akun'] ?? '');
        $nama = trim($row['nama_akun'] ?? '');
        $parentKode = trim($row['parent_kode'] ?? '');

        if ($rawKode == '') {
            return null;
        }

        // 🔥 Pisahkan kode dan teks jika bercampur
        $parts = explode('.', $rawKode);

        $kodeSegments = [];
        $namaTambahan = [];

        foreach ($parts as $part) {
            if (ctype_digit($part)) {
                $kodeSegments[] = $part;
            } else {
                $namaTambahan[] = $part;
            }
        }

        // Maksimal 6 segmen kode
        $kodeSegments = array_slice($kodeSegments, 0, 6);

        $kode = implode('.', $kodeSegments);

        // Jika nama kosong, ambil dari sisa teks
        if (!empty($namaTambahan)) {
        $nama = implode('.', $namaTambahan);
        }

        if ($kode == '' || $nama == '') {
            return null;
        }

        // 🔥 Batasi panjang nama
        $nama = mb_substr($nama, 0, 1000);

        // 🔗 Cari Parent
        $parentId = null;

        if ($parentKode != '') {
            $induk = Rekening::where('kode_akun', $parentKode)->first();
            if ($induk) {
                $parentId = $induk->id;
            }
        }

        // 🎯 Tentukan Level
        $count = count($kodeSegments);

        switch ($count) {
            case 1: $level = 'Akun'; break;
            case 2: $level = 'Kelompok'; break;
            case 3: $level = 'Jenis'; break;
            case 4: $level = 'Objek'; break;
            case 5: $level = 'Rincian Objek'; break;
            case 6: $level = 'Sub Rincian Objek'; break;
            default: return null;
        }

        

        return new Rekening([
            'kode_akun' => $kode,
            'nama_akun' => $nama,
            'parent_id' => $parentId,
            'level'     => $level,
        ]);
    }



    // 🚀 Supaya tidak timeout
    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
