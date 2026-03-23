<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HspkItem extends Model
{
    protected $table = 'hspk_items';

    protected $fillable = [
        'hspk_id',
        'standar_harga_id',
        'koefisien',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function ssh()
    {
        return $this->belongsTo(StandarHarga::class, 'standar_harga_id');
    }

    public function hspk()
    {
        return $this->belongsTo(Hspk::class);
    }
}
