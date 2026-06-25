<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    protected $fillable = [
        'kode_kemendagri',
        'nama',
        'kode_pos',
    ];

    public function wilayahs(): HasMany
    {
        return $this->hasMany(Wilayah::class);
    }
}
