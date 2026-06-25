<?php

namespace App\Exports;

use App\Models\Desa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DesaExport implements FromCollection, WithHeadings
{
    public function __construct(private array $filters = [])
    {
    }

    public function collection(): Collection
    {
        $search = $this->filters['search'] ?? null;
        $kecamatanId = $this->filters['kecamatan_id'] ?? null;
        $searchLike = $search ? '%'.$this->escapeLike($search).'%' : null;

        return Desa::with('wilayah.kecamatan')
            ->when($searchLike, function ($query, string $searchLike) {
                $query->whereHas('wilayah', function ($query) use ($searchLike) {
                    $query->where('nama', 'like', $searchLike);
                });
            })
            ->when($kecamatanId, function ($query, string $kecamatanId) {
                $query->whereHas('wilayah', function ($query) use ($kecamatanId) {
                    $query->where('kecamatan_id', $kecamatanId);
                });
            })
            ->latest()
            ->get()
            ->map(fn (Desa $desa): array => [
                'kecamatan' => $desa->wilayah->kecamatan->nama,
                'desa' => $desa->wilayah->nama,
                'alamat_kantor' => $desa->alamat_kantor,
                'kepala_desa' => $desa->kepala_desa,
                'jumlah_penduduk' => $desa->jumlah_penduduk,
                'luas_wilayah' => $desa->luas_wilayah,
                'tanggal_dibuat' => optional($desa->created_at)->format('d/m/Y H:i'),
                'terakhir_diperbarui' => optional($desa->updated_at)->format('d/m/Y H:i'),
            ]);
    }

    public function headings(): array
    {
        return [
            'Kecamatan',
            'Desa',
            'Alamat Kantor',
            'Kepala Desa',
            'Jumlah Penduduk',
            'Luas Wilayah',
            'Tanggal Dibuat',
            'Terakhir Diperbarui',
        ];
    }

    private function escapeLike(string $value): string
    {
        return addcslashes($value, '\%_');
    }
}
