@extends('layouts.app')

@section('title', 'Settings Profil')

@section('content')
    <div class="page-header">
        <h1>Settings Profil</h1>
    </div>

    <section class="card form-card">
        <h2>Profil Pengguna</h2>

        <form action="{{ route('settings.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="field">
                <label>Foto Profil Saat Ini</label>
                @if ($user->foto_profil)
                    <img src="{{ asset('storage/'.$user->foto_profil) }}" alt="Foto {{ $user->name }}" class="photo-preview">
                @else
                    <div class="avatar avatar-placeholder">-</div>
                @endif
            </div>

            <div class="field">
                <label for="name">Nama</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label>Role</label>
                <div class="detail-value">{{ str_replace('_', ' ', $user->role) }}</div>
                <div class="help-text">Role tidak bisa diubah dari halaman settings.</div>
            </div>

            <div class="field">
                <label for="foto_profil">Upload/Ganti Foto Profil</label>
                <input type="file" id="foto_profil" name="foto_profil" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <div class="help-text">Format jpg, jpeg, png, atau webp. Maksimal 1MB.</div>
                @error('foto_profil') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="actions">
                <button type="submit" class="button button-primary">Update Profil</button>
            </div>
        </form>
    </section>

    <section class="card form-card">
        <h2>Ganti Password</h2>

        <form action="{{ route('settings.password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="current_password">Password Lama</label>
                <input type="password" id="current_password" name="current_password">
                @error('current_password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password">Password Baru</label>
                <input type="password" id="password" name="password">
                <div class="help-text">Minimal 12 karakter, wajib ada huruf besar, huruf kecil, angka, dan simbol.</div>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Konfirmasi Password Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation">
            </div>

            <div class="actions">
                <button type="submit" class="button button-primary">Update Password</button>
            </div>
        </form>
    </section>
@endsection
