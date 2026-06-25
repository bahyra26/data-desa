<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Data Desa</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .muted { color: #6b7280; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 5px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>Data Desa</h1>
    <div class="muted">Tanggal cetak: {{ $tanggalCetak->format('d/m/Y H:i') }}</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kecamatan</th>
                <th>Desa</th>
                <th>Alamat Kantor</th>
                <th>Kepala Desa</th>
                <th>Jumlah Penduduk</th>
                <th>Luas Wilayah</th>
                <th>Tanggal Dibuat</th>
                <th>Diperbarui</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row['kecamatan'] }}</td>
                    <td>{{ $row['desa'] }}</td>
                    <td>{{ $row['alamat_kantor'] ?: '-' }}</td>
                    <td>{{ $row['kepala_desa'] ?: '-' }}</td>
                    <td>{{ $row['jumlah_penduduk'] ? number_format($row['jumlah_penduduk'], 0, ',', '.') : '-' }}</td>
                    <td>{{ $row['luas_wilayah'] ? number_format($row['luas_wilayah'], 2, ',', '.').' km2' : '-' }}</td>
                    <td>{{ $row['tanggal_dibuat'] ?: '-' }}</td>
                    <td>{{ $row['terakhir_diperbarui'] ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Belum ada data desa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
