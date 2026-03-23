<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendapatan extends Model
{
    use HasFactory;

    protected $table = 't_pendapatan';

    protected $fillable = [
        'unit_id',
        'tahun',
        'kode_akun',
        'uraian',
        'volume',
        'satuan',
        'tarif',
        'jumlah',
    ];

    // Relasi ke Unit Kerja
    public function unit()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_id');
    }

    // Relasi ke Master Rekening (Untuk ambil nama akun otomatis)
    // Ingat: Akun Pendapatan biasanya dimulai angka 4
    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'kode_akun', 'kode_akun');
    }
}