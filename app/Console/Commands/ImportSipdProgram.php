<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Program;

class ImportSipdProgram extends Command
{
    protected $signature = 'sipd:import-program {file}';
    protected $description = 'Import SIPD Program (Level 3) dari file JSON';

    public function handle()
    {
        $file = $this->argument('file');
        $path = storage_path("app/" . $file);

        if (!file_exists($path)) {
            $this->error("File tidak ditemukan: " . $path);
            return 1;
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!$data) {
            $this->error("JSON tidak valid.");
            return 1;
        }

        DB::transaction(function () use ($data) {

            $this->info("Hapus program lama level 3...");
            Program::where('level', 3)->delete();

            foreach ($data as $item) {

                $kode = trim($item['kode']);
                $nama = trim($item['nama']);

                $segments = explode('.', $kode);

                // VALIDASI HARUS 3 SEGMEN
                if (count($segments) !== 3) {
                    $this->warn("Skip kode tidak valid: $kode");
                    continue;
                }

                // Parent = 2 segmen pertama
                $parentKode = implode('.', array_slice($segments, 0, 2));

                $parent = Program::where('kode_program', $parentKode)->first();

                if (!$parent) {
                    $this->error("❌ Parent tidak ditemukan untuk kode: $kode (parent: $parentKode)");
                    continue;
                }

                Program::create([
                    'kode_program' => $kode,
                    'nama_program' => $nama,
                    'level' => 3,
                    'parent_id' => $parent->id
                ]);

                $this->line("✔ Import $kode");
            }
        });

        $this->info("✅ Import Program selesai.");
        return 0;
    }
}