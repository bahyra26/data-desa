@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="login-brand">
        <img src="{{ asset('images/logo-pasuruan.png') }}" alt="Lambang Kabupaten Pasuruan" class="login-logo">
        <div class="login-brand-text">
            <span class="login-brand-title">DINAS PEMBERDAYAAN MASYARAKAT DESA</span>
            <span class="login-brand-reg">KABUPATEN PASURUAN</span>
        </div>
    </div>

    <div class="page-header">
        <h1>Portal Data Wilayah</h1>
    </div>

    <section class="card form-card">
        <form action="{{ route('login.store') }}" method="POST">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" autocomplete="current-password">
                    <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Tampilkan/sembunyikan password">
                        <svg id="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg id="eye-off-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field checkbox-field">
                <label>
                    <input type="checkbox" name="remember" value="1">
                    Ingat saya
                </label>
            </div>

            <div class="actions">
                <button type="submit" class="button button-primary">Login</button>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            const offIcon = document.getElementById('eye-off-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.style.display = 'none';
                offIcon.style.display = '';
            } else {
                input.type = 'password';
                icon.style.display = '';
                offIcon.style.display = 'none';
            }
        }
    </script>
@endpush

@push('styles')
    <style>
        .login-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
            padding: 20px 22px;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 10px;
            box-shadow: var(--shadow);
        }
        .login-logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .login-brand-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .login-brand-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: 0.03em;
            line-height: 1.3;
        }
        .login-brand-reg {
            font-size: 12px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: 0.02em;
            line-height: 1.3;
            opacity: 0.75;
            margin-top: 2px;
        }
        .password-wrapper {
            position: relative;
        }
        .password-wrapper input {
            padding-right: 42px;
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: var(--text);
            opacity: 0.5;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .password-toggle:hover {
            opacity: 0.8;
        }
        .password-toggle svg {
            width: 20px;
            height: 20px;
        }
        @media (max-width: 480px) {
            .login-brand {
                padding: 14px 16px;
            }
            .login-logo {
                width: 44px;
                height: 44px;
            }
            .login-brand-title {
                font-size: 13px;
            }
            .login-brand-reg {
                font-size: 11px;
            }
        }
    </style>
@endpush
