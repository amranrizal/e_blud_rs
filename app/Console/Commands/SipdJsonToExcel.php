<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;

class SipdJsonToExcel extends Command
{
    protected $signature = 'sipd:json-to-excel';
    protected $description = 'Convert JSON Sub Kegiatan SIPD ke Excel';

    public function handle()
    {

        $path = storage_path('app/kegiatan_xxx.json');

        if (!file_exists($path)) {
            $this->error("File JSON tidak ditemukan.");
            return;
        }

        $data = json_decode(file_get_contents($path), true);

        $rows = [];

        foreach ($data as $item) {

            $nama = $item['nama'];

            // potong mulai baris X.XX sampai akhir
            $nama = preg_replace('/\n\s*X\..*/s', '', $nama);

            // bersihkan newline sisa
            $nama = preg_replace('/[\r\n]+/', ' ', $nama);

            $nama = trim($nama);

            $rows[] = [
                'kode_program' => trim($item['kode']),
                'nama_program' => $nama,
                'level' => 'Kegiatan'
            ];
        }

        Excel::store(new class($rows) implements FromCollection {

            protected $rows;

            public function __construct($rows)
            {
                $this->rows = $rows;
            }

            public function collection()
            {
                return collect($this->rows);
            }

        }, 'kegiatan.xlsx');

        $this->info("Excel berhasil dibuat di storage/app/sub_kegiatan.xlsx");
    }
}