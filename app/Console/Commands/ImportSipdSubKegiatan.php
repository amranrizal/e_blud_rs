<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Program;
use Illuminate\Support\Facades\DB;

class ImportSipdSubKegiatan extends Command
{
    protected $signature = 'sipd:import-sub {file}';
    protected $description = 'Import SIPD Sub Kegiatan (6 segmen)';

    public function handle()
    {
        $path = storage_path('app/' . $this->argument('file'));

        if (!file_exists($path)) {
            $this->error("File tidak ditemukan.");
            return;
        }

        $data = json_decode(file_get_contents($path), true);

        DB::beginTransaction();

        try {

            $inserted = 0;

            foreach ($data as $row) {

                $kode = trim($row['kode']);
                $nama = trim($row['nama']);

                $segments = explode('.', $kode);

                if (count($segments) !== 6) {
                    continue;
                }

                // ===== Parent Kegiatan (5 segmen) =====
                $kodeKegiatan = implode('.', array_slice($segments, 0, 5));

                $kegiatan = Program::where('kode_program', $kodeKegiatan)->first();

                if (!$kegiatan) {

                    // ===== Parent Program (3 segmen) =====
                    $kodeProgram = implode('.', array_slice($segments, 0, 3));

                    $program = Program::where('kode_program', $kodeProgram)->first();

                    if (!$program) {
                        $program = Program::create([
                            'kode_program' => $kodeProgram,
                            'nama_program' => '[AUTO GENERATED PROGRAM]',
                            'level' => 3,
                            'parent_id' => null
                        ]);

                        $this->warn("Auto create program: $kodeProgram");
                    }

                    $kegiatan = Program::create([
                        'kode_program' => $kodeKegiatan,
                        'nama_program' => '[AUTO GENERATED KEGIATAN]',
                        'level' => 4,
                        'parent_id' => $program->id
                    ]);

                    $this->warn("Auto create kegiatan: $kodeKegiatan");
                }

                // ===== Insert Sub Kegiatan =====
                Program::updateOrCreate(
                    ['kode_program' => $kode],
                    [
                        'nama_program' => $nama,
                        'level' => 5,
                        'parent_id' => $kegiatan->id,
                    ]
                );

                $inserted++;
            }

            DB::commit();

            $this->info("Import selesai. Inserted/Updated: $inserted");

        } catch (\Exception $e) {

            DB::rollBack();
            $this->error($e->getMessage());
        }
    }
}