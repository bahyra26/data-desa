<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Audit Log</title>
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
    <h1>Audit Log</h1>
    <div class="muted">Tanggal cetak: {{ $tanggalCetak->format('d/m/Y H:i') }}</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu</th>
                <th>User</th>
                <th>Role</th>
                <th>Action</th>
                <th>Module</th>
                <th>Description</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row['waktu'] }}</td>
                    <td>{{ $row['user'] }}</td>
                    <td>{{ $row['role'] }}</td>
                    <td>{{ $row['action'] }}</td>
                    <td>{{ $row['module'] }}</td>
                    <td>{{ $row['description'] }}</td>
                    <td>{{ $row['ip_address'] ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Belum ada audit log.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
