@extends('layouts.app')
@section('title', 'Restore Teachers')
@section('page-title', 'Restore Teachers')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Deleted Teachers</h2>
        <a href="{{ route('teachers.index') }}" class="btn btn-secondary">Back to Teachers</a>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Teacher ID</th><th>Name</th><th>Gender</th><th>Phone</th><th>Email</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse ($teachers as $teacher)
                <tr>
                    <td><code>{{ $teacher->teacher_code ?? '—' }}</code></td>
                    <td><strong>{{ $teacher->name }}</strong></td>
                    <td>{{ $teacher->gender ? ucfirst($teacher->gender) : '—' }}</td>
                    <td>{{ $teacher->phone ?? '—' }}</td>
                    <td>{{ $teacher->email ?? '—' }}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-primary" onclick="confirmRestore('/api/teachers/{{ $teacher->id }}/restore', 'teacher')" data-tip="Restore Teacher">Restore</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><div class="empty-icon"><i data-lucide="rotate-ccw"></i></div><h3>No deleted teachers</h3><p>There are no deleted teachers to restore.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
