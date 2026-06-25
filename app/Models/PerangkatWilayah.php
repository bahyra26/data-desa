<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerangkatWilayah extends Model
{
    protected $fillable = [
        'wilayah_id',
        'jabatan_perangkat_id',
        'nama',
        'foto',
        'jenis_kelamin',
        'nomor_hp',
        'email',
        'mulai_menjabat',
        'akhir_menjabat',
        'status',
    ];

    protected $casts = [
        'mulai_menjabat' => 'date',
        'akhir_menjabat' => 'date',
    ];

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function jabatanPerangkat(): BelongsTo
    {
        return $this->belongsTo(JabatanPerangkat::class);
    }

    public function getStatusMasaJabatanAttribute(): string
    {
        $hariIni = Carbon::today();

        if (! $this->akhir_menjabat) {
            return 'tanpa_batas';
        }

        if ($this->mulai_menjabat && $hariIni->lt($this->mulai_menjabat)) {
            return 'belum_mulai';
        }

        if ($hariIni->gt($this->akhir_menjabat)) {
            return 'berakhir';
        }

        if ($hariIni->diffInDays($this->akhir_menjabat) <= 90) {
            return 'hampir_berakhir';
        }

        return 'aktif';
    }

    public function getJumlahHariTersisaAttribute(): ?int
    {
        if (! $this->akhir_menjabat) {
            return null;
        }

        return (int) Carbon::today()->diffInDays($this->akhir_menjabat, false);
    }

    public function getCountdownMasaJabatanAttribute(): string
    {
        $hariIni = Carbon::today();

        if (! $this->akhir_menjabat) {
            return 'Tanggal akhir belum diisi';
        }

        if ($this->mulai_menjabat && $hariIni->lt($this->mulai_menjabat)) {
            return 'Mulai dalam '.(int) $hariIni->diffInDays($this->mulai_menjabat).' hari';
        }

        if ($hariIni->gt($this->akhir_menjabat)) {
            return 'Sudah berakhir '.(int) $this->akhir_menjabat->diffInDays($hariIni).' hari lalu';
        }

        $sisaHari = (int) $hariIni->diffInDays($this->akhir_menjabat);

        if ($sisaHari <= 30) {
            return 'Berakhir dalam '.$sisaHari.' hari';
        }

        if ($sisaHari <= 90) {
            return 'Sisa '.$sisaHari.' hari';
        }

        $tahun = (int) floor($hariIni->diffInYears($this->akhir_menjabat));
        $setelahTahun = $hariIni->copy()->addYears($tahun);
        $bulan = (int) floor($setelahTahun->diffInMonths($this->akhir_menjabat));

        $bagian = [];

        if ($tahun > 0) {
            $bagian[] = $tahun.' tahun';
        }

        if ($bulan > 0) {
            $bagian[] = $bulan.' bulan';
        }

        return 'Sisa '.($bagian ? implode(' ', $bagian) : $sisaHari.' hari');
    }

    public function getProgressMasaJabatanAttribute(): ?int
    {
        if (! $this->mulai_menjabat || ! $this->akhir_menjabat) {
            return null;
        }

        if ($this->akhir_menjabat->lte($this->mulai_menjabat)) {
            return null;
        }

        $hariIni = Carbon::today();
        $totalHari = $this->mulai_menjabat->diffInDays($this->akhir_menjabat);
        $hariBerjalan = $this->mulai_menjabat->diffInDays($hariIni, false);

        if ($hariBerjalan <= 0) {
            return 0;
        }

        if ($hariIni->gte($this->akhir_menjabat)) {
            return 100;
        }

        return (int) round(min(100, max(0, ($hariBerjalan / $totalHari) * 100)));
    }
}
