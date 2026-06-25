<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Desa extends Model
{
    protected $fillable = [
        'wilayah_id',
        'alamat_kantor',
        'kepala_desa',
        'jumlah_penduduk',
        'luas_wilayah',
    ];

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }
}
