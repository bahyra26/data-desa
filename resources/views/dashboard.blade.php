@extends('layouts.app')

@section('title', 'Dashboard Data Desa')

@section('content')
    <div class="page-header">
        <h1>Dashboard Ringkasan</h1>
    </div>

    {{-- ============ DATA MASTER WILAYAH ============ --}}
    <section class="section-dashboard" aria-label="Data Master Wilayah">
        <div class="section-dashboard-header">
            <h2>Data Master Wilayah</h2>
            <span class="section-badge badge-master">Data referensi tetap</span>
        </div>

        <div class="stats-grid stats-2col">
            <div class="card-master">
                <div class="card-master-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" width="26" height="26"><path d="M12 2 3 7v15h18V7l-9-5Zm-4 18v-7h8v7H8Z"/></svg>
                </div>
                <div class="card-master-body">
                    <div class="card-master-label">Total Kecamatan</div>
                    <div class="card-master-value">{{ number_format($stats['total_kecamatan'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="card-master">
                <div class="card-master-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" width="26" height="26"><path d="M3 3h8v8H3V3Zm10 0h8v8h-8V3ZM3 13h8v8H3v-8Zm10 0h8v8h-8v-8Z"/></svg>
                </div>
                <div class="card-master-body">
                    <div class="card-master-label">Total Desa</div>
                    <div class="card-master-value">{{ number_format($stats['total_desa'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ DATA OPERASIONAL & MONITORING ============ --}}
    <section class="section-dashboard" aria-label="Data Operasional dan Monitoring">
        <div class="section-dashboard-header">
            <h2>Data Operasional &amp; Monitoring</h2>
            <span class="section-badge badge-dynamic">Berubah sesuai aktivitas input</span>
        </div>

        {{-- Operational stats --}}
        <div class="stats-grid stats-4col">
            <div class="card-dynamic">
                <div class="card-dynamic-label">
                    Data Desa Diinput
                    <span class="status-dot status-dot-blue" title="Terupdate dari input data"></span>
                </div>
                <div class="card-dynamic-value">{{ number_format($stats['total_data_desa'], 0, ',', '.') }}</div>
                <div class="card-dynamic-footer">Total data desa yang tercatat di sistem</div>
            </div>
            <div class="card-dynamic">
                <div class="card-dynamic-label">
                    Perangkat Aktif
                    <span class="status-dot status-dot-green" title="Data real-time"></span>
                </div>
                <div class="card-dynamic-value">{{ number_format($stats['total_perangkat_aktif'], 0, ',', '.') }}</div>
                <div class="card-dynamic-footer">Perangkat desa yang sedang aktif bertugas</div>
            </div>
            <div class="card-dynamic">
                <div class="card-dynamic-label">
                    Hampir Berakhir
                    <span class="status-dot status-dot-yellow" title="Perlu perhatian"></span>
                </div>
                <div class="card-dynamic-value">{{ number_format($stats['total_perangkat_hampir_berakhir'], 0, ',', '.') }}</div>
                <div class="card-dynamic-footer">Masa jabatan akan berakhir dalam 90 hari</div>
            </div>
            <div class="card-dynamic">
                <div class="card-dynamic-label">
                    Sudah Berakhir
                    <span class="status-dot status-dot-red" title="Sudah lewat"></span>
                </div>
                <div class="card-dynamic-value">{{ number_format($stats['total_perangkat_berakhir'], 0, ',', '.') }}</div>
                <div class="card-dynamic-footer">Masa jabatan sudah berakhir</div>
            </div>
        </div>

        {{-- Progress pengisian data desa --}}
        @php
            $totalDesa = max($stats['total_desa'], 1);
            $persenDataDesa = min(round(($stats['total_data_desa'] / $totalDesa) * 100), 100);
        @endphp
        <div class="progress-section">
            <div class="progress-section-header">
                <span class="progress-section-label">Progress Pengisian Data Desa</span>
                <span class="progress-section-value">{{ $stats['total_data_desa'] }} / {{ $stats['total_desa'] }} desa ({{ $persenDataDesa }}%)</span>
            </div>
            <div class="progress-bar-track">
                <div class="progress-bar-fill {{ $persenDataDesa === 100 ? 'fill-done' : ($persenDataDesa >= 60 ? 'fill-good' : ($persenDataDesa >= 30 ? 'fill-medium' : 'fill-low')) }}" style="width: {{ $persenDataDesa }}%"></div>
            </div>
        </div>

        {{-- 5 Data Desa Terbaru --}}
        <div class="section-subsection">
            <h3 class="subsection-heading">5 Data Desa Terbaru</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kecamatan</th>
                            <th>Desa</th>
                            <th>Kepala Desa</th>
                            <th>Diinput</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($desasTerbaru as $desa)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $desa->wilayah->kecamatan->nama }}</td>
                                <td><strong>{{ $desa->wilayah->nama }}</strong></td>
                                <td>{{ $desa->kepala_desa ?: '-' }}</td>
                                <td>{{ $desa->created_at ? $desa->created_at->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <span class="empty-state-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="36" height="36"><path d="M12 5v14M5 12h14"/></svg>
                                    </span>
                                    <span class="empty-state-text">Belum ada data desa yang diinput.</span>
                                    <span class="empty-state-hint">Gunakan menu <a href="{{ route('desa.create') }}">Data Wilayah</a> untuk menambah data desa.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pemberitahuan Masa Jabatan --}}
        <div class="section-subsection">
            <h3 class="subsection-heading">Pemberitahuan Masa Jabatan</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Kecamatan</th>
                            <th>Desa</th>
                            <th>Akhir Menjabat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($peringatanMasaJabatan as $perangkat)
                            <tr class="{{ $perangkat->status_masa_jabatan === 'berakhir' ? 'row-danger' : 'row-warning' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $perangkat->nama }}</strong></td>
                                <td>{{ $perangkat->jabatanPerangkat->nama }}</td>
                                <td>{{ $perangkat->wilayah->kecamatan->nama }}</td>
                                <td>{{ $perangkat->wilayah->nama }}</td>
                                <td>{{ $perangkat->akhir_menjabat->format('d/m/Y') }}</td>
                                <td>
                                    @if ($perangkat->status_masa_jabatan === 'berakhir')
                                        <span class="badge badge-danger">Sudah lewat</span>
                                    @else
                                        <span class="badge badge-warning">{{ $perangkat->countdown_masa_jabatan }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if (auth()->user()->canManageData())
                                        <a href="{{ route('perangkat.edit', $perangkat) }}" class="button button-warning">Edit</a>
                                    @else
                                        <span class="muted">Lihat saja</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <span class="empty-state-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="36" height="36"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </span>
                                    <span class="empty-state-text">Belum ada perangkat aktif yang masa jabatannya akan berakhir dalam 90 hari.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 5 Perangkat Terdekat Akhir Masa Jabatan --}}
        <div class="section-subsection">
            <h3 class="subsection-heading">5 Perangkat Terdekat Akhir Masa Jabatan</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Wilayah</th>
                            <th>Akhir Menjabat</th>
                            <th>Countdown</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perangkatTerdekatAkhirMasaJabatan as $perangkat)
                            <tr class="{{ $perangkat->status_masa_jabatan === 'hampir_berakhir' ? 'row-warning' : '' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $perangkat->nama }}</strong></td>
                                <td>{{ $perangkat->jabatanPerangkat->nama }}</td>
                                <td>{{ $perangkat->wilayah->nama }}, {{ $perangkat->wilayah->kecamatan->nama }}</td>
                                <td>{{ $perangkat->akhir_menjabat->format('d/m/Y') }}</td>
                                <td><span class="countdown-badge">{{ $perangkat->countdown_masa_jabatan }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <span class="empty-state-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="36" height="36"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </span>
                                    <span class="empty-state-text">Belum ada perangkat aktif dengan tanggal akhir menjabat.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- ============ REKAPITULASI WILAYAH ============ --}}
    <section class="section-dashboard rekap-section" aria-label="Rekapitulasi Wilayah">
        <div class="section-dashboard-header">
            <h2>Rekapitulasi Wilayah</h2>
            <span class="section-badge badge-master">Data struktural</span>
        </div>

        {{-- ── Search Cari Desa ── --}}
        <div class="rekap-toolbar">
            <div class="rekap-search-wrap">
                <label for="rekap-wilayah-search" class="rekap-search-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    Cari Desa
                </label>
                <form id="rekap-form" action="{{ route('dashboard') }}" method="GET" autocomplete="off">
                    <div class="rekap-combobox" data-rekap-combobox>
                        <input
                            id="rekap-wilayah-search"
                            type="search"
                            class="rekap-select"
                            placeholder="Ketik nama desa atau kecamatan"
                            aria-expanded="false"
                            aria-controls="rekap-wilayah-options"
                            aria-autocomplete="list"
                        >
                        <input type="hidden" id="rekap-wilayah-id" name="wilayah_id" value="{{ $selectedDesaDetail['wilayah_id'] ?? '' }}">
                        <div id="rekap-wilayah-options" class="rekap-options" role="listbox"></div>
                    </div>
                    @if ($selectedDesaDetail)
                        <a href="{{ route('dashboard') }}" class="rekap-clear-btn" title="Hapus filter">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- ── Detail Card Desa Terpilih ── --}}
        @if ($selectedDesaDetail)
            <div class="rekap-detail-card" id="rekapDetailCard">
                <div class="rekap-detail-header">
                    <div class="rekap-detail-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <div>
                            <strong>{{ $selectedDesaDetail['nama'] }}</strong>
                            <span class="rekap-detail-kec">{{ $selectedDesaDetail['kecamatan_nama'] }}</span>
                        </div>
                    </div>
                    <div class="rekap-detail-actions">
                        @if (isset($selectedDesaDetail['desa_id']))
                            <a href="{{ route('desa.show', $selectedDesaDetail['desa_id']) }}" class="button button-primary">Detail Lengkap</a>
                        @endif
                    </div>
                </div>

                <div class="rekap-detail-grid">
                    <div class="rekap-detail-item">
                        <span class="rekap-detail-label">Kepala Desa</span>
                        <span class="rekap-detail-value">{{ $selectedDesaDetail['kepala_desa'] ?? '-' }}</span>
                    </div>
                    <div class="rekap-detail-item">
                        <span class="rekap-detail-label">Jenis Wilayah</span>
                        <span class="rekap-detail-value">{{ ucfirst($selectedDesaDetail['jenis']) }}</span>
                    </div>
                    <div class="rekap-detail-item">
                        <span class="rekap-detail-label">Kode Kemendagri</span>
                        <span class="rekap-detail-value">{{ $selectedDesaDetail['kode_kemendagri'] ?? '-' }}</span>
                    </div>
                    <div class="rekap-detail-item">
                        <span class="rekap-detail-label">Kode Pos</span>
                        <span class="rekap-detail-value">{{ $selectedDesaDetail['kode_pos'] ?? '-' }}</span>
                    </div>
                    @if (isset($selectedDesaDetail['jumlah_penduduk']))
                        <div class="rekap-detail-item">
                            <span class="rekap-detail-label">Jumlah Penduduk</span>
                            <span class="rekap-detail-value">{{ number_format($selectedDesaDetail['jumlah_penduduk'], 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if (isset($selectedDesaDetail['luas_wilayah']))
                        <div class="rekap-detail-item">
                            <span class="rekap-detail-label">Luas Wilayah</span>
                            <span class="rekap-detail-value">{{ number_format($selectedDesaDetail['luas_wilayah'], 2, ',', '.') }} km²</span>
                        </div>
                    @endif
                    @if (isset($selectedDesaDetail['alamat_kantor']))
                        <div class="rekap-detail-item rekap-detail-item-full">
                            <span class="rekap-detail-label">Alamat Kantor</span>
                            <span class="rekap-detail-value">{{ $selectedDesaDetail['alamat_kantor'] ?: '-' }}</span>
                        </div>
                    @endif
                </div>

                {{-- Perangkat desa --}}
                @if ($selectedDesaPerangkat && $selectedDesaPerangkat->isNotEmpty())
                    <div class="rekap-perangkat-section">
                        <h4 class="rekap-perangkat-title">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0Zm-12 9c.7-3.2 3.6-5 8-5s7.3 1.8 8 5H4Z"/></svg>
                            Perangkat Desa
                            <span class="rekap-perangkat-count">{{ $selectedDesaPerangkat->count() }}</span>
                        </h4>
                        <div class="table-wrap">
                            <table class="rekap-perangkat-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Status</th>
                                        <th>Masa Jabatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($selectedDesaPerangkat as $p)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $p->nama }}</strong></td>
                                            <td>{{ $p->jabatanPerangkat->nama }}</td>
                                            <td>
                                                @php $statusDot = match($p->status) { 'aktif' => 'green', 'nonaktif' => 'yellow', default => 'gray' }; @endphp
                                                <span class="rekap-status-badge status-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
                                            </td>
                                            <td class="muted">
                                                {{ $p->mulai_menjabat ? $p->mulai_menjabat->format('d/m/Y') : '-' }}
                                                &mdash;
                                                {{ $p->akhir_menjabat ? $p->akhir_menjabat->format('d/m/Y') : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- ── Tabel Kecamatan dengan daftar desa ── --}}
        <div class="card rekap-table-card">
            <div class="rekap-table-header">
                <span class="rekap-table-title">Daftar Kecamatan &amp; Desa</span>
                <span class="rekap-table-count">{{ $wilayahPerKecamatan->count() }} kecamatan</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kecamatan</th>
                            <th>Jumlah Desa</th>
                            <th>Data Terisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wilayahPerKecamatan as $kecamatan)
                            @php
                                $desaWilayahs = $kecamatan->wilayahs->where('jenis', 'desa');
                                $desaCount = $desaWilayahs->count();
                                $terisiCount = $desaWilayahs->filter(fn($w) => ($w->desas_count ?? 0) > 0)->count();
                            @endphp
                            <tr class="kecamatan-row js-kecamatan-toggle" data-target="wilayah-kecamatan-{{ $kecamatan->id }}" tabindex="0" role="button" aria-expanded="false">
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="toggle-indicator" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M8 5l8 7-8 7z"/></svg>
                                    </span>
                                    <strong>{{ $kecamatan->nama }}</strong>
                                </td>
                                <td>
                                    <span class="rekap-badge-desa">{{ $desaCount }} desa</span>
                                </td>
                                <td>
                                    @if ($desaCount > 0)
                                        @php $pct = min(round(($terisiCount / max($desaCount, 1)) * 100), 100); @endphp
                                        <div class="rekap-mini-progress" title="{{ $terisiCount }} dari {{ $desaCount }} desa sudah diinput">
                                            <div class="rekap-mini-bar {{ $pct === 100 ? 'bar-done' : ($pct > 0 ? 'bar-partial' : 'bar-empty') }}" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="rekap-mini-label {{ $pct === 100 ? 'text-green' : ($pct > 0 ? 'text-yellow' : 'text-muted') }}">
                                            {{ $terisiCount }}/{{ $desaCount }}
                                        </span>
                                    @else
                                        <span class="muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr id="wilayah-kecamatan-{{ $kecamatan->id }}" class="wilayah-detail-row">
                                <td colspan="4">
                                    <div class="wilayah-anim-wrap">
                                        <div class="wilayah-detail-content">
                                            @php $desaWilayahs = $kecamatan->wilayahs->where('jenis', 'desa'); @endphp
                                            @if ($desaWilayahs->isNotEmpty())
                                                <div class="wilayah-list">
                                                    @foreach ($desaWilayahs as $wilayah)
                                                        @php
                                                            $hasData = ($wilayah->desas_count ?? 0) > 0;
                                                            $isActive = $selectedDesaDetail && $selectedDesaDetail['wilayah_id'] == $wilayah->id;
                                                        @endphp
                                                        <a href="{{ route('dashboard', ['wilayah_id' => $wilayah->id]) }}"
                                                           class="wilayah-chip {{ $hasData ? 'has-data' : 'no-data' }} {{ $isActive ? 'is-active' : '' }}"
                                                           title="{{ $wilayah->nama }} — {{ $hasData ? 'Data sudah diinput' : 'Data belum diinput' }}">
                                                            <span class="wilayah-chip-name">{{ $wilayah->nama }}</span>
                                                            @if ($hasData)
                                                                <span class="wilayah-chip-check" title="Data sudah diinput">
                                                                    <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                                                </span>
                                                            @else
                                                                <span class="wilayah-chip-dot" title="Data belum diinput"></span>
                                                            @endif
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="muted">Belum ada desa untuk kecamatan ini.</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection


@push('scripts')
    <script>
        (function() {
            // ── Data ──
            const wilayahs = @json($wilayahOptions);
            const selectedWilayahId = '{{ $selectedDesaDetail['wilayah_id'] ?? '' }}';

            // ── Searchable desa picker ──
            const inputEl = document.getElementById('rekap-wilayah-search');
            const hiddenEl = document.getElementById('rekap-wilayah-id');
            const optionsEl = document.getElementById('rekap-wilayah-options');
            const formEl = document.getElementById('rekap-form');

            if (inputEl && hiddenEl && optionsEl && formEl) {
                const selectedWilayah = wilayahs.find((w) => String(w.id) === String(selectedWilayahId));
                let activeIndex = -1;

                if (selectedWilayah) {
                    inputEl.value = `${selectedWilayah.nama} - ${selectedWilayah.kecamatan_nama}`;
                }

                function filteredOptions() {
                    const term = inputEl.value.trim().toLowerCase();

                    return wilayahs
                        .filter((w) => !term || `${w.nama} ${w.kecamatan_nama}`.toLowerCase().includes(term))
                        .slice(0, 12);
                }

                function renderOptions() {
                    const options = filteredOptions();
                    optionsEl.innerHTML = '';
                    activeIndex = -1;

                    if (options.length === 0) {
                        const empty = document.createElement('div');
                        empty.className = 'rekap-option-empty';
                        empty.textContent = 'Desa tidak ditemukan.';
                        optionsEl.appendChild(empty);
                    }

                    options.forEach((w, index) => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'rekap-option';
                        button.setAttribute('role', 'option');
                        button.dataset.index = String(index);
                        const textWrap = document.createElement('div');
                        const nameEl = document.createElement('strong');
                        const kecamatanEl = document.createElement('span');
                        nameEl.textContent = w.nama;
                        kecamatanEl.textContent = w.kecamatan_nama;
                        textWrap.append(nameEl, kecamatanEl);
                        button.appendChild(textWrap);
                        button.addEventListener('click', () => {
                            hiddenEl.value = w.id;
                            inputEl.value = `${w.nama} - ${w.kecamatan_nama}`;
                            closeOptions();
                            formEl.submit();
                        });
                        optionsEl.appendChild(button);
                    });

                    openOptions();
                }

                function openOptions() {
                    optionsEl.classList.add('open');
                    inputEl.setAttribute('aria-expanded', 'true');
                }

                function closeOptions() {
                    optionsEl.classList.remove('open');
                    inputEl.setAttribute('aria-expanded', 'false');
                }

                function setActive(index) {
                    const optionButtons = optionsEl.querySelectorAll('.rekap-option');
                    optionButtons.forEach((button) => button.classList.remove('is-active'));
                    activeIndex = Math.max(0, Math.min(index, optionButtons.length - 1));
                    if (optionButtons[activeIndex]) {
                        optionButtons[activeIndex].classList.add('is-active');
                        optionButtons[activeIndex].scrollIntoView({ block: 'nearest' });
                    }
                }

                inputEl.addEventListener('focus', renderOptions);
                inputEl.addEventListener('input', () => {
                    hiddenEl.value = '';
                    renderOptions();
                });
                inputEl.addEventListener('keydown', (event) => {
                    const optionButtons = optionsEl.querySelectorAll('.rekap-option');
                    if (event.key === 'ArrowDown') {
                        event.preventDefault();
                        if (! optionsEl.classList.contains('open')) {
                            renderOptions();
                        }
                        setActive(activeIndex + 1);
                    }
                    if (event.key === 'ArrowUp') {
                        event.preventDefault();
                        setActive(activeIndex <= 0 ? optionButtons.length - 1 : activeIndex - 1);
                    }
                    if (event.key === 'Enter' && activeIndex >= 0 && optionButtons[activeIndex]) {
                        event.preventDefault();
                        optionButtons[activeIndex].click();
                    }
                    if (event.key === 'Escape') {
                        closeOptions();
                    }
                });
                document.addEventListener('click', (event) => {
                    const wrapper = inputEl.closest('[data-rekap-combobox]');
                    if (wrapper && ! wrapper.contains(event.target)) {
                        closeOptions();
                    }
                });
            }

            // ── Kecamatan toggle with smooth slide animation ──
            document.querySelectorAll('.kecamatan-row').forEach((row) => {
                const detailRow = document.getElementById(row.dataset.target);
                if (!detailRow) return;
                const indicator = row.querySelector('.toggle-indicator');

                function toggleDetail() {
                    const isOpening = !detailRow.classList.contains('is-open');

                    // Close others with animation
                    document.querySelectorAll('.wilayah-detail-row.is-open').forEach((r) => {
                        if (r.id !== row.dataset.target) {
                            closeDetail(r);
                        }
                    });

                    if (isOpening) {
                        openDetail(detailRow, row);
                    } else {
                        closeDetail(detailRow, row);
                    }
                }

                function openDetail(detailRow, row) {
                    detailRow.classList.add('is-open');
                    row.setAttribute('aria-expanded', 'true');

                    // Wait for transition then scroll
                    setTimeout(() => {
                        detailRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 80);
                }

                function closeDetail(detailRow, row) {
                    detailRow.classList.remove('is-open');
                    if (row) row.setAttribute('aria-expanded', 'false');
                }

                row.addEventListener('click', toggleDetail);
                row.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        toggleDetail();
                    }
                });
            });

            // ── Auto-open if desa selected ──
            if (selectedWilayahId) {
                const activeChip = document.querySelector('.wilayah-chip.is-active');
                if (activeChip) {
                    const detailRow = activeChip.closest('.wilayah-detail-row');
                    if (detailRow && !detailRow.classList.contains('is-open')) {
                        const relatedRow = document.querySelector(`.kecamatan-row[data-target="${detailRow.id}"]`);
                        if (relatedRow) {
                            relatedRow.click();
                        }
                    }
                }
            }
        })();
    </script>
@endpush
