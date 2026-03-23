<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hspk extends Model
{
    protected $table = 'hspk';

    protected $fillable = [
        'uraian',
        'satuan',
        'rekening_id',
        'tahun',
        'harga_total',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(HspkItem::class);
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIKA HITUNG TOTAL
    |--------------------------------------------------------------------------
    */

    public function hitungTotal()
    {
        $total = $this->items()
            ->with('ssh')
            ->get()
            ->sum(function ($item) {
                return $item->koefisien * $item->ssh->harga;
            });

        $this->update([
            'harga_total' => $total
        ]);
    }
}
