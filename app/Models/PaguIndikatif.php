<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaguIndikatif extends Model
{
    use HasFactory;

    protected $table = 'pagu_indikatifs';

    protected $fillable = [
        'unit_id',
        'sub_kegiatan_id',
        'pagu',
        'tahun',
        'status_validasi',
        'catatan_revisi',
        'validator_id',
        'tgl_validasi'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI HEADER
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

    /*
    |--------------------------------------------------------------------------
    | DETAIL RINCIAN
    |--------------------------------------------------------------------------
    */

    public function budgets()
    {
        return $this->hasMany(Budget::class, 'pagu_indikatif_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER HITUNG
    |--------------------------------------------------------------------------
    */

    public function getTotalTerpakaiAttribute()
    {
        return $this->budgets()->sum('total_anggaran');
    }

    public function getSisaPaguAttribute()
    {
        return $this->pagu - $this->total_terpakai;
    }

    public function audits()
    {
        return $this->hasMany(PaguIndikatifAudit::class)
                    ->orderByDesc('created_at');
    }

}
