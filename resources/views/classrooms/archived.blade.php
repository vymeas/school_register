@extends('layouts.app')
@section('title', 'Archived Classrooms')
@section('page-title', 'Archived Classrooms')

@section('content')

<style>
.archived-banner {
    background: linear-gradient(135deg, #1c1c2e 0%, #2d2b4e 60%, #3d3a6e 100%);
    border: 1px solid rgba(139,92,246,0.25);
    border-radius: var(--radius-xl);
    padding: 24px 28px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.archived-banner-left { display: flex; align-items: center; gap: 16px; }
.archived-banner-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: rgba(139,92,246,0.15);
    border: 1px solid rgba(139,92,246,0.3);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px;
}
.archived-banner-text h2 { font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 2px; }
.archived-banner-text p  { font-size: 13px; color: rgba(255,255,255,0.55); }
.archived-count-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px;
    background: rgba(139,92,246,0.15);
    border: 1px solid rgba(139,92,246,0.3);
    border-radius: 20px;
    font-size: 13px; font-weight: 700;
    color: #a78bfa;
}
</style>

{{-- Banner --}}
<div class="archived-banner">
    <div class="archived-banner-left">
        <div class="archived-banner-icon">📦</div>
        <div class="archived-banner-text">
            <h2>Archived Classrooms</h2>
            <p>Classrooms below have been archived and are hidden from the main list. You can restore them at any time.</p>
        </div>
    </div>
    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
        <span class="archived-count-badge">📦 {{ $classrooms->count() }} archived</span>
        <a href="{{ route('classrooms.index') }}" class="btn btn-secondary">← Back to Classrooms</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px; padding:12px 18px; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); border-radius:var(--radius-sm); color:#10b981; font-size:13px; font-weight:600;">
        ✅ {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Term</th>
                    <th>Grade</th>
                    <th>Turn</th>
                    <th>Capacity</th>
                    <th>Teacher</th>
                    <th>Archived At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($classrooms as $classroom)
                <tr>
                    <td>
                        <strong>{{ $classroom->name }}</strong>
                    </td>
                    <td>{{ $classroom->grade->term->name ?? '—' }}</td>
                    <td>{{ $classroom->grade->name ?? '—' }}</td>
                    <td>
                        @if($classroom->turn)
                            {{ $classroom->turn->name }}
                            <br>
                            <span style="font-size:11px; color:var(--text-muted);">
                                {{ date('h:i A', strtotime($classroom->turn->start_time)) }}
                                —
                                {{ date('h:i A', strtotime($classroom->turn->end_time)) }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $classroom->capacity ?? '—' }}</td>
                    <td>{{ $classroom->teacher->name ?? '—' }}</td>
                    <td style="font-size:12px; color:var(--text-muted);">
                        {{ $classroom->updated_at->format('d M Y, h:i A') }}
                    </td>
                    <td>
                        <form method="POST" action="{{ route('classrooms.restore', $classroom->id) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success"
                                onclick="return confirm('Restore classroom \'{{ addslashes($classroom->name) }}\'?')">
                                ♻️ Restore
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-icon">📦</div>
                            <h3>No archived classrooms</h3>
                            <p>All classrooms are currently active.</p>
                            <a href="{{ route('classrooms.index') }}" class="btn btn-primary">← Back to Classrooms</a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
