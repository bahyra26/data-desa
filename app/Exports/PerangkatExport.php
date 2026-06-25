<?php

namespace App\Exports;

use App\Models\PerangkatWilayah;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PerangkatExport implements FromCollection, WithHeadings
{
    public function __construct(private array $filters = [])
    {
    }

    public function collection(): Collection
    {
        $search = $this->filters['search'] ?? null;
        $kecamatanId = $this->filters['kecamatan_id'] ?? null;
        $jabatanId = $this->filters['jabatan_perangkat_id'] ?? null;
        $status = $this->filters['status'] ?? null;
        $masaJabatan = $this->filters['masa_jabatan'] ?? null;
        $searchLike = $search ? '%'.$this->escapeLike($search).'%' : null;

        return PerangkatWilayah::with('wilayah.kecamatan', 'jabatanPerangkat')
            ->when($searchLike, fn ($query) => $query->where('nama', 'like', $searchLike))
            ->when($kecamatanId, function ($query, string $kecamatanId) {
                $query->whereHas('wilayah', fn ($query) => $query->where('kecamatan_id', $kecamatanId));
            })
            ->when($jabatanId, fn ($query, string $jabatanId) => $query->where('jabatan_perangkat_id', $jabatanId))
            ->when($status, fn ($query, string $status) => $query->where('status', $status))
            ->when($masaJabatan, fn ($query, string $masaJabatan) => $this->applyMasaJabatanFilter($query, $masaJabatan))
            ->latest()
            ->get()
            ->map(fn (PerangkatWilayah $perangkat): array => [
                'nama' => $perangkat->nama,
                'jabatan' => $perangkat->jabatanPerangkat->nama,
                'kecamatan' => $perangkat->wilayah->kecamatan->nama,
                'desa' => $perangkat->wilayah->nama,
                'jenis_kelamin' => $perangkat->jenis_kelamin,
                'nomor_hp' => $perangkat->nomor_hp,
                'email' => $perangkat->email,
                'mulai_menjabat' => optional($perangkat->mulai_menjabat)->format('d/m/Y'),
                'akhir_menjabat' => optional($perangkat->akhir_menjabat)->format('d/m/Y'),
                'status' => ucfirst($perangkat->status),
                'countdown_masa_jabatan' => $perangkat->countdown_masa_jabatan,
            ]);
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Jabatan',
            'Kecamatan',
            'Desa',
            'Jenis Kelamin',
            'Nomor HP',
            'Email',
            'Mulai Menjabat',
            'Akhir Menjabat',
            'Status',
            'Countdown Masa Jabatan',
        ];
    }

    private function applyMasaJabatanFilter($query, string $masaJabatan): void
    {
        $hariIni = now()->toDateString();
        $batasPeringatan = now()->addDays(90)->toDateString();

        match ($masaJabatan) {
            'tanpa_batas' => $query->whereNull('akhir_menjabat'),
            'belum_mulai' => $query->whereNotNull('akhir_menjabat')
                ->whereNotNull('mulai_menjabat')
                ->whereDate('mulai_menjabat', '>', $hariIni),
            'berakhir' => $query->whereNotNull('akhir_menjabat')
                ->whereDate('akhir_menjabat', '<', $hariIni),
            'hampir_berakhir' => $query->whereNotNull('akhir_menjabat')
                ->where(function ($query) use ($hariIni) {
                    $query->whereNull('mulai_menjabat')
                        ->orWhereDate('mulai_menjabat', '<=', $hariIni);
                })
                ->whereDate('akhir_menjabat', '>=', $hariIni)
                ->whereDate('akhir_menjabat', '<=', $batasPeringatan),
            'aktif' => $query->whereNotNull('akhir_menjabat')
                ->where(function ($query) use ($hariIni) {
                    $query->whereNull('mulai_menjabat')
                        ->orWhereDate('mulai_menjabat', '<=', $hariIni);
                })
                ->whereDate('akhir_menjabat', '>', $batasPeringatan),
        };
    }

    private function escapeLike(string $value): string
    {
        return addcslashes($value, '\%_');
    }
}
