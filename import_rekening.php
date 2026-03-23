<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Rekening;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = storage_path('app/Rekening_FINAL_Kemendagri_Struktur_Benar.xlsx');

$rows = IOFactory::load($filePath)
    ->getActiveSheet()
    ->toArray();

DB::beginTransaction();

foreach ($rows as $index => $row) {

    if ($index == 0) continue;

    $kode = trim($row[0] ?? '');
    $nama = trim($row[1] ?? '');
    $parentKode = trim($row[2] ?? '');

    if ($kode == '' || $nama == '') continue;

    $parentId = null;

    if ($parentKode != '') {
        $parent = Rekening::where('kode_akun', $parentKode)->first();
        if ($parent) $parentId = $parent->id;
    }

    $count = count(explode('.', $kode));

    $level =
        $count == 1 ? 'Akun' :
        ($count == 2 ? 'Kelompok' :
        ($count == 3 ? 'Jenis' :
        ($count == 4 ? 'Objek' :
        ($count == 5 ? 'Rincian Objek' : 'Sub Rincian Objek'))));

    Rekening::updateOrCreate(
        ['kode_akun' => $kode],
        [
            'nama_akun' => $nama,
            'parent_id' => $parentId,
            'level'     => $level,
        ]
    );

}

DB::commit();

echo "IMPORT SELESAI 🔥\n";
