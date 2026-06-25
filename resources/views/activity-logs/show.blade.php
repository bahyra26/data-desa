@extends('layouts.app')

@section('title', 'Detail Audit Log')

@section('content')
    <div class="page-header">
        <h1>Detail Audit Log</h1>
        <a href="{{ route('activity-logs.index') }}" class="button button-secondary">Kembali</a>
    </div>

    <section class="card">
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Waktu</div>
                <div class="detail-value">{{ $activityLog->created_at ? $activityLog->created_at->format('d/m/Y H:i:s') : '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">User</div>
                <div class="detail-value">{{ $activityLog->user?->name ?? 'System' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Role</div>
                <div class="detail-value">{{ $activityLog->user ? str_replace('_', ' ', $activityLog->user->role) : '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Action</div>
                <div class="detail-value">{{ $activityLog->action }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Module</div>
                <div class="detail-value">{{ $activityLog->module }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">IP Address</div>
                <div class="detail-value">{{ $activityLog->ip_address ?: '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Subject</div>
                <div class="detail-value">{{ $activityLog->subject_type ? class_basename($activityLog->subject_type).' #'.$activityLog->subject_id : '-' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">User Agent</div>
                <div class="detail-value">{{ $activityLog->user_agent ?: '-' }}</div>
            </div>
        </div>
    </section>

    <section class="card">
        <h2>Description</h2>
        <p>{{ $activityLog->description }}</p>
    </section>

    <section class="card">
        <h2>Old Values</h2>
        <pre class="json-preview">{{ $activityLog->old_values ? json_encode($activityLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '-' }}</pre>
    </section>

    <section class="card">
        <h2>New Values</h2>
        <pre class="json-preview">{{ $activityLog->new_values ? json_encode($activityLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '-' }}</pre>
    </section>
@endsection

@push('styles')
    <style>
        .json-preview { white-space: pre-wrap; overflow-x: auto; padding: 12px; border-radius: 8px; background: #111827; color: #f9fafb; line-height: 1.5; }
    </style>
@endpush
