<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory;

    protected $table = 'm_unit_kerja';

    protected $guarded = ['id'];
    
    // Kolom yang boleh diisi lewat form
    protected $fillable = [
        'kode_unit', 
        'nama_unit',
        'kepa_unit', // Opsional: Nama Kepala Unit (jika ada di tabel)
    ];

    // Relasi ke RKT (Untuk nanti fase selanjutnya)
    public function rkts()
    {
        return $this->hasMany(Rkt::class, 'unit_id');
    }

        // Relasi ke Mapping Anggaran (Renja/Pagu)
    public function paguIndikatifs()
    {
        return $this->hasMany(PaguIndikatif::class, 'unit_id');
    }

    public function renjas()
    {
        return $this->hasMany(Renja::class,'unit_id');
    }
}