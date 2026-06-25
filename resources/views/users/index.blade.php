@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
    <div class="page-header">
        <h1>Manajemen User</h1>
        <a href="{{ route('users.create') }}" class="button button-primary">Tambah User</a>
    </div>

    <section class="card">
        <div class="filter-heading">
            <div>
                <h2>Kelola Akses</h2>
                <p class="muted">Cari akun berdasarkan nama atau email, lalu filter role pengguna.</p>
            </div>
            <span class="result-pill">{{ number_format($users->total(), 0, ',', '.') }} user</span>
        </div>
        <form action="{{ route('users.index') }}" method="GET" class="filter-form">
            <div class="search-field">
                <span class="search-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                </span>
                <input
                    type="text"
                    name="search"
                    class="filter-input"
                    value="{{ $filters['search'] }}"
                    placeholder="Cari nama atau email"
                >
            </div>
            <div class="filter-field compact">
                <label>Role</label>
                <select name="role" class="filter-select js-auto-submit">
                    <option value="">Semua Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected($filters['role'] === $role)>{{ str_replace('_', ' ', $role) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-field compact">
                <label>Urutkan</label>
                <select name="sort" class="filter-select js-auto-submit">
                    <option value="name_asc" @selected($filters['sort'] === 'name_asc')>Nama A-Z</option>
                    <option value="name_desc" @selected($filters['sort'] === 'name_desc')>Nama Z-A</option>
                    <option value="email_asc" @selected($filters['sort'] === 'email_asc')>Email A-Z</option>
                    <option value="latest" @selected($filters['sort'] === 'latest')>Terbaru</option>
                    <option value="oldest" @selected($filters['sort'] === 'oldest')>Terlama</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="button button-primary">Cari</button>
                @if ($filters['search'] || $filters['role'] || $filters['sort'] !== 'name_asc')
                    <a href="{{ route('users.index') }}" class="button button-secondary">Reset</a>
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
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $users->firstItem() + $loop->index }}</td>
                            <td>
                                @if ($user->foto_profil)
                                    <img src="{{ asset('storage/'.$user->foto_profil) }}" alt="Foto {{ $user->name }}" class="avatar">
                                @else
                                    <span class="avatar avatar-placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                @endif
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge badge-secondary">{{ str_replace('_', ' ', $user->role) }}</span></td>
                            <td>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('users.edit', $user) }}" class="button button-warning">Edit / Reset Password</a>
                                    @if (! auth()->user()->is($user))
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button button-danger">Hapus</button>
                                        </form>
                                    @else
                                        <span class="muted">Akun aktif</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">Belum ada user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="pagination-wrap">
                <div class="muted">
                    Menampilkan {{ $users->firstItem() }}-{{ $users->lastItem() }} dari {{ $users->total() }} user
                </div>
                <div class="actions">
                    @if ($users->onFirstPage())
                        <span class="button button-secondary disabled">Sebelumnya</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="button button-secondary">Sebelumnya</a>
                    @endif

                    <span class="muted">Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}</span>

                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="button button-secondary">Berikutnya</a>
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
