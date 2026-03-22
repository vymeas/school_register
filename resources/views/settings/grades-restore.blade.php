@extends('layouts.app')
@section('title', 'Restore Grades')
@section('page-title', 'Restore Grades')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Deleted Grades</h2>
        <a href="{{ route('grades.index') }}" class="btn btn-secondary">Back to Grades</a>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>Name</th><th>Term</th><th>Description</th><th>Classrooms</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse ($grades as $grade)
                <tr>
                    <td><strong>{{ $grade->name }}</strong></td>
                    <td>{{ $grade->term->name ?? '—' }}</td>
                    <td>{{ $grade->description ?? '—' }}</td>
                    <td>{{ $grade->classrooms_count }}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-primary" onclick="confirmRestore('/api/grades/{{ $grade->id }}/restore', 'grade')" data-tip="Restore Grade">♻ Restore</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state"><div class="empty-icon">♻️</div><h3>No deleted grades</h3><p>There are no deleted grades to restore.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
