<?php

namespace App\Helpers;

class RekeningLevelHelper
{
    public static function nextLevel(?string $parentLevel): string
    {
        return match ($parentLevel) {
            null                => 'Akun',
            'Akun'              => 'Kelompok',
            'Kelompok'          => 'Jenis',
            'Jenis'             => 'Objek',
            'Objek'             => 'Rincian Objek',
            'Rincian Objek'     => 'Sub Rincian Objek',
            default             => abort(403, 'Level maksimal tercapai'),
        };
    }

    public static function detectLevelFromKode(?string $kode): string
    {
        if (!$kode) {
            return 'Akun';
        }

        $segments = explode('.', $kode);
        $count = count($segments);

        return match ($count) {
            1 => 'Akun',
            2 => 'Kelompok',
            3 => 'Jenis',
            4 => 'Objek',
            5 => 'Rincian Objek',
            6 => 'Sub Rincian Objek',
            default => 'Akun',
        };
    }

}
