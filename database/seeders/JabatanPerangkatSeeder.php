<?php

namespace Database\Seeders;

use App\Models\JabatanPerangkat;
use Illuminate\Database\Seeder;

class JabatanPerangkatSeeder extends Seeder
{
    /**
     * Seed perangkat jabatan master data.
     */
    public function run(): void
    {
        $jabatans = [
            'Kepala Desa',
            'Sekretaris Desa',
            'Kaur Keuangan',
            'Kaur Umum',
            'Kaur Perencanaan',
            'Kasi Pemerintahan',
            'Kasi Kesejahteraan',
            'Kasi Pelayanan',
            'Kepala Dusun',
        ];

        foreach ($jabatans as $index => $nama) {
            JabatanPerangkat::updateOrCreate([
                'nama' => $nama,
            ], [
                'level_urutan' => $index + 1,
            ]);
        }
    }
}
