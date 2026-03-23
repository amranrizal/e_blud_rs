<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekeningAudit extends Model
{
    protected $table = 'rekening_audits';

    protected $fillable = [
        'rekening_id',
        'action',
        'before',
        'after',
        'user_id',
    ];

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];

    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'rekening_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
