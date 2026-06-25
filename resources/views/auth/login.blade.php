@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="login-brand">
        <img src="{{ asset('images/logo-pasuruan.png') }}" alt="Lambang Kabupaten Pasuruan" class="login-logo">
        <div class="login-brand-text">
            <span class="login-brand-title">DINAS PEMBERDAYAAN</span>
            <span class="login-brand-sub">MASYARAKAT DESA</span>
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
                <input type="password" id="password" name="password">
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
            font-size: 16px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: 0.03em;
            line-height: 1.2;
        }
        .login-brand-sub {
            font-size: 12px;
            font-weight: 750;
            color: var(--text);
            letter-spacing: 0.02em;
            line-height: 1.3;
        }
        .login-brand-reg {
            font-size: 11px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: 0.02em;
            line-height: 1.3;
            opacity: 0.8;
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
                font-size: 14px;
            }
            .login-brand-sub,
            .login-brand-reg {
                font-size: 11px;
            }
        }
    </style>
@endpush
