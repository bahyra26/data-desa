@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
    <div class="page-header">
        <h1>Audit Log</h1>
        <div class="actions">
            <a href="{{ route('activity-logs.export.excel', request()->query()) }}" class="button button-secondary">Export Excel</a>
            <a href="{{ route('activity-logs.export.pdf', request()->query()) }}" class="button button-secondary">Export PDF</a>
        </div>
    </div>

    <section class="card">
        <div class="filter-heading">
            <div>
                <h2>Telusuri Aktivitas</h2>
                <p class="muted">Cari user, aksi, modul, deskripsi, dan batasi rentang tanggal.</p>
            </div>
            <span class="result-pill">{{ number_format($logs->total(), 0, ',', '.') }} log</span>
        </div>
        <form action="{{ route('activity-logs.index') }}" method="GET" class="filter-form">
            <div class="search-field">
                <span class="search-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                </span>
                <input
                    type="text"
                    name="search"
                    class="filter-input"
                    value="{{ $filters['search'] }}"
                    placeholder="Cari user, action, module, deskripsi"
                >
            </div>
            <div class="filter-field compact">
                <label>Module</label>
                <select name="module" class="filter-select js-auto-submit">
                    <option value="">Semua Module</option>
                    @foreach ($modules as $module)
                        <option value="{{ $module }}" @selected($filters['module'] === $module)>{{ $module }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-field compact">
                <label>Action</label>
                <select name="action" class="filter-select js-auto-submit">
                    <option value="">Semua Action</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action }}" @selected($filters['action'] === $action)>{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-field compact">
                <label>Dari</label>
                <input type="date" name="date_from" class="filter-select" value="{{ $filters['date_from'] }}">
            </div>
            <div class="filter-field compact">
                <label>Sampai</label>
                <input type="date" name="date_to" class="filter-select" value="{{ $filters['date_to'] }}">
            </div>
            <div class="filter-actions">
                <button type="submit" class="button button-primary">Cari</button>
                @if ($filters['search'] || $filters['module'] || $filters['action'] || $filters['date_from'] || $filters['date_to'])
                    <a href="{{ route('activity-logs.index') }}" class="button button-secondary">Reset</a>
                @endif
            </div>
        </form>
    </section>

    <section class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                            <td>{{ $log->user?->name ?? 'System' }}</td>
                            <td>{{ $log->user ? str_replace('_', ' ', $log->user->role) : '-' }}</td>
                            <td><span class="badge badge-secondary">{{ $log->action }}</span></td>
                            <td><span class="badge badge-secondary">{{ $log->module }}</span></td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->ip_address ?: '-' }}</td>
                            <td>
                                <a href="{{ route('activity-logs.show', $log) }}" class="button button-secondary">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty">Belum ada audit log.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="pagination-wrap">
                <div class="muted">
                    Menampilkan {{ $logs->firstItem() }}-{{ $logs->lastItem() }} dari {{ $logs->total() }} log
                </div>
                <div class="actions">
                    @if ($logs->onFirstPage())
                        <span class="button button-secondary disabled">Sebelumnya</span>
                    @else
                        <a href="{{ $logs->previousPageUrl() }}" class="button button-secondary">Sebelumnya</a>
                    @endif

                    <span class="muted">Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() }}</span>

                    @if ($logs->hasMorePages())
                        <a href="{{ $logs->nextPageUrl() }}" class="button button-secondary">Berikutnya</a>
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
        document.querySelectorAll('.js-auto-submit').forEach((select) => {
            select.addEventListener('change', () => select.form.submit());
        });
    </script>
@endpush
