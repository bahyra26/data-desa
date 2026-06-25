@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="page-header">
        <h1>Edit User</h1>
        <a href="{{ route('users.index') }}" class="button button-secondary">Kembali</a>
    </div>

    <section class="card form-card">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

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
                <label for="password">Password Baru</label>
                <input type="password" id="password" name="password">
                <div class="help-text">Kosongkan jika tidak ingin reset password. Jika diisi: minimal 12 karakter, ada huruf besar, huruf kecil, angka, dan simbol.</div>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="">Pilih role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(old('role', $user->role) === $role)>{{ str_replace('_', ' ', $role) }}</option>
                    @endforeach
                </select>
                @error('role') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="actions">
                <button type="submit" class="button button-primary">Update</button>
                <a href="{{ route('users.index') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
