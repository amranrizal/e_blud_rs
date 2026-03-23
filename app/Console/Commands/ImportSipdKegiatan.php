<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Program;
use Illuminate\Support\Facades\DB;

class ImportSipdKegiatan extends Command
{
    protected $signature = 'sipd:import-kegiatan {file}';
    protected $description = 'Import SIPD Kegiatan (5 segmen)';

    public function handle()
    {
        $path = storage_path('app/' . $this->argument('file'));

        if (!file_exists($path)) {
            $this->error("File tidak ditemukan: $path");
            return;
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!$data) {
            $this->error("JSON tidak valid");
            return;
        }

        DB::beginTransaction();

        try {

            $inserted = 0;

            foreach ($data as $row) {

                $kode = trim($row['kode']);
                $nama = trim($row['nama']);

                $segments = explode('.', $kode);

                // pastikan 5 segmen
                if (count($segments) !== 5) {
                    $this->warn("Skip bukan 5 segmen: $kode");
                    continue;
                }

                // parent = 3 segmen (Program)
                $parentKode = implode('.', array_slice($segments, 0, 3));

                $parent = Program::where('kode_program', $parentKode)->first();

                if (!$parent) {

                    $parent = Program::create([
                        'kode_program' => $parentKode,
                        'nama_program' => '[AUTO GENERATED PROGRAM]',
                        'level' => 3,
                        'parent_id' => null
                    ]);

                    $this->warn("Auto create missing program: $parentKode");
                }

                // skip jika sudah ada
                if (Program::where('kode_program', $kode)->exists()) {
                    continue;
                }

                Program::updateOrCreate(
                    ['kode_program' => $kode],
                    [
                        'nama_program' => $nama,
                        'level' => 4,
                        'parent_id' => $parent->id
                    ]
                );

                $inserted++;
            }

            DB::commit();

            $this->info("Import selesai. Inserted: $inserted");

        } catch (\Exception $e) {

            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
        }
    }
}