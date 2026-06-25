@extends('layouts.app')

@section('title', 'Perangkat Desa')

@section('content')
    <div class="page-header">
        <h1>Perangkat Desa</h1>
        <div class="actions">
            <a href="{{ route('perangkat.export.excel', request()->query()) }}" class="button button-secondary">Export Excel</a>
            <a href="{{ route('perangkat.export.pdf', request()->query()) }}" class="button button-secondary">Export PDF</a>
            @if (auth()->user()->canManageData())
                <a href="{{ route('perangkat.create') }}" class="button button-primary">Tambah Perangkat</a>
            @endif
        </div>
    </div>

    <section class="card">
        <div class="filter-heading">
            <div>
                <h2>Temukan Perangkat</h2>
                <p class="muted">Cari berdasarkan nama, jabatan, kontak, desa, atau kecamatan.</p>
            </div>
            <span class="result-pill">{{ number_format($perangkats->total(), 0, ',', '.') }} perangkat</span>
        </div>
        <form action="{{ route('perangkat.index') }}" method="GET" class="filter-form">
            <div class="search-field">
                <span class="search-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                </span>
                <input
                    type="text"
                    name="search"
                    class="filter-input"
                    value="{{ $filters['search'] }}"
                    placeholder="Cari nama, jabatan, kontak, desa"
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
            <div class="filter-field">
                <label>Jabatan</label>
                <select name="jabatan_perangkat_id" class="filter-select js-auto-submit">
                    <option value="">Semua Jabatan</option>
                    @foreach ($jabatans as $jabatan)
                        <option value="{{ $jabatan->id }}" @selected($filters['jabatan_perangkat_id'] == $jabatan->id)>{{ $jabatan->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-field compact">
                <label>Status</label>
                <select name="status" class="filter-select js-auto-submit">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected($filters['status'] === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected($filters['status'] === 'nonaktif')>Nonaktif</option>
                    <option value="selesai" @selected($filters['status'] === 'selesai')>Selesai</option>
                    <option value="pensiun" @selected($filters['status'] === 'pensiun')>Pensiun</option>
                </select>
            </div>
            <div class="filter-field">
                <label>Masa Jabatan</label>
                <select name="masa_jabatan" class="filter-select js-auto-submit">
                    <option value="">Semua Masa Jabatan</option>
                    <option value="aktif" @selected($filters['masa_jabatan'] === 'aktif')>Aktif</option>
                    <option value="hampir_berakhir" @selected($filters['masa_jabatan'] === 'hampir_berakhir')>Hampir Berakhir</option>
                    <option value="berakhir" @selected($filters['masa_jabatan'] === 'berakhir')>Sudah Berakhir</option>
                    <option value="belum_mulai" @selected($filters['masa_jabatan'] === 'belum_mulai')>Belum Mulai</option>
                    <option value="tanpa_batas" @selected($filters['masa_jabatan'] === 'tanpa_batas')>Tanpa Tanggal Akhir</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="button button-primary">Cari</button>
                @if ($filters['search'] || $filters['kecamatan_id'] || $filters['jabatan_perangkat_id'] || $filters['status'] || $filters['masa_jabatan'])
                    <a href="{{ route('perangkat.index') }}" class="button button-secondary">Reset</a>
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
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Kecamatan</th>
                        <th>Desa</th>
                        <th>Status</th>
                        <th>Masa Jabatan</th>
                        <th>Countdown</th>
                        <th>Progress</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($perangkats as $perangkat)
                        <tr class="{{ $perangkat->status_masa_jabatan === 'berakhir' ? 'row-danger' : ($perangkat->status_masa_jabatan === 'hampir_berakhir' ? 'row-warning' : '') }}">
                            <td>{{ $perangkats->firstItem() + $loop->index }}</td>
                            <td>
                                @if ($perangkat->foto)
                                    <img src="{{ asset('storage/'.$perangkat->foto) }}" alt="Foto {{ $perangkat->nama }}" class="avatar">
                                @else
                                    <span class="avatar avatar-placeholder">{{ strtoupper(substr($perangkat->nama, 0, 1)) }}</span>
                                @endif
                            </td>
                            <td>{{ $perangkat->nama }}</td>
                            <td>{{ $perangkat->jabatanPerangkat->nama }}</td>
                            <td>{{ $perangkat->wilayah->kecamatan->nama }}</td>
                            <td>
                                {{ $perangkat->wilayah->nama }}
                                @if ($perangkat->wilayah->desas->first())
                                    <div>
                                        <a href="{{ route('desa.show', $perangkat->wilayah->desas->first()) }}" class="muted">Detail wilayah</a>
                                    </div>
                                @endif
                            </td>
                            <td>{{ ucfirst($perangkat->status) }}</td>
                            <td>
                                {{ $perangkat->mulai_menjabat ? $perangkat->mulai_menjabat->format('d/m/Y') : '-' }}
                                s/d
                                {{ $perangkat->akhir_menjabat ? $perangkat->akhir_menjabat->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                @php
                                    $badgeClass = match ($perangkat->status_masa_jabatan) {
                                        'berakhir' => 'badge-danger',
                                        'hampir_berakhir' => 'badge-warning',
                                        'aktif' => 'badge-success',
                                        default => 'badge-secondary',
                                    };
                                    $labelStatus = [
                                        'belum_mulai' => 'Belum Mulai',
                                        'aktif' => 'Aktif',
                                        'hampir_berakhir' => 'Hampir Berakhir',
                                        'berakhir' => 'Berakhir',
                                        'tanpa_batas' => 'Tanpa Batas',
                                    ][$perangkat->status_masa_jabatan];
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $labelStatus }}</span>
                                <div class="muted">{{ $perangkat->countdown_masa_jabatan }}</div>
                            </td>
                            <td>
                                @if (! is_null($perangkat->progress_masa_jabatan))
                                    <div class="progress">
                                        <div class="progress-bar {{ $perangkat->status_masa_jabatan === 'berakhir' ? 'danger' : ($perangkat->status_masa_jabatan === 'hampir_berakhir' ? 'warning' : '') }}" style="width: {{ $perangkat->progress_masa_jabatan }}%"></div>
                                    </div>
                                    <span class="muted">{{ $perangkat->progress_masa_jabatan }}%</span>
                                @else
                                    <span class="muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    @if ($perangkat->wilayah->desas->first())
                                        <a href="{{ route('desa.show', $perangkat->wilayah->desas->first()) }}" class="button button-secondary">Wilayah</a>
                                    @endif
                                    @if (auth()->user()->canManageData())
                                        <a href="{{ route('perangkat.edit', $perangkat) }}" class="button button-warning">Edit</a>
                                    @endif
                                    @if (auth()->user()->canDeleteData())
                                        <form action="{{ route('perangkat.destroy', $perangkat) }}" method="POST" onsubmit="return confirm('Hapus data perangkat ini?')">
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
                            <td colspan="11" class="empty">Belum ada data perangkat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($perangkats->hasPages())
            <div class="pagination-wrap">
                <div class="muted">
                    Menampilkan {{ $perangkats->firstItem() }}-{{ $perangkats->lastItem() }} dari {{ $perangkats->total() }} perangkat
                </div>
                <div class="actions">
                    @if ($perangkats->onFirstPage())
                        <span class="button button-secondary disabled">Sebelumnya</span>
                    @else
                        <a href="{{ $perangkats->previousPageUrl() }}" class="button button-secondary">Sebelumnya</a>
                    @endif

                    <span class="muted">Halaman {{ $perangkats->currentPage() }} dari {{ $perangkats->lastPage() }}</span>

                    @if ($perangkats->hasMorePages())
                        <a href="{{ $perangkats->nextPageUrl() }}" class="button button-secondary">Berikutnya</a>
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
