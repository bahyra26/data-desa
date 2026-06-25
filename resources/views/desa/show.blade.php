@extends('layouts.app')

@section('title', 'Detail Data Desa')

@section('content')
    <div class="page-header">
        <h1>Detail Data Desa</h1>
        <div class="actions">
            <a href="{{ route('desa.index') }}" class="button button-secondary">Kembali</a>
            <a href="{{ route('desa.export.pdf', $desa) }}" class="button button-secondary">Export PDF</a>
            @if (auth()->user()->canManageData())
                <a href="{{ route('desa.edit', $desa) }}" class="button button-warning">Edit</a>
            @endif
        </div>
    </div>

    <section class="card">
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Kecamatan</div>
                <div class="detail-value">{{ $desa->wilayah->kecamatan->nama }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Desa</div>
                <div class="detail-value">{{ $desa->wilayah->nama }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Kode Kemendagri Kecamatan</div>
                <div class="detail-value">{{ $desa->wilayah->kecamatan->kode_kemendagri }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Kode Pos Kecamatan</div>
                <div class="detail-value">{{ $desa->wilayah->kecamatan->kode_pos }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Alamat Kantor</div>
                <div class="detail-value">{{ $desa->alamat_kantor ?: '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Lokasi Kantor</div>
                <div class="detail-value">
                    @if ($desa->alamat_kantor)
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($desa->alamat_kantor) }}" target="_blank" rel="noopener" class="button button-secondary">Buka di Google Maps</a>
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Kepala Desa</div>
                <div class="detail-value">{{ $desa->kepala_desa ?: '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Jumlah Penduduk</div>
                <div class="detail-value">{{ $desa->jumlah_penduduk ? number_format($desa->jumlah_penduduk, 0, ',', '.') : '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Luas Wilayah</div>
                <div class="detail-value">{{ $desa->luas_wilayah ? number_format($desa->luas_wilayah, 2, ',', '.') . ' km2' : '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Tanggal Dibuat</div>
                <div class="detail-value">{{ $desa->created_at ? $desa->created_at->format('d/m/Y H:i') : '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Terakhir Diperbarui</div>
                <div class="detail-value">{{ $desa->updated_at ? $desa->updated_at->format('d/m/Y H:i') : '-' }}</div>
            </div>
        </div>
    </section>

    @php
        $statusLabels = [
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
            'selesai' => 'Selesai',
            'pensiun' => 'Pensiun',
        ];
    @endphp

    <section class="card">
        <div class="page-header">
            <h2>Perangkat Aktif Saat Ini</h2>
            @if (auth()->user()->canManageData())
                <a href="{{ route('perangkat.create') }}" class="button button-primary">Tambah Perangkat</a>
            @endif
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th>Masa Jabatan</th>
                        <th>Countdown</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($perangkatAktif as $perangkat)
                        <tr class="{{ $perangkat->status_masa_jabatan === 'berakhir' ? 'row-danger' : ($perangkat->status_masa_jabatan === 'hampir_berakhir' ? 'row-warning' : '') }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($perangkat->foto)
                                    <img src="{{ asset('storage/'.$perangkat->foto) }}" alt="Foto {{ $perangkat->nama }}" class="avatar">
                                @else
                                    <span class="avatar avatar-placeholder">{{ strtoupper(substr($perangkat->nama, 0, 1)) }}</span>
                                @endif
                            </td>
                            <td>{{ $perangkat->nama }}</td>
                            <td>{{ $perangkat->jabatanPerangkat->nama }}</td>
                            <td><span class="badge badge-success">{{ $statusLabels[$perangkat->status] ?? ucfirst($perangkat->status) }}</span></td>
                            <td>
                                {{ $perangkat->mulai_menjabat ? $perangkat->mulai_menjabat->format('d/m/Y') : '-' }}
                                s/d
                                {{ $perangkat->akhir_menjabat ? $perangkat->akhir_menjabat->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                <span class="muted">{{ $perangkat->countdown_masa_jabatan }}</span>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty">Belum ada perangkat aktif untuk wilayah ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card">
        <h2>Riwayat Perangkat</h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th>Masa Jabatan</th>
                        <th>Countdown</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatPerangkat as $perangkat)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($perangkat->foto)
                                    <img src="{{ asset('storage/'.$perangkat->foto) }}" alt="Foto {{ $perangkat->nama }}" class="avatar">
                                @else
                                    <span class="avatar avatar-placeholder">{{ strtoupper(substr($perangkat->nama, 0, 1)) }}</span>
                                @endif
                            </td>
                            <td>{{ $perangkat->nama }}</td>
                            <td>{{ $perangkat->jabatanPerangkat->nama }}</td>
                            <td><span class="badge badge-secondary">{{ $statusLabels[$perangkat->status] ?? ucfirst($perangkat->status) }}</span></td>
                            <td>
                                {{ $perangkat->mulai_menjabat ? $perangkat->mulai_menjabat->format('d/m/Y') : '-' }}
                                s/d
                                {{ $perangkat->akhir_menjabat ? $perangkat->akhir_menjabat->format('d/m/Y') : '-' }}
                            </td>
                            <td><span class="muted">{{ $perangkat->countdown_masa_jabatan }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">Belum ada riwayat perangkat untuk wilayah ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
