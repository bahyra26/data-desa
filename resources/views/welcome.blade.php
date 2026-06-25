<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta http-equiv="refresh" content="0;url={{ url('/dashboard') }}">
</head>
<body>
    <p style="font-family:sans-serif;text-align:center;padding:4rem 1rem;color:#666;">
        Redirecting to <a href="{{ url('/dashboard') }}" style="color:#2563eb;">Dashboard</a>...
    </p>
    <script>window.location.href='{{ url('/dashboard') }}';</script>
</body>
</html>
