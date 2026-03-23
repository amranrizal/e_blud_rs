<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asb extends Model
{
    protected $table = 'asb';

    protected $fillable = [
        'kode',
        'uraian',
        'satuan',
        'tarif',
        'rekening_id',
        'tahun',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER (OPSIONAL)
    |--------------------------------------------------------------------------
    */

    // Hitung total berdasarkan volume
    public function hitungTotal($volume)
    {
        return $volume * $this->tarif;
    }
}
