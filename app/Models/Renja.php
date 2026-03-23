<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renja extends Model
{
    protected $table = 'renja';

    protected $fillable = [
        'unit_id',
        'sub_kegiatan_id',
        'tahun',
        'indikator_kinerja',
        'target',
        'satuan',
        'pagu_rencana'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI KE UNIT KERJA
    |--------------------------------------------------------------------------
    */

    public function unit()
    {
        return $this->belongsTo(UnitKerja::class,'unit_id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI KE SUB KEGIATAN (PROGRAM SIPD)
    |--------------------------------------------------------------------------
    */

    public function subKegiatan()
    {
        return $this->belongsTo(Program::class,'sub_kegiatan_id');
    }

}