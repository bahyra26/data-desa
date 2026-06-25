<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    protected $fillable = [
        'kecamatan_id',
        'nama',
        'jenis',
    ];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function desas(): HasMany
    {
        return $this->hasMany(Desa::class);
    }

    public function perangkatWilayahs(): HasMany
    {
        return $this->hasMany(PerangkatWilayah::class);
    }
}
