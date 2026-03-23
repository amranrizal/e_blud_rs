<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SshExport;

class GenerateSshExcel extends Command
{
    protected $signature = 'ssh:generate {tahun}';
    protected $description = 'Generate Excel dari file JSON SSH';

    public function handle()
    {
        $tahun = $this->argument('tahun');

        $path = storage_path("app/ssh_full_{$tahun}.json");

        if (!file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");
            return;
        }

        $json = json_decode(file_get_contents($path), true);

        $data = collect($json)->map(function ($item) {
            return [
                $item['kode'] ?? '',
                $item['uraian'] ?? '',
                $item['spesifikasi'] ?? '',
                $item['satuan'] ?? '',
                $item['harga'] ?? 0,
                $item['tkdn'] ?? 0,
            ];
        })->toArray();

        $filename = "SSH_{$tahun}.xlsx";

        Excel::store(new SshExport($data), "public/{$filename}");

        $this->info("Berhasil generate: storage/app/public/{$filename}");
    }
}