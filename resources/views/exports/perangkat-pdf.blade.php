<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Data Perangkat</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .muted { color: #6b7280; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 4px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>Data Perangkat Desa</h1>
    <div class="muted">Tanggal cetak: {{ $tanggalCetak->format('d/m/Y H:i') }}</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Kecamatan</th>
                <th>Desa</th>
                <th>JK</th>
                <th>Nomor HP</th>
                <th>Email</th>
                <th>Mulai</th>
                <th>Akhir</th>
                <th>Status</th>
                <th>Countdown</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['jabatan'] }}</td>
                    <td>{{ $row['kecamatan'] }}</td>
                    <td>{{ $row['desa'] }}</td>
                    <td>{{ $row['jenis_kelamin'] ?: '-' }}</td>
                    <td>{{ $row['nomor_hp'] ?: '-' }}</td>
                    <td>{{ $row['email'] ?: '-' }}</td>
                    <td>{{ $row['mulai_menjabat'] ?: '-' }}</td>
                    <td>{{ $row['akhir_menjabat'] ?: '-' }}</td>
                    <td>{{ $row['status'] }}</td>
                    <td>{{ $row['countdown_masa_jabatan'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12">Belum ada data perangkat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
