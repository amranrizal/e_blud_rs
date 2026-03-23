<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rekening;

class ImportSipdRekening extends Command
{
    protected $signature = 'import:sipd-rekening';
    protected $description = 'Import rekening BAS SIPD 2026';

    public function handle()
    {
        $path = storage_path('app/sipd_akun_2026_full.json');

        if (!file_exists($path)) {
            $this->error('File JSON tidak ditemukan!');
            return;
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true);

        foreach ($data as $item) {

            $kode = $item['kode_akun'];
            $nama = $item['nama_akun'];

            $segments = explode('.', $kode);
            $level = count($segments);

            $parentKode = null;
            if ($level > 1) {
                array_pop($segments);
                $parentKode = implode('.', $segments);
            }

            $parentId = null;

            if ($parentKode) {
                $parent = Rekening::where('kode_akun', $parentKode)->first();
                if ($parent) {
                    $parentId = $parent->id;
                }
            }

            Rekening::updateOrCreate(
                ['kode_akun' => $kode],
                [
                    'nama_akun' => $nama,
                    'level' => $level,
                    'parent_id' => $parentId
                ]
            );
        }

        $this->info('Import BAS SIPD selesai!');
    }
}