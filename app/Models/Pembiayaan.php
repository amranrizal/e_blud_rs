<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembiayaan extends Model
{
    use HasFactory;

    protected $table = 't_pembiayaan';
    protected $guarded = ['id'];

    // Relasi ke Rekening (untuk ambil nama akun)
    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'kode_akun', 'kode_akun');
    }
}
