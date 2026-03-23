<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    use HasFactory;

    protected $table = 'm_rekening';

    protected $fillable = [
        'parent_id',
        'kode_akun',
        'nama_akun',
        'level' // Enum: Akun, Kelompok, Jenis, Objek, Rincian Objek, Sub Rincian Objek
    ];

    // Relasi ke Induk
    public function parent()
    {
        return $this->belongsTo(Rekening::class, 'parent_id');
    }

    // Relasi ke Anak
    public function children()
    {
        // Tambahkan orderBy disini biar anak, cucu, cicit otomatis urut!
        return $this->hasMany(Rekening::class, 'parent_id')->orderBy('kode_akun', 'ASC');
    }

    // Relasi ke Standar Harga (SSH)
    public function standarHargas()
    {
        return $this->hasMany(StandarHarga::class, 'rekening_id');
    }

    public function asb()
    {
        return $this->hasMany(Asb::class);
    }


}