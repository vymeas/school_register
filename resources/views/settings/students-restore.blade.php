@extends('layouts.app')
@section('title', 'Restore Students')
@section('page-title', 'Restore Students')

@section('content')
<div class="card">
    <div class="card-header">
        <div>
            <h2 style="margin:0;">🗂 Deleted Students</h2>
            <p style="margin:4px 0 0; font-size:13px; color:var(--text-muted);">
                Students removed from the active list. Restore to make them visible again.
            </p>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">← Back to Students</a>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Study Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($students as $student)
                <tr>
                    <td><code>{{ $student->student_code ?? '—' }}</code></td>
                    <td><strong>{{ $student->full_name }}</strong></td>
                    <td>{{ $student->gender ? ucfirst($student->gender) : '—' }}</td>
                    <td>{{ $student->date_of_birth ? $student->date_of_birth->format('d M Y') : '—' }}</td>
                    <td>
                        @if($student->study_status === 'studying')
                            <span class="badge active" style="background:rgba(16,185,129,0.12); color:#10b981;">📖 Studying</span>
                        @else
                            <span class="badge expired" style="background:rgba(239,68,68,0.1); color:#ef4444;">🚫 Dropped</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group">
                            <button
                                class="btn btn-sm btn-primary"
                                onclick="confirmRestore('/api/students/{{ $student->id }}/restore', 'student')"
                                data-tip="Restore Student"
                            >♻ Restore</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon">♻️</div>
                            <h3>No deleted students</h3>
                            <p>There are no deleted students to restore.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
