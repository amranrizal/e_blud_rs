<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SbuParameter extends Model
{
    protected $fillable = [
        'standar_harga_id',
        'kode_parameter',
        'label',
        'tipe',
        'nilai_default',
        'is_required',
        'urutan',
    ];

    public function standarHarga()
    {
        return $this->belongsTo(StandarHarga::class);
    }
}
