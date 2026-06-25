@extends('layouts.app')

@section('title', 'Backup Database')

@section('content')
    <div class="page-header">
        <h1>Backup Database</h1>
        <form action="{{ route('backups.store') }}" method="POST">
            @csrf
            <button type="submit" class="button button-primary">Buat Backup Baru</button>
        </form>
    </div>

    <section class="card">
        <div class="alert alert-error">
            Restore backup akan mengganti data aktif pada tabel kecamatan, wilayah, desa, jabatan perangkat, perangkat, users, dan activity logs. Pastikan file backup yang dipilih benar.
        </div>

        <div class="filter-heading">
            <div>
                <h2>File Backup</h2>
                <p class="muted">Cari nama file backup atau urutkan berdasarkan waktu, nama, dan ukuran.</p>
            </div>
            <span class="result-pill">{{ number_format($backups->count(), 0, ',', '.') }} file</span>
        </div>
        <form action="{{ route('backups.index') }}" method="GET" class="filter-form">
            <div class="search-field">
                <span class="search-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                </span>
                <input
                    type="text"
                    name="search"
                    class="filter-input"
                    value="{{ $filters['search'] }}"
                    placeholder="Cari nama file backup"
                >
            </div>
            <div class="filter-field compact">
                <label>Urutkan</label>
                <select name="sort" class="filter-select js-auto-submit">
                    <option value="latest" @selected($filters['sort'] === 'latest')>Terbaru</option>
                    <option value="oldest" @selected($filters['sort'] === 'oldest')>Terlama</option>
                    <option value="name_asc" @selected($filters['sort'] === 'name_asc')>Nama A-Z</option>
                    <option value="size_desc" @selected($filters['sort'] === 'size_desc')>Ukuran terbesar</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="button button-primary">Cari</button>
                @if ($filters['search'] || $filters['sort'] !== 'latest')
                    <a href="{{ route('backups.index') }}" class="button button-secondary">Reset</a>
                @endif
            </div>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama File</th>
                        <th>Ukuran</th>
                        <th>Terakhir Diubah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($backups as $backup)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $backup['name'] }}</strong></td>
                            <td>{{ number_format($backup['size'] / 1024, 2, ',', '.') }} KB</td>
                            <td>{{ $backup['modified_at'] }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('backups.download', $backup['name']) }}" class="button button-secondary">Download</a>
                                    <form action="{{ route('backups.restore', $backup['name']) }}" method="POST" onsubmit="return confirm('PERINGATAN: Restore backup akan mengganti data aktif. Lanjutkan restore file {{ $backup['name'] }}?')">
                                        @csrf
                                        <button type="submit" class="button button-warning">Restore</button>
                                    </form>
                                    <form action="{{ route('backups.destroy', $backup['name']) }}" method="POST" onsubmit="return confirm('Hapus file backup ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button button-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">Tidak ada file backup yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.js-auto-submit').forEach((select) => {
            select.addEventListener('change', () => select.form.submit());
        });
    </script>
@endpush
