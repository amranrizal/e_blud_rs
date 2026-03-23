<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pejabat extends Model
{
    use HasFactory;

    // 1. Sesuaikan Nama Tabel
    // Cek di phpMyAdmin, namanya 'pejabats' atau 'm_pejabat'?
    // Kalau default laravel biasanya 'pejabats'. Kalau custom ganti string ini.
    protected $table = 'm_pejabat'; 

    // 2. Matikan Timestamps (Karena di gambar tidak ada created_at & updated_at)
    public $timestamps = false;

    // 3. Daftar kolom yang boleh diisi (Sesuai gambar)
    protected $fillable = [
        'unit_id',
        'nama',
        'nip',
        'jabatan',
        'is_active'
    ];

    public function unit()
    {
    return $this->belongsTo(UnitKerja::class, 'unit_id', 'id');
    }
}