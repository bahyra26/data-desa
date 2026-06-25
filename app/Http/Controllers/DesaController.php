<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\PerangkatWilayah;
use App\Models\Wilayah;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DesaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $this->validateIndexFilters($request);
        $search = $filters['search'] ?? null;
        $kecamatanId = $filters['kecamatan_id'] ?? null;
        $searchLike = $search ? '%'.$this->escapeLike($search).'%' : null;

        $desas = Desa::with('wilayah.kecamatan')
            ->when($searchLike, function ($query, string $searchLike) {
                $query->where(function ($query) use ($searchLike) {
                    $query->where('kepala_desa', 'like', $searchLike)
                        ->orWhere('alamat_kantor', 'like', $searchLike)
                        ->orWhereHas('wilayah', function ($query) use ($searchLike) {
                            $query->where('nama', 'like', $searchLike)
                                ->orWhereHas('kecamatan', function ($query) use ($searchLike) {
                                    $query->where('nama', 'like', $searchLike);
                                });
                        });
                });
            })
            ->when($kecamatanId, function ($query, string $kecamatanId) {
                $query->whereHas('wilayah', function ($query) use ($kecamatanId) {
                    $query->where('kecamatan_id', $kecamatanId);
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('desa.index', [
            'desas' => $desas,
            'kecamatans' => Kecamatan::orderBy('nama')->get(),
            'filters' => [
                'search' => $search ?? '',
                'kecamatan_id' => $kecamatanId ?? '',
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('desa.create', $this->formData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $desa = Desa::create($this->validateDesa($request));
        $desa->load('wilayah');

        ActivityLogger::log(
            $request,
            'create',
            'desa',
            'Menambahkan data desa '.$desa->wilayah->nama.'.',
            $desa,
            null,
            $desa->toArray()
        );

        return redirect()->route('desa.index')->with('success', 'Data desa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Desa $desa)
    {
        $desa->load('wilayah.kecamatan');

        $perangkatAktif = PerangkatWilayah::with('jabatanPerangkat')
            ->where('wilayah_id', $desa->wilayah_id)
            ->where('status', 'aktif')
            ->join('jabatan_perangkats', 'perangkat_wilayahs.jabatan_perangkat_id', '=', 'jabatan_perangkats.id')
            ->orderBy('jabatan_perangkats.level_urutan')
            ->orderBy('perangkat_wilayahs.nama')
            ->select('perangkat_wilayahs.*')
            ->get();

        $riwayatPerangkat = PerangkatWilayah::with('jabatanPerangkat')
            ->where('wilayah_id', $desa->wilayah_id)
            ->whereIn('status', ['nonaktif', 'selesai', 'pensiun'])
            ->orderByDesc('akhir_menjabat')
            ->orderByDesc('updated_at')
            ->get();

        return view('desa.show', compact('desa', 'perangkatAktif', 'riwayatPerangkat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Desa $desa)
    {
        $desa->load('wilayah.kecamatan');

        return view('desa.edit', array_merge($this->formData(), compact('desa')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Desa $desa)
    {
        $oldValues = $desa->toArray();

        $desa->update($this->validateDesa($request, $desa));
        $desa->load('wilayah');

        ActivityLogger::log(
            $request,
            'update',
            'desa',
            'Memperbarui data desa '.$desa->wilayah->nama.'.',
            $desa,
            $oldValues,
            $desa->fresh()->toArray()
        );

        return redirect()->route('desa.index')->with('success', 'Data desa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Desa $desa)
    {
        $desa->load('wilayah');
        $oldValues = $desa->toArray();
        $description = 'Menghapus data desa '.$desa->wilayah->nama.'.';

        $desa->delete();

        ActivityLogger::log(
            $request,
            'delete',
            'desa',
            $description,
            $desa,
            $oldValues,
            null
        );

        return redirect()->route('desa.index')->with('success', 'Data desa berhasil dihapus.');
    }

    private function validateDesa(Request $request, ?Desa $desa = null): array
    {
        return $request->validate([
            'kecamatan_id' => ['required', 'exists:kecamatans,id'],
            'wilayah_id' => [
                'required',
                Rule::exists('wilayahs', 'id')
                    ->where('kecamatan_id', $request->input('kecamatan_id'))
                    ->where('jenis', 'desa'),
                Rule::unique('desas', 'wilayah_id')->ignore($desa),
            ],
            'alamat_kantor' => ['nullable', 'string', 'max:1000'],
            'kepala_desa' => ['nullable', 'string', 'max:255'],
            'jumlah_penduduk' => ['nullable', 'integer', 'min:0'],
            'luas_wilayah' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function validateIndexFilters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kecamatan_id' => ['nullable', 'integer', 'exists:kecamatans,id'],
        ]);
    }

    private function escapeLike(string $value): string
    {
        return addcslashes($value, '\%_');
    }

    private function formData(): array
    {
        $wilayahs = Wilayah::with('kecamatan')->where('jenis', 'desa')->orderBy('nama')->get();

        return [
            'kecamatans' => Kecamatan::orderBy('nama')->get(),
            'wilayahs' => $wilayahs,
            'wilayahOptions' => $wilayahs->map(fn (Wilayah $wilayah): array => [
                'id' => $wilayah->id,
                'kecamatan_id' => $wilayah->kecamatan_id,
                'nama' => $wilayah->nama,
                'jenis' => $wilayah->jenis,
            ])->values(),
        ];
    }
}
