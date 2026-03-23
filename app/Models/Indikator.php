<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    use HasFactory;

    protected $fillable = [
        'm_program_id',
        'jenis',
        'tolok_ukur',
        'target',
        'satuan',
        'tahun',
        'created_by'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'm_program_id');
    }
}
