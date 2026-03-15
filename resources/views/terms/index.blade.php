@extends('layouts.app')
@section('title', 'Terms')
@section('page-title', 'Academic Terms')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>All Terms</h2>
        <button class="btn btn-primary" onclick="openModal('termModal')">+ Add Term</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Name</th><th>Start Date</th><th>End Date</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($terms as $term)
                <tr>
                    <td><strong>{{ $term->name }}</strong></td>
                    <td>{{ $term->start_date->format('d M Y') }}</td>
                    <td>{{ $term->end_date->format('d M Y') }}</td>
                    <td><span class="badge {{ $term->status }}">{{ ucfirst($term->status) }}</span></td>
                    <td><div class="btn-group"><button class="btn btn-sm btn-danger" onclick="confirmDelete('/api/terms/{{ $term->id }}', 'term')">🗑</button></div></td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state"><div class="empty-icon">📅</div><h3>No terms</h3><p>Add your first academic term.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="termModal">
    <div class="modal">
        <div class="modal-header"><h3>Add Term</h3><button class="modal-close" onclick="closeModal('termModal')">✕</button></div>
        <div class="modal-body">
            <form id="termForm" method="POST" action="{{ route('terms.store') }}">
                @csrf
                <div class="form-group"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" placeholder="e.g. 2025-2026" required></div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Start Date *</label><input type="date" name="start_date" class="form-control" required></div>
                    <div class="form-group"><label class="form-label">End Date *</label><input type="date" name="end_date" class="form-control" required></div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('termModal')">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('termForm').submit()">Save</button>
        </div>
    </div>
</div>
@endsection
