<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorKinerja extends Model
{
    use HasFactory;

    protected $table = 'indikator_kinerja';
    protected $guarded = ['id']; // Biar bisa mass assignment

    protected $fillable = [
        'pagu_indikatif_id',
        'jenis',      // Input, Output, Outcome
        'tolok_ukur', // Uraian
        'target',     // Angka
        'satuan',     // Satuan
    ];

    // Relasi Balik ke Pagu Indikatif
    public function paguIndikatif()
    {
        return $this->belongsTo(PaguIndikatif::class, 'pagu_indikatif_id');
    }
}