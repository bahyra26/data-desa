<?php

namespace App\Http\Controllers;

use App\Models\JabatanPerangkat;
use App\Models\Kecamatan;
use App\Models\PerangkatWilayah;
use App\Models\Wilayah;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PerangkatWilayahController extends Controller
{
    private const STATUS_OPTIONS = ['aktif', 'nonaktif', 'selesai', 'pensiun'];

    private const JABATAN_UTAMA = [
        'Kepala Desa',
        'Sekretaris Desa',
    ];

    private const JABATAN_DESA_ONLY = [
        'Kepala Desa',
        'Sekretaris Desa',
        'Kaur Keuangan',
        'Kaur Umum',
        'Kaur Perencanaan',
        'Kepala Dusun',
    ];

    /**
     * Display a listing of perangkat wilayah.
     */
    public function index(Request $request)
    {
        $filters = $this->validateIndexFilters($request);
        $search = $filters['search'] ?? null;
        $kecamatanId = $filters['kecamatan_id'] ?? null;
        $jabatanId = $filters['jabatan_perangkat_id'] ?? null;
        $status = $filters['status'] ?? null;
        $masaJabatan = $filters['masa_jabatan'] ?? null;
        $searchLike = $search ? '%'.$this->escapeLike($search).'%' : null;

        $perangkats = PerangkatWilayah::with('wilayah.kecamatan', 'wilayah.desas', 'jabatanPerangkat')
            ->when($searchLike, function ($query, string $searchLike) {
                $query->where(function ($query) use ($searchLike) {
                    $query->where('nama', 'like', $searchLike)
                        ->orWhere('nomor_hp', 'like', $searchLike)
                        ->orWhere('email', 'like', $searchLike)
                        ->orWhereHas('jabatanPerangkat', function ($query) use ($searchLike) {
                            $query->where('nama', 'like', $searchLike);
                        })
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
            ->when($jabatanId, function ($query, string $jabatanId) {
                $query->where('jabatan_perangkat_id', $jabatanId);
            })
            ->when($status, function ($query, string $status) {
                $query->where('status', $status);
            })
            ->when($masaJabatan, function ($query, string $masaJabatan) {
                $this->applyMasaJabatanFilter($query, $masaJabatan);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('perangkat.index', [
            'perangkats' => $perangkats,
            'kecamatans' => Kecamatan::orderBy('nama')->get(),
            'jabatans' => JabatanPerangkat::orderBy('level_urutan')->get(),
            'filters' => [
                'search' => $search ?? '',
                'kecamatan_id' => $kecamatanId ?? '',
                'jabatan_perangkat_id' => $jabatanId ?? '',
                'status' => $status ?? '',
                'masa_jabatan' => $masaJabatan ?? '',
            ],
        ]);
    }

    /**
     * Show the form for creating perangkat wilayah.
     */
    public function create()
    {
        return view('perangkat.create', $this->formData());
    }

    /**
     * Store a newly created perangkat wilayah.
     */
    public function store(Request $request)
    {
        $data = $this->validatePerangkat($request);
        $data['foto'] = $this->storeFoto($request);

        $perangkat = PerangkatWilayah::create($data);

        ActivityLogger::log(
            $request,
            'create',
            'perangkat',
            'Menambahkan perangkat '.$perangkat->nama.'.',
            $perangkat,
            null,
            $perangkat->toArray()
        );

        if ($data['foto']) {
            ActivityLogger::log(
                $request,
                'upload_foto',
                'perangkat',
                'Mengupload foto perangkat '.$perangkat->nama.'.',
                $perangkat,
                null,
                ['foto' => $data['foto']]
            );
        }

        return redirect()->route('perangkat.index')->with('success', 'Data perangkat berhasil ditambahkan.');
    }

    /**
     * Show the form for editing perangkat wilayah.
     */
    public function edit(PerangkatWilayah $perangkat)
    {
        $perangkat->load('wilayah.kecamatan', 'jabatanPerangkat');

        return view('perangkat.edit', array_merge($this->formData(), compact('perangkat')));
    }

    /**
     * Update perangkat wilayah.
     */
    public function update(Request $request, PerangkatWilayah $perangkat)
    {
        $data = $this->validatePerangkat($request, $perangkat);
        $oldValues = $perangkat->toArray();
        $oldFoto = $perangkat->foto;

        if ($request->hasFile('foto')) {
            $this->deleteFoto($perangkat);
            $data['foto'] = $this->storeFoto($request);
        }

        $perangkat->update($data);
        $perangkat->refresh();

        ActivityLogger::log(
            $request,
            'update',
            'perangkat',
            'Memperbarui perangkat '.$perangkat->nama.'.',
            $perangkat,
            $oldValues,
            $perangkat->toArray()
        );

        if ($request->hasFile('foto')) {
            ActivityLogger::log(
                $request,
                'update_foto',
                'perangkat',
                'Mengganti foto perangkat '.$perangkat->nama.'.',
                $perangkat,
                ['foto' => $oldFoto],
                ['foto' => $perangkat->foto]
            );
        }

        return redirect()->route('perangkat.index')->with('success', 'Data perangkat berhasil diperbarui.');
    }

    /**
     * Remove perangkat wilayah.
     */
    public function destroy(Request $request, PerangkatWilayah $perangkat)
    {
        $oldValues = $perangkat->toArray();
        $description = 'Menghapus perangkat '.$perangkat->nama.'.';

        $this->deleteFoto($perangkat);
        $perangkat->delete();

        ActivityLogger::log(
            $request,
            'delete',
            'perangkat',
            $description,
            $perangkat,
            $oldValues,
            null
        );

        return redirect()->route('perangkat.index')->with('success', 'Data perangkat berhasil dihapus.');
    }

    private function validateIndexFilters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kecamatan_id' => ['nullable', 'integer', 'exists:kecamatans,id'],
            'jabatan_perangkat_id' => ['nullable', 'integer', 'exists:jabatan_perangkats,id'],
            'status' => ['nullable', Rule::in(self::STATUS_OPTIONS)],
            'masa_jabatan' => ['nullable', Rule::in(['aktif', 'hampir_berakhir', 'berakhir', 'belum_mulai', 'tanpa_batas'])],
        ]);
    }

    private function validatePerangkat(Request $request, ?PerangkatWilayah $ignoredPerangkat = null): array
    {
        $validator = Validator::make($request->all(), [
            'kecamatan_id' => ['required', 'exists:kecamatans,id'],
            'wilayah_id' => [
                'required',
                Rule::exists('wilayahs', 'id')
                    ->where('kecamatan_id', $request->input('kecamatan_id'))
                    ->where('jenis', 'desa'),
            ],
            'jabatan_perangkat_id' => ['required', 'exists:jabatan_perangkats,id'],
            'nama' => ['required', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'nomor_hp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'mulai_menjabat' => ['nullable', 'date'],
            'akhir_menjabat' => ['nullable', 'date', 'after_or_equal:mulai_menjabat'],
            'status' => ['required', Rule::in(self::STATUS_OPTIONS)],
        ]);

        $validator->after(function ($validator) use ($request, $ignoredPerangkat) {
            if ($validator->errors()->any()) {
                return;
            }

            // Wilayah & jenis 'desa' sudah divalidasi oleh Rule::exists di atas

            if ($request->input('status') !== 'aktif') {
                return;
            }

            $jabatan = JabatanPerangkat::find($request->input('jabatan_perangkat_id'));

            if (! $jabatan || ! in_array($jabatan->nama, self::JABATAN_UTAMA, true)) {
                return;
            }

            $perangkatAktif = PerangkatWilayah::where('wilayah_id', $request->input('wilayah_id'))
                ->where('jabatan_perangkat_id', $jabatan->id)
                ->where('status', 'aktif')
                ->when($ignoredPerangkat, function ($query) use ($ignoredPerangkat) {
                    $query->whereKeyNot($ignoredPerangkat->getKey());
                })
                ->first();

            if ($perangkatAktif) {
                $validator->errors()->add(
                    'jabatan_perangkat_id',
                    "Jabatan {$jabatan->nama} masih aktif di wilayah ini atas nama {$perangkatAktif->nama}. Ubah status perangkat lama menjadi nonaktif, selesai, atau pensiun terlebih dahulu sebelum menambah perangkat baru."
                );
            }
        });

        return $validator->validate();
    }

    private function formData(): array
    {
        $wilayahs = Wilayah::with('kecamatan')->where('jenis', 'desa')->orderBy('nama')->get();
        $jabatans = JabatanPerangkat::orderBy('level_urutan')->get();

        return [
            'kecamatans' => Kecamatan::orderBy('nama')->get(),
            'wilayahs' => $wilayahs,
            'wilayahOptions' => $wilayahs->map(fn (Wilayah $wilayah): array => [
                'id' => $wilayah->id,
                'kecamatan_id' => $wilayah->kecamatan_id,
                'nama' => $wilayah->nama,
                'jenis' => $wilayah->jenis,
            ])->values(),
            'jabatans' => $jabatans,
            'jabatanOptions' => $jabatans->map(fn (JabatanPerangkat $jabatan): array => [
                'id' => $jabatan->id,
                'nama' => $jabatan->nama,
                'scope' => $this->scopeJabatan($jabatan->nama),
            ])->values(),
        ];
    }

    private function scopeJabatan(string $namaJabatan): string
    {
        if (in_array($namaJabatan, self::JABATAN_DESA_ONLY, true)) {
            return 'desa';
        }

        return 'umum';
    }

    private function escapeLike(string $value): string
    {
        return addcslashes($value, '\%_');
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

    private function storeFoto(Request $request): ?string
    {
        if (! $request->hasFile('foto')) {
            return null;
        }

        return $request->file('foto')->store('perangkat', 'public');
    }

    private function deleteFoto(PerangkatWilayah $perangkat): void
    {
        if ($perangkat->foto) {
            Storage::disk('public')->delete($perangkat->foto);
        }
    }
}
