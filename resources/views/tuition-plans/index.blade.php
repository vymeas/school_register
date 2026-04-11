@extends('layouts.app')
@section('title', __('Tuition Plans'))
@section('page-title', __('Tuition Plans'))

@section('content')
<div class="card">
    <div class="card-header">
        <h2>All Tuition Plans</h2>
        <button class="btn btn-primary" onclick="openModal('planModal')">+ Add Plan</button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>{{ __('Name') }}</th><th>{{ __('Classroom') }}</th><th>{{ __('Duration') }}</th><th>{{ __('Price') }}</th><th>{{ __('Status') }}</th><th>{{ __('Actions') }}</th></tr></thead>
            <tbody>
            @forelse($plans as $plan)
                <tr>
                    <td><strong>{{ $plan->name }}</strong></td>
                    <td>{{ $plan->classroom ?? '—' }}</td>
                    <td>{{ $plan->duration_month }} month{{ $plan->duration_month > 1 ? 's' : '' }}</td>
                    <td><strong>${{ number_format($plan->price, 2) }}</strong></td>
                    <td><span class="badge {{ $plan->status }}">{{ ucfirst($plan->status) }}</span></td>
                    <td><div class="btn-group">
                        @if(auth()->user()->role !== 'accountant')
                        <button class="btn btn-sm btn-danger" onclick="confirmDelete('/api/tuition-plans/{{ $plan->id }}', 'tuition plan')" data-tip="Delete Plan"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
                        @endif
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state"><div class="empty-icon"><i data-lucide="banknote"></i></div><h3>No tuition plans</h3></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="planModal">
    <div class="modal">
        <div class="modal-header"><h3>Add Tuition Plan</h3><button class="modal-close" onclick="closeModal('planModal')"><i data-lucide="x" style="width:18px;height:18px;"></i></button></div>
        <div class="modal-body">
            <form id="planForm" method="POST" action="{{ route('tuition-plans.store') }}">
                @csrf
                <div class="form-group"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" placeholder="e.g. 3 Months" required></div>
                <div class="form-group">
                    <label class="form-label">Classroom (write & option)</label>
                    <input type="text" name="classroom" class="form-control" list="classroomList" placeholder="Select or type classroom">
                    <datalist id="classroomList">
                        @foreach($classrooms as $classroomName)
                            <option value="{{ $classroomName }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Duration (months) *</label><input type="number" name="duration_month" class="form-control" min="1" required></div>
                    <div class="form-group"><label class="form-label">Price ($) *</label><input type="number" name="price" class="form-control" step="0.01" min="0" required></div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('planModal')">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('planForm').submit()">Save</button>
        </div>
    </div>
</div>
@endsection
