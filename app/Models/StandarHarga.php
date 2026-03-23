<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandarHarga extends Model
{
    protected $table = 'standar_hargas';

    /**
     * HANYA field SSH murni yang boleh diisi dari request
     */
    protected $fillable = [
        'kode_kelompok', 
        'kode_barang',  
        'uraian',
        'spesifikasi',
        'satuan',
        'harga',
        'rekening_id',
        'tahun',
        'parent_id',
        'is_group'
    ];

    /**
     * Kolom legacy & sensitif DIKUNCI
     */
    protected $guarded = [
        'id',
        'kode_kelompok',
        'deprecated_kode_kelompok',
        'kode_barang',
        'kode_akun',
        'created_at',
        'updated_at',
    ];

    /**
     * Relasi ke rekening (source of truth kode_akun)
     */
    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }

    public function hspkItems()
    {
        return $this->hasMany(HspkItem::class, 'standar_harga_id');
    }

    public function sbuParameters()
    {
        return $this->hasMany(SbuParameter::class)
                ->orderBy('urutan');
    }

    public function hitungTotalSbu(array $inputParameters = [])
    {
        $total = $this->harga;

        foreach ($this->sbuParameters as $param) {

            $nilai = $inputParameters[$param->kode_parameter]
                ?? $param->nilai_default
                ?? 1;

            $total *= $nilai;
        }

        return $total;
    }

    public function parent()
    {
        return $this->belongsTo(StandarHarga::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(StandarHarga::class, 'parent_id')
            ->where('is_group', 0)
            ->whereNotNull('parent_id') // 🔥 double safety
            ->orderBy('kode_barang');
    }

}
