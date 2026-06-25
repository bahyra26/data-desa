@extends('layouts.app')

@section('title', 'Data Wilayah')

@section('content')
    <div class="page-header">
        <h1>Data Wilayah</h1>
        <div class="actions">
            <a href="{{ route('desa.export.excel', request()->query()) }}" class="button button-secondary">Export Excel</a>
            <a href="{{ route('desa.export.list-pdf', request()->query()) }}" class="button button-secondary">Export PDF</a>
            @if (auth()->user()->canManageData())
                <a href="{{ route('desa.create') }}" class="button button-primary">Tambah Data</a>
            @endif
        </div>
    </div>

    <section class="card">
        <div class="filter-heading">
            <div>
                <h2>Temukan Wilayah</h2>
                <p class="muted">Cari berdasarkan desa, kecamatan, kepala desa, atau alamat kantor.</p>
            </div>
            <span class="result-pill">{{ number_format($desas->total(), 0, ',', '.') }} data</span>
        </div>
        <form action="{{ route('desa.index') }}" method="GET" class="filter-form">
            <div class="search-field">
                <span class="search-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                </span>
                <input
                    type="text"
                    name="search"
                    class="filter-input"
                    value="{{ $filters['search'] }}"
                    placeholder="Cari desa, kecamatan, kepala desa, alamat"
                >
            </div>
            <div class="filter-field">
                <label for="kecamatan_id">Kecamatan</label>
                <select name="kecamatan_id" id="kecamatan_id" class="filter-select js-auto-submit">
                    <option value="">Semua Kecamatan</option>
                    @foreach ($kecamatans as $kecamatan)
                        <option value="{{ $kecamatan->id }}" @selected($filters['kecamatan_id'] == $kecamatan->id)>{{ $kecamatan->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="button button-primary">Cari</button>
                @if ($filters['search'] || $filters['kecamatan_id'])
                    <a href="{{ route('desa.index') }}" class="button button-secondary">Reset</a>
                @endif
            </div>
        </form>
    </section>

    <section class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kecamatan</th>
                        <th>Desa</th>
                        <th>Alamat Kantor</th>
                        <th>Lokasi</th>
                        <th>Kepala Desa</th>
                        <th>Jumlah Penduduk</th>
                        <th>Luas Wilayah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($desas as $desa)
                        <tr>
                            <td>{{ $desas->firstItem() + $loop->index }}</td>
                            <td>{{ $desa->wilayah->kecamatan->nama }}</td>
                            <td>{{ $desa->wilayah->nama }}</td>
                            <td class="address-cell">
                                {{ $desa->alamat_kantor ?: '-' }}
                            </td>
                            <td>
                                @if ($desa->alamat_kantor)
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($desa->alamat_kantor) }}" target="_blank" rel="noopener" class="button button-secondary">Lihat Lokasi</a>
                                @else
                                    <span class="muted">Belum diisi</span>
                                @endif
                            </td>
                            <td>{{ $desa->kepala_desa ?: '-' }}</td>
                            <td>{{ $desa->jumlah_penduduk ? number_format($desa->jumlah_penduduk, 0, ',', '.') : '-' }}</td>
                            <td>{{ $desa->luas_wilayah ? number_format($desa->luas_wilayah, 2, ',', '.') . ' km2' : '-' }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('desa.show', $desa) }}" class="button button-primary">Detail</a>
                                    @if (auth()->user()->canManageData())
                                        <a href="{{ route('desa.edit', $desa) }}" class="button button-warning">Edit</a>
                                    @endif
                                    @if (auth()->user()->canDeleteData())
                                        <form action="{{ route('desa.destroy', $desa) }}" method="POST" onsubmit="return confirm('Hapus data desa ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button button-danger">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty">Belum ada data desa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($desas->hasPages())
            <div class="pagination-wrap">
                <div class="muted">
                    Menampilkan {{ $desas->firstItem() }}-{{ $desas->lastItem() }} dari {{ $desas->total() }} data desa
                </div>
                <div class="actions">
                    @if ($desas->onFirstPage())
                        <span class="button button-secondary disabled">Sebelumnya</span>
                    @else
                        <a href="{{ $desas->previousPageUrl() }}" class="button button-secondary">Sebelumnya</a>
                    @endif

                    <span class="muted">Halaman {{ $desas->currentPage() }} dari {{ $desas->lastPage() }}</span>

                    @if ($desas->hasMorePages())
                        <a href="{{ $desas->nextPageUrl() }}" class="button button-secondary">Berikutnya</a>
                    @else
                        <span class="button button-secondary disabled">Berikutnya</span>
                    @endif
                </div>
            </div>
        @endif
    </section>
@endsection

@push('scripts')
    <script>
        const filterForm = document.querySelector('.filter-form');
        const kecamatanSelect = document.getElementById('kecamatan_id');

        kecamatanSelect.addEventListener('change', () => {
            filterForm.submit();
        });

        document.querySelectorAll('.js-auto-submit').forEach((select) => {
            if (select !== kecamatanSelect) {
                select.addEventListener('change', () => filterForm.submit());
            }
        });
    </script>
@endpush
