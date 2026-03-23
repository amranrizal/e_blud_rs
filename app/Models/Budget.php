<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StandarHarga;


class Budget extends Model
{
    use HasFactory;

    protected $table = 'budgets';

    protected $fillable = [
        'pagu_indikatif_id',
        'unit_id',
        'sub_kegiatan_id',
        'kode_akun',
        'uraian',
        'harga_satuan',
        'volume',
        'satuan',
        'total_anggaran',
        'tahun',
        'status',
        'standar_harga_id',
        'is_manual',
        'keterangan_harga'
    ];

    protected $casts = [
        'harga_satuan' => 'float',
        'volume' => 'float',
        'total_anggaran' => 'float',
        'is_manual' => 'boolean'
    ];


    /*
    |--------------------------------------------------------------------------
    | HEADER
    |--------------------------------------------------------------------------
    */

    public function pagu()
    {
        return $this->belongsTo(PaguIndikatif::class, 'pagu_indikatif_id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI TAMBAHAN (OPTIONAL)
    |--------------------------------------------------------------------------
    */

    public function unit()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_id', 'id');
    }

    public function subKegiatan()
    {
        return $this->belongsTo(Program::class, 'sub_kegiatan_id', 'id');
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'kode_akun', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    public function getTotalAttribute()
    {
        return $this->total_anggaran;
    }

    public function standarHarga()
    {
        return $this->belongsTo(StandarHarga::class);
    }

}
