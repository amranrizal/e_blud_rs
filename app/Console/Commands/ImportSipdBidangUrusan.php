<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Program;

class ImportSipdBidangUrusan extends Command
{
    protected $signature = 'import:sipd-bidang-urusan';
    protected $description = 'Import Bidang Urusan SIPD';

    public function handle()
    {
        $path = storage_path('app/sipd_bidang_urusan_clean.json');

        if (!file_exists($path)) {
            $this->error('File JSON tidak ditemukan!');
            return;
        }

        $data = json_decode(file_get_contents($path), true);

        foreach ($data as $item) {

            $kode = $item['kode'];
            $nama = $item['nama'];

            $segments = explode('.', $kode);
            $kodeUrusan = $segments[0];

            // ======================
            // HANDLE URUSAN (Level 1)
            // ======================
            $urusan = Program::firstOrCreate(
                ['kode_program' => $kodeUrusan],
                [
                    'nama_program' => 'URUSAN ' . $kodeUrusan,
                    'level' => 'Urusan',
                    'parent_id' => null
                ]
            );

            // ======================
            // HANDLE BIDANG URUSAN (Level 2)
            // ======================
            Program::updateOrCreate(
                ['kode_program' => $kode],
                [
                    'nama_program' => $nama,
                    'level' => 'Bidang Urusan',
                    'parent_id' => $urusan->id
                ]
            );
        }

        $this->info('Import Bidang Urusan selesai!');
    }
}