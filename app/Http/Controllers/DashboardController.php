<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\PerangkatWilayah;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard summary.
     */
    public function index(Request $request)
    {
        $hariIni = Carbon::today();
        $batasPeringatan = Carbon::today()->addDays(90);

        $stats = [
            'total_kecamatan' => Kecamatan::count(),
            'total_desa' => Wilayah::where('jenis', 'desa')->count(),
            'total_wilayah' => Wilayah::count(),
            'total_data_desa' => Desa::count(),
            'total_perangkat_aktif' => PerangkatWilayah::where('status', 'aktif')->count(),
            'total_perangkat_hampir_berakhir' => PerangkatWilayah::where('status', 'aktif')
                ->whereNotNull('akhir_menjabat')
                ->where(function ($query) use ($hariIni) {
                    $query->whereNull('mulai_menjabat')
                        ->orWhereDate('mulai_menjabat', '<=', $hariIni);
                })
                ->whereDate('akhir_menjabat', '>=', $hariIni)
                ->whereDate('akhir_menjabat', '<=', $batasPeringatan)
                ->count(),
            'total_perangkat_berakhir' => PerangkatWilayah::where('status', 'aktif')
                ->whereNotNull('akhir_menjabat')
                ->whereDate('akhir_menjabat', '<', $hariIni)
                ->count(),
        ];

        $desasTerbaru = Desa::with('wilayah.kecamatan')
            ->latest()
            ->limit(5)
            ->get();

        $peringatanMasaJabatan = PerangkatWilayah::with('wilayah.kecamatan', 'jabatanPerangkat')
            ->where('status', 'aktif')
            ->whereNotNull('akhir_menjabat')
            ->whereDate('akhir_menjabat', '<=', $batasPeringatan)
            ->orderBy('akhir_menjabat')
            ->limit(10)
            ->get();

        $perangkatTerdekatAkhirMasaJabatan = PerangkatWilayah::with('wilayah.kecamatan', 'jabatanPerangkat')
            ->where('status', 'aktif')
            ->whereNotNull('akhir_menjabat')
            ->whereDate('akhir_menjabat', '>=', $hariIni)
            ->orderBy('akhir_menjabat')
            ->limit(5)
            ->get();

        $wilayahPerKecamatan = Kecamatan::with([
            'wilayahs' => fn ($query) => $query->withCount('desas')->orderBy('nama'),
        ])
            ->withCount([
                'wilayahs as total_wilayah',
                'wilayahs as total_desa' => fn ($query) => $query->where('jenis', 'desa'),
            ])
            ->orderBy('nama')
            ->get();

        // ── Data untuk dropdown desa ──
        $wilayahOptions = Wilayah::with('kecamatan')
            ->where('jenis', 'desa')
            ->orderBy('nama')
            ->get()
            ->map(fn ($w) => [
                'id'      => $w->id,
                'kecamatan_id'  => $w->kecamatan_id,
                'kecamatan_nama' => $w->kecamatan->nama,
                'nama'    => $w->nama,
            ])
            ->values();

        // ── Detail desa yang dipilih ──
        $selectedDesaDetail = null;
        $selectedDesaPerangkat = null;

        if ($request->filled('wilayah_id')) {
            $selectedWilayah = Wilayah::with('kecamatan')
                ->where('jenis', 'desa')
                ->find($request->wilayah_id);

            if ($selectedWilayah) {
                $data = [
                    'wilayah_id'       => $selectedWilayah->id,
                    'nama'             => $selectedWilayah->nama,
                    'jenis'            => $selectedWilayah->jenis,
                    'kecamatan_nama'   => $selectedWilayah->kecamatan->nama,
                    'kode_kemendagri'  => $selectedWilayah->kecamatan->kode_kemendagri,
                    'kode_pos'         => $selectedWilayah->kecamatan->kode_pos,
                ];

                $desaData = Desa::where('wilayah_id', $selectedWilayah->id)->first();
                if ($desaData) {
                    $data = array_merge($data, [
                        'desa_id'         => $desaData->id,
                        'kepala_desa'     => $desaData->kepala_desa,
                        'alamat_kantor'   => $desaData->alamat_kantor,
                        'jumlah_penduduk' => $desaData->jumlah_penduduk,
                        'luas_wilayah'    => $desaData->luas_wilayah,
                    ]);
                }

                $selectedDesaDetail = $data;

                $selectedDesaPerangkat = PerangkatWilayah::with('jabatanPerangkat')
                    ->where('wilayah_id', $selectedWilayah->id)
                    ->orderByRaw("CASE status WHEN 'aktif' THEN 0 WHEN 'nonaktif' THEN 1 WHEN 'selesai' THEN 2 WHEN 'pensiun' THEN 3 ELSE 4 END")
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            }
        }

        return view('dashboard', compact(
            'stats',
            'desasTerbaru',
            'peringatanMasaJabatan',
            'perangkatTerdekatAkhirMasaJabatan',
            'hariIni',
            'wilayahPerKecamatan',
            'wilayahOptions',
            'selectedDesaDetail',
            'selectedDesaPerangkat',
        ));
    }
}
