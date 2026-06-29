@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="login-page">
        {{-- Decorative blurred orbs --}}
        <div class="login-bg-shape login-bg-shape--1"></div>
        <div class="login-bg-shape login-bg-shape--2"></div>
        <div class="login-bg-shape login-bg-shape--3"></div>

        <div class="login-card-wrapper">
            <div class="login-card">
                {{-- Brand header --}}
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

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">Periksa kembali input yang belum sesuai.</div>
                @endif

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
                        <button type="submit" class="button button-primary login-btn">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
        /* ── Full-viewport wrapper (SCROLLABLE) ── */
        .login-page {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0a1628;
            overflow-y: auto;
            z-index: 1;
        }
        .login-page::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                linear-gradient(135deg, rgba(10, 22, 40, 0.82) 0%, rgba(19, 35, 58, 0.65) 50%, rgba(10, 22, 40, 0.85) 100%),
                url("https://dpmd.pasuruankab.go.id/storage/file_media/cc16405a863814d5402ea575f2e4d972.jpg") center / cover no-repeat;
            z-index: -1;
        }

        .login-card-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            padding: 24px;
            box-sizing: border-box;
        }

        /* ── Decorative blurred orbs ── */
        .login-bg-shape {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.35;
            pointer-events: none;
            will-change: transform;
        }
        .login-bg-shape--1 {
            width: clamp(280px, 35vw, 450px);
            height: clamp(280px, 35vw, 450px);
            background: radial-gradient(circle, #3b82f6 0%, #1d4ed8 100%);
            top: -120px;
            right: -80px;
            animation: floatSlow 8s ease-in-out infinite alternate;
        }
        .login-bg-shape--2 {
            width: clamp(240px, 30vw, 380px);
            height: clamp(240px, 30vw, 380px);
            background: radial-gradient(circle, #a78bfa 0%, #7c3aed 100%);
            bottom: -100px;
            left: -100px;
            animation: floatSlow 10s ease-in-out infinite alternate-reverse;
        }
        .login-bg-shape--3 {
            width: clamp(140px, 18vw, 220px);
            height: clamp(140px, 18vw, 220px);
            background: radial-gradient(circle, #22d3ee 0%, #0891b2 100%);
            top: 55%;
            left: 60%;
            opacity: 0.2;
            animation: floatSlow 12s ease-in-out infinite alternate;
        }
        @keyframes floatSlow {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, -30px) scale(1.08); }
        }

        /* ── Glassmorphism card ── */
        .login-card {
            position: relative;
            width: 100%;
            max-width: 420px;
            padding: 32px 30px;
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            box-shadow:
                0 12px 48px rgba(0, 0, 0, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
            z-index: 2;
            animation: cardIn 0.6s ease-out;
        }
        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ── Brand inside glass card ── */
        .login-card .login-brand {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: none;
            padding: 18px 20px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .login-card .login-logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .login-card .login-brand-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .login-card .login-brand-title {
            font-size: 15px;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.03em;
            line-height: 1.3;
        }
        .login-card .login-brand-reg {
            font-size: 12px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.7);
            letter-spacing: 0.02em;
            line-height: 1.3;
            margin-top: 2px;
        }

        /* ── Title ── */
        .login-card .page-header {
            margin-bottom: 18px;
        }
        .login-card .page-header h1 {
            color: #fff;
            font-size: 24px;
        }

        /* ── Alerts on dark glass ── */
        .login-card .alert-success {
            background: rgba(21, 128, 61, 0.25);
            border-color: rgba(21, 128, 61, 0.4);
            color: #bbf7d0;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-weight: 650;
        }
        .login-card .alert-error {
            background: rgba(220, 38, 38, 0.2);
            border-color: rgba(220, 38, 38, 0.35);
            color: #fecaca;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-weight: 650;
        }

        /* ── Form labels ── */
        .login-card label {
            color: rgba(255, 255, 255, 0.9);
            display: block;
            font-weight: 750;
            margin-bottom: 7px;
        }
        .login-card .checkbox-field label {
            color: rgba(255, 255, 255, 0.8);
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-weight: 700;
        }
        .login-card .checkbox-field input {
            width: auto;
        }

        /* ── Glass inputs ── */
        .login-card input:not([type="checkbox"]) {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 8px;
            font-size: 14px;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            box-sizing: border-box;
        }
        .login-card input:not([type="checkbox"])::placeholder {
            color: rgba(255, 255, 255, 0.35);
        }
        .login-card input:not([type="checkbox"]):hover {
            background: rgba(255, 255, 255, 0.11);
            border-color: rgba(255, 255, 255, 0.28);
        }
        .login-card input:not([type="checkbox"]):focus {
            background: rgba(255, 255, 255, 0.13);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.08);
        }

        /* ── Password wrapper ── */
        .login-card .password-wrapper {
            position: relative;
        }
        .login-card .password-wrapper input {
            padding-right: 42px;
        }
        .login-card .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }
        .login-card .password-toggle:hover {
            color: rgba(255, 255, 255, 0.9);
        }
        .login-card .password-toggle svg {
            width: 20px;
            height: 20px;
        }

        /* ── Error ── */
        .login-card .error {
            color: #fca5a5;
            font-size: 14px;
            margin-top: 6px;
            font-weight: 700;
        }

        /* ── Field spacing ── */
        .login-card .field {
            margin-bottom: 18px;
        }

        /* ── Actions ── */
        .login-card .actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        /* ── Login button ── */
        .login-btn {
            width: 100%;
            min-height: 44px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.03em;
            border-radius: 10px;
            border: 0;
            cursor: pointer;
            color: #fff;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 16px;
            line-height: 1.2;
        }
        .login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35);
        }
        .login-btn:active {
            transform: translateY(0);
        }

        /* ── Responsive: tablet / small laptop ── */
        @media (max-width: 900px) {
            .login-card-wrapper {
                padding: 20px;
            }
            .login-card {
                padding: 28px 26px;
            }
        }

        /* ── Responsive: large phone / small tablet ── */
        @media (max-width: 640px) {
            .login-card-wrapper {
                padding: 16px;
            }
            .login-card {
                max-width: 100%;
                padding: 24px 20px;
                border-radius: 16px;
            }
            .login-card .login-brand {
                padding: 14px 16px;
                gap: 12px;
            }
            .login-card .login-logo {
                width: 44px;
                height: 44px;
            }
            .login-card .login-brand-title {
                font-size: 13px;
            }
            .login-card .login-brand-reg {
                font-size: 11px;
            }
            .login-card .page-header h1 {
                font-size: 20px;
            }
            .login-card .alert-success,
            .login-card .alert-error {
                padding: 10px 12px;
                font-size: 13px;
                margin-bottom: 12px;
            }
            .login-card input:not([type="checkbox"]) {
                padding: 11px 12px;
                font-size: 16px; /* prevents iOS zoom */
            }
            .login-card .field {
                margin-bottom: 14px;
            }
        }

        /* ── Responsive: small phone ── */
        @media (max-width: 380px) {
            .login-card-wrapper {
                padding: 10px;
            }
            .login-card {
                padding: 20px 16px;
                border-radius: 14px;
            }
            .login-card .login-brand {
                padding: 12px 14px;
                gap: 10px;
            }
            .login-card .login-logo {
                width: 38px;
                height: 38px;
            }
            .login-card .login-brand-title {
                font-size: 12px;
            }
            .login-card .login-brand-reg {
                font-size: 10px;
            }
            .login-card .page-header h1 {
                font-size: 18px;
            }
            .login-btn {
                min-height: 42px;
                font-size: 14px;
            }
        }

        /* ── Responsive: short viewport height ── */
        @media (max-height: 700px) {
            .login-card-wrapper {
                padding: 16px;
            }
            .login-card {
                padding: 20px 24px;
            }
            .login-card .login-brand {
                padding: 12px 16px;
                margin-bottom: 12px;
            }
            .login-card .login-logo {
                width: 44px;
                height: 44px;
            }
            .login-card .login-brand-title {
                font-size: 13px;
            }
            .login-card .login-brand-reg {
                font-size: 11px;
            }
            .login-card .page-header {
                margin-bottom: 12px;
            }
            .login-card .page-header h1 {
                font-size: 18px;
            }
            .login-card .field {
                margin-bottom: 12px;
            }
            .login-card input:not([type="checkbox"]) {
                padding: 10px 12px;
            }
        }

        /* ── Responsive: very short viewport ── */
        @media (max-height: 600px) {
            .login-card-wrapper {
                padding: 12px;
            }
            .login-card {
                padding: 16px 20px;
                border-radius: 14px;
            }
            .login-card .login-brand {
                padding: 10px 14px;
                margin-bottom: 10px;
                gap: 10px;
            }
            .login-card .login-logo {
                width: 36px;
                height: 36px;
            }
            .login-card .login-brand-title {
                font-size: 12px;
            }
            .login-card .login-brand-reg {
                font-size: 10px;
            }
            .login-card .page-header {
                margin-bottom: 10px;
            }
            .login-card .page-header h1 {
                font-size: 16px;
            }
            .login-card .field {
                margin-bottom: 10px;
            }
            .login-card label {
                margin-bottom: 5px;
                font-size: 13px;
            }
            .login-card input:not([type="checkbox"]) {
                padding: 8px 10px;
                font-size: 13px;
            }
            .login-btn {
                min-height: 38px;
                font-size: 13px;
            }
        }

        /* ── Landscape phone ── */
        @media (max-height: 500px) and (orientation: landscape) {
            .login-card-wrapper {
                padding: 10px;
                align-items: flex-start;
                padding-top: 10px;
                padding-bottom: 10px;
            }
            .login-card {
                padding: 14px 18px;
                border-radius: 12px;
                animation: none;
            }
            .login-card .login-brand {
                padding: 8px 12px;
                margin-bottom: 8px;
                gap: 8px;
            }
            .login-card .login-logo {
                width: 32px;
                height: 32px;
            }
            .login-card .login-brand-title {
                font-size: 11px;
            }
            .login-card .login-brand-reg {
                font-size: 9px;
            }
            .login-card .page-header {
                margin-bottom: 8px;
            }
            .login-card .page-header h1 {
                font-size: 15px;
            }
            .login-card .field {
                margin-bottom: 8px;
            }
            .login-card label {
                margin-bottom: 4px;
                font-size: 12px;
            }
            .login-card input:not([type="checkbox"]) {
                padding: 7px 10px;
                font-size: 12px;
            }
            .login-btn {
                min-height: 36px;
                font-size: 12px;
            }
            .login-card .alert-success,
            .login-card .alert-error {
                padding: 8px 10px;
                font-size: 12px;
                margin-bottom: 8px;
            }
            .login-bg-shape--3 {
                display: none;
            }
        }

        /* ── Reduced motion preference ── */
        @media (prefers-reduced-motion: reduce) {
            .login-card {
                animation: none;
            }
            .login-bg-shape {
                animation: none;
            }
        }
    </style>
@endpush
