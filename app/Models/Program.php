<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'm_program';
    protected $primaryKey = 'id';

    protected $fillable = [
        'parent_id',
        'kode_program', // <--- Ganti 'kode' jadi 'kode_program'
        'nama_program', // <--- Ganti 'nama' jadi 'nama_program'
        'level',
    ];

    // Relasi ke Parent (Untuk melihat siapa induknya)
    public function parent()
    {
        return $this->belongsTo(Program::class, 'parent_id', 'id');
    }

    // Relasi ke Child (Untuk melihat sub-nya, misal Program punya banyak Kegiatan)
    public function children()
    {
         return $this->hasMany(Program::class, 'parent_id', 'id')->with('children');
    }

    public function paguIndikatifs()
    {
        return $this->hasMany(PaguIndikatif::class, 'sub_kegiatan_id');
    }

    public function indikators()
    {
        return $this->hasMany(Indikator::class, 'm_program_id');
    }
    
    public function renjas()
    {
        return $this->hasMany(Renja::class,'sub_kegiatan_id');
    }
}