<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'm_kegiatan';
    protected $guarded = [];

    public function subKegiatan() {
        return $this->hasMany(SubKegiatan::class, 'kegiatan_id');
    }
}