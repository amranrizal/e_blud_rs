<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaguIndikatifAudit extends Model
{
    protected $fillable = [
        'pagu_indikatif_id',
        'status_lama',
        'status_baru',
        'catatan',
        'user_id',
    ];

    public function pagu()
    {
        return $this->belongsTo(PaguIndikatif::class, 'pagu_indikatif_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
