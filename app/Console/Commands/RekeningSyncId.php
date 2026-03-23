<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StandarHarga;
use App\Models\Rekening;

class RekeningSyncId extends Command
{
    protected $signature = 'rekening:sync-id';
    protected $description = 'Sync rekening_id from standar_hargas.kode_akun';

    public function handle(): int
    {
        $count = 0;

        StandarHarga::whereNotNull('kode_akun')
            ->whereNull('rekening_id')
            ->chunk(100, function ($rows) use (&$count) {
                foreach ($rows as $ssh) {
                    $rekening = Rekening::where(
                        'kode_akun',
                        $ssh->kode_akun
                    )->first();

                    if ($rekening) {
                        $ssh->rekening_id = $rekening->id;
                        $ssh->save();
                        $count++;
                    }
                }
            });

        $this->info("Updated {$count} rows");

        return Command::SUCCESS;
    }
}
