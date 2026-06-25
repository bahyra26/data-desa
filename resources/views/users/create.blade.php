@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
    <div class="page-header">
        <h1>Tambah User</h1>
        <a href="{{ route('users.index') }}" class="button button-secondary">Kembali</a>
    </div>

    <section class="card form-card">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="field">
                <label for="name">Nama</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}">
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}">
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
                <div class="help-text">Minimal 12 karakter, wajib ada huruf besar, huruf kecil, angka, dan simbol.</div>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="">Pilih role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(old('role') === $role)>{{ str_replace('_', ' ', $role) }}</option>
                    @endforeach
                </select>
                @error('role') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="actions">
                <button type="submit" class="button button-primary">Simpan</button>
                <a href="{{ route('users.index') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
