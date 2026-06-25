<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Desa {{ $desa->wilayah->nama }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        h2 { font-size: 15px; margin: 18px 0 8px; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; }
        .profile td:first-child { width: 32%; font-weight: bold; background: #f9fafb; }
    </style>
</head>
<body>
    <h1>Detail Desa {{ $desa->wilayah->nama }}</h1>
    <div class="muted">Tanggal cetak: {{ $tanggalCetak->format('d/m/Y H:i') }}</div>

    <h2>Profil Wilayah</h2>
    <table class="profile">
        <tr>
            <td>Kecamatan</td>
            <td>{{ $desa->wilayah->kecamatan->nama }}</td>
        </tr>
        <tr>
            <td>Desa</td>
            <td>{{ $desa->wilayah->nama }}</td>
        </tr>
        <tr>
            <td>Kode Kemendagri Kecamatan</td>
            <td>{{ $desa->wilayah->kecamatan->kode_kemendagri }}</td>
        </tr>
        <tr>
            <td>Kode Pos Kecamatan</td>
            <td>{{ $desa->wilayah->kecamatan->kode_pos }}</td>
        </tr>
        <tr>
            <td>Jumlah Penduduk</td>
            <td>{{ $desa->jumlah_penduduk ? number_format($desa->jumlah_penduduk, 0, ',', '.') : '-' }}</td>
        </tr>
        <tr>
            <td>Luas Wilayah</td>
            <td>{{ $desa->luas_wilayah ? number_format($desa->luas_wilayah, 2, ',', '.').' km2' : '-' }}</td>
        </tr>
    </table>

    <h2>Data Kantor</h2>
    <table class="profile">
        <tr>
            <td>Alamat Kantor</td>
            <td>{{ $desa->alamat_kantor ?: '-' }}</td>
        </tr>
        <tr>
            <td>Kepala Desa</td>
            <td>{{ $desa->kepala_desa ?: '-' }}</td>
        </tr>
    </table>

    <h2>Perangkat Aktif</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Masa Jabatan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($perangkatAktif as $perangkat)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $perangkat->nama }}</td>
                    <td>{{ $perangkat->jabatanPerangkat->nama }}</td>
                    <td>
                        {{ $perangkat->mulai_menjabat ? $perangkat->mulai_menjabat->format('d/m/Y') : '-' }}
                        s/d
                        {{ $perangkat->akhir_menjabat ? $perangkat->akhir_menjabat->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ ucfirst($perangkat->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada perangkat aktif.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Riwayat Perangkat</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Masa Jabatan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($riwayatPerangkat as $perangkat)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $perangkat->nama }}</td>
                    <td>{{ $perangkat->jabatanPerangkat->nama }}</td>
                    <td>
                        {{ $perangkat->mulai_menjabat ? $perangkat->mulai_menjabat->format('d/m/Y') : '-' }}
                        s/d
                        {{ $perangkat->akhir_menjabat ? $perangkat->akhir_menjabat->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ ucfirst($perangkat->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada riwayat perangkat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
