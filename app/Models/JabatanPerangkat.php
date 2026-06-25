<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JabatanPerangkat extends Model
{
    protected $fillable = [
        'nama',
        'level_urutan',
    ];

    public function perangkatWilayahs(): HasMany
    {
        return $this->hasMany(PerangkatWilayah::class);
    }
}
