@extends('layouts.app')
@section('title', 'Score Input')
@section('page-title', 'STUDENT SCORE INPUT')

@section('content')
<style>
    .score-input { width:60px; height:36px; text-align:center; border:1px solid #e2e8f0; border-radius:6px; font-size:13px; font-weight:600; color:#1e293b; outline:none; transition: all 0.2s; }
    .score-input:focus { border-color:var(--accent-primary); box-shadow:0 0 0 2px rgba(60,80,224,0.1); }
    .score-input.danger { background:#fee2e2; color:#dc2626; border-color:#fca5a5; }
    .score-input.warning { background:#fef3c7; color:#d97706; border-color:#fde68a; }
    .remark-input { width:100%; height:36px; border:1px solid #e2e8f0; border-radius:6px; padding:0 10px; font-size:12px; outline:none; transition: all 0.2s; color:#1e293b; }
    .remark-input::placeholder { color:#cbd5e1; }
    .remark-input:focus { border-color:var(--accent-primary); box-shadow:0 0 0 2px rgba(60,80,224,0.1); }
    .grade-badge { display:inline-flex; width:24px; height:24px; align-items:center; justify-content:center; border-radius:4px; font-weight:700; font-size:12px; color:white; }
    .grade-A { background:#16a34a; }
    .grade-B { background:#3b82f6; }
    .grade-C { background:#f59e0b; }
    .grade-D { background:#ef4444; }
    .val-blue { color:#1d4ed8; font-weight:700; }
</style>

<div style="margin-top:-20px; margin-bottom:24px; color:var(--text-muted); font-size:14px;">Enter and manage student scores</div>

<div class="form-row" style="grid-template-columns: 2fr 1fr; margin-bottom: 24px;">
    <!-- Score Information Card -->
    <div class="card" style="border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div class="card-header" style="border-bottom:1px solid #f1f5f9; padding:16px 20px; display:flex; align-items:center; gap:10px;">
            <i data-lucide="clipboard-list" style="color:var(--accent-primary); width:20px; height:20px;"></i>
            <h3 style="font-size:15px; font-weight:700; color:#1e293b; margin:0;">Score Information</h3>
        </div>
        <div class="card-body" style="padding:20px;">
            <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:16px; margin-bottom:16px;">
                <div>
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#475569;">Academic Year</label>
                    <select class="form-control" style="height:40px; font-size:13px; border-radius:8px;">
                        <option>2025-2026</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#475569;">Term</label>
                    <select class="form-control" style="height:40px; font-size:13px; border-radius:8px;">
                        <option>Semester 1</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#475569;">Class</label>
                    <select class="form-control" style="height:40px; font-size:13px; border-radius:8px;">
                        <option>Grade 5</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#475569;">Teacher</label>
                    <input type="text" class="form-control" value="Teacher Name" style="height:40px; font-size:13px; border-radius:8px;">
                </div>
            </div>
            <div style="display:grid; grid-template-columns: 1fr 3fr; gap:16px;">
                <div>
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#475569;">Date</label>
                    <div style="position:relative;">
                        <i data-lucide="calendar" style="position:absolute; left:12px; top:10px; width:16px; height:16px; color:#94a3b8;"></i>
                        <input type="text" class="form-control" value="24/05/2025" style="height:40px; padding-left:36px; font-size:13px; border-radius:8px;">
                    </div>
                </div>
                <div>
                    <label class="form-label" style="font-size:12px; font-weight:600; color:#475569;">Subject(s)</label>
                    <div style="display:flex; gap:20px; align-items:center; height:40px; border:1px solid #e2e8f0; border-radius:8px; padding:0 16px; background:#fff;">
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:500; cursor:pointer; color:#1e293b;">
                            <input type="checkbox" checked style="width:16px; height:16px; accent-color:var(--accent-primary);"> Math
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:500; cursor:pointer; color:#1e293b;">
                            <input type="checkbox" checked style="width:16px; height:16px; accent-color:var(--accent-primary);"> Khmer
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:500; cursor:pointer; color:#1e293b;">
                            <input type="checkbox" checked style="width:16px; height:16px; accent-color:var(--accent-primary);"> Science
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:500; cursor:pointer; color:#1e293b;">
                            <input type="checkbox" checked style="width:16px; height:16px; accent-color:var(--accent-primary);"> Sociology
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card" style="border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.05); background:#f8fafc;">
        <div class="card-body" style="padding:20px;">
            <div style="font-size:13px; font-weight:700; color:var(--success); margin-bottom:12px;">Score Formula</div>
            <div style="font-size:13px; color:var(--text-primary); margin-bottom:6px; font-weight:600;">Each Subject (0 - 100)</div>
            <div style="font-size:13px; color:var(--text-primary); margin-bottom:6px;"><b>Total</b> = Math + Khmer + Science + Sociology</div>
            <div style="font-size:13px; color:var(--accent-primary); font-weight:700; margin-bottom:16px;">{{ __('Average') }} = {{ __('Total') }} / 4</div>
            
            <div style="font-size:13px; font-weight:700; color:var(--success); margin-bottom:12px;">{{ __('Grade Scale') ?? 'Grade Scale' }}</div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <span class="badge" style="background:#dcfce7; color:#16a34a; font-weight:600; border:1px solid #bbf7d0; padding:6px 12px;">A (85-100)</span>
                <span class="badge" style="background:#e0f2fe; color:#0284c7; font-weight:600; border:1px solid #bae6fd; padding:6px 12px;">B (70-84)</span>
                <span class="badge" style="background:#fef3c7; color:#d97706; font-weight:600; border:1px solid #fde68a; padding:6px 12px;">C (50-69)</span>
                <span class="badge" style="background:#fee2e2; color:#dc2626; font-weight:600; border:1px solid #fecaca; padding:6px 12px;">D (<50)</span>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('scores.store') }}" method="POST" id="score-form">
    @csrf
    <!-- Toolbar -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div class="search-box" style="width:300px; max-width:100%;">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" placeholder="{{ __('Search student by name or ID...') ?? 'Search student by name or ID...' }}" style="height:42px; border-radius:8px; border:1px solid #cbd5e1;">
        </div>
        <div style="display:flex; gap:10px;">
            <button type="submit" class="btn btn-success" style="height:42px; padding:0 20px; font-weight:600; border-radius:8px; box-shadow:0 1px 2px rgba(16,185,129,0.2);"><i data-lucide="save" style="width:16px;height:16px;"></i> {{ __('Save') }}</button>
            <button type="button" class="btn btn-primary" style="height:42px; padding:0 20px; font-weight:600; border-radius:8px;"><i data-lucide="download" style="width:16px;height:16px;"></i> {{ __('Import Excel') }}</button>
            <button type="button" class="btn btn-success" style="height:42px; padding:0 20px; font-weight:600; border-radius:8px; background:#16a34a;"><i data-lucide="upload" style="width:16px;height:16px;"></i> {{ __('Export') }}</button>
            <button type="reset" class="btn btn-warning" style="height:42px; padding:0 20px; font-weight:600; border-radius:8px; background:#f97316; color:white; border-color:#f97316;"><i data-lucide="rotate-ccw" style="width:16px;height:16px;"></i> {{ __('Reset') }}</button>
            <button type="button" class="btn" style="height:42px; padding:0 20px; font-weight:600; border-radius:8px; background:#8b5cf6; color:white; border:none;"><i data-lucide="check" style="width:16px;height:16px;"></i> {{ __('Submit Final') }}</button>
        </div>
</div>

<!-- Data Table -->
<div class="card" style="border-radius:12px; overflow:hidden; border:1px solid #e2e8f0; margin-bottom:24px;">
    <div class="table-responsive">
        <table class="data-table" style="width:100%;">
            <thead style="background:#0f3b79; color:white;">
                <tr>
                    <th style="color:white; border-bottom:none; font-weight:600; padding:12px; text-align:center;">ល.រ</th>
                    <th style="color:white; border-bottom:none; font-weight:600; padding:12px; text-align:center;">{{ __('Student ID') }}</th>
                    <th style="color:white; border-bottom:none; font-weight:600; padding:12px; text-align:left;">{{ __('Student Name') }}</th>
                    <th style="color:white; border-bottom:none; font-weight:600; padding:12px; text-align:center;">{{ __('Math') }}<br><span style="font-size:10px; font-weight:400;">(0-100)</span></th>
                    <th style="color:white; border-bottom:none; font-weight:600; padding:12px; text-align:center;">{{ __('Khmer') }}<br><span style="font-size:10px; font-weight:400;">(0-100)</span></th>
                    <th style="color:white; border-bottom:none; font-weight:600; padding:12px; text-align:center;">{{ __('Science') }}<br><span style="font-size:10px; font-weight:400;">(0-100)</span></th>
                    <th style="color:white; border-bottom:none; font-weight:600; padding:12px; text-align:center;">{{ __('Sociology') }}<br><span style="font-size:10px; font-weight:400;">(0-100)</span></th>
                    <th style="color:white; border-bottom:none; font-weight:600; padding:12px; text-align:center;">{{ __('Total') }}<br><span style="font-size:10px; font-weight:400;">(0-400)</span></th>
                    <th style="color:white; border-bottom:none; font-weight:600; padding:12px; text-align:center;">{{ __('Average') }}<br><span style="font-size:10px; font-weight:400;">(0-100)</span></th>
                    <th style="color:white; border-bottom:none; font-weight:600; padding:12px; text-align:center;">{{ __('Grade') }}</th>
                    <th style="color:white; border-bottom:none; font-weight:600; padding:12px; text-align:left; min-width:120px;">{{ __('Remark') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $student)
                @php
                    $score = $student->scores->first();
                @endphp
                <tr>
                    <td style="text-align:center; color:#64748b;">{{ $students->firstItem() + $index }}</td>
                    <td style="text-align:center; color:#475569; font-weight:500;">
                        {{ $student->student_code }}
                        <input type="hidden" name="scores[{{ $index }}][student_id]" value="{{ $student->id }}">
                    </td>
                    <td style="color:#1e293b; font-weight:500;">{{ $student->full_name }}</td>
                    <td style="text-align:center;"><input type="text" name="scores[{{ $index }}][math_score]" class="score-input" value="{{ $score->math_score ?? '' }}"></td>
                    <td style="text-align:center;"><input type="text" name="scores[{{ $index }}][khmer_score]" class="score-input" value="{{ $score->khmer_score ?? '' }}"></td>
                    <td style="text-align:center;"><input type="text" name="scores[{{ $index }}][science_score]" class="score-input" value="{{ $score->science_score ?? '' }}"></td>
                    <td style="text-align:center;"><input type="text" name="scores[{{ $index }}][sociology_score]" class="score-input" value="{{ $score->sociology_score ?? '' }}"></td>
                    <td style="text-align:center;" class="val-blue">{{ $score ? $score->total : '-' }}</td>
                    <td style="text-align:center;" class="val-blue">{{ $score ? $score->average : '-' }}</td>
                    <td style="text-align:center;">
                        @if($score)
                            <span class="grade-badge grade-{{ $score->grade }}">{{ $score->grade }}</span>
                        @else
                            <span class="grade-badge" style="background:#cbd5e1;">-</span>
                        @endif
                    </td>
                    <td><input type="text" name="scores[{{ $index }}][remark]" class="remark-input" placeholder="Enter remark" value="{{ $score->remark ?? '' }}"></td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="text-align:center; padding: 20px; color:#64748b;">No students found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="padding:16px 20px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:13px; color:var(--text-secondary);">
                Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} students
            </div>
            <div style="display:flex; gap:16px; align-items:center;">
                <div>
                    {{ $students->links('pagination::bootstrap-4') }}
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <select class="form-control" style="width:100px; height:34px; padding:4px 28px 4px 12px; border-radius:6px; font-size:13px;">
                        <option>10 / page</option>
                        <option>20 / page</option>
                        <option>50 / page</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Bottom Stats and Note -->
<div style="display:grid; grid-template-columns: repeat(5, 1fr); gap:20px; margin-bottom:24px;">
    <!-- Total Students -->
    <div class="card" style="border-radius:12px; border:1px solid #e0e7ff; background:#f5f8ff; display:flex; align-items:center; justify-content:center; padding:20px; gap:16px;">
        <div style="color:#2563eb;"><i data-lucide="users" style="width:40px;height:40px; fill:#2563eb;"></i></div>
        <div style="text-align:center;">
            <div style="font-size:13px; color:#1e40af; font-weight:600; margin-bottom:4px;">{{ __('Total Students') }}</div>
            <div style="font-size:24px; font-weight:800; color:#1e3a8a;">{{ $students->total() }}</div>
        </div>
    </div>
    
    <!-- Average Score -->
    <div class="card" style="border-radius:12px; border:1px solid #dcfce7; background:#f0fdf4; display:flex; align-items:center; justify-content:center; padding:20px; gap:16px;">
        <div style="color:#16a34a;"><i data-lucide="bar-chart" style="width:40px;height:40px;"></i></div>
        <div style="text-align:center;">
            <div style="font-size:13px; color:#166534; font-weight:600; margin-bottom:4px;">{{ __('Average Score') }}</div>
            <div style="font-size:24px; font-weight:800; color:#14532d;">67.88</div>
        </div>
    </div>
    
    <!-- Pass -->
    <div class="card" style="border-radius:12px; border:1px solid #dcfce7; background:#f0fdf4; display:flex; align-items:center; justify-content:center; padding:20px; gap:16px;">
        <div style="color:white; background:#16a34a; border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center;"><i data-lucide="check" style="width:24px;height:24px;"></i></div>
        <div style="text-align:center;">
            <div style="font-size:13px; color:#166534; font-weight:600; margin-bottom:4px;">{{ __('Pass') }} (≥ 50)</div>
            <div style="font-size:20px; font-weight:800; color:#14532d;">32 <span style="font-size:14px; font-weight:600;">(80%)</span></div>
        </div>
    </div>
    
    <!-- Fail -->
    <div class="card" style="border-radius:12px; border:1px solid #fee2e2; background:#fef2f2; display:flex; align-items:center; justify-content:center; padding:20px; gap:16px;">
        <div style="color:white; background:#dc2626; border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center;"><i data-lucide="x" style="width:24px;height:24px;"></i></div>
        <div style="text-align:center;">
            <div style="font-size:13px; color:#991b1b; font-weight:600; margin-bottom:4px;">Fail (< 50)</div>
            <div style="font-size:20px; font-weight:800; color:#7f1d1d;">8 <span style="font-size:14px; font-weight:600;">(20%)</span></div>
        </div>
    </div>

    <!-- Note -->
    <div class="card" style="border-radius:12px; border:1px solid #e2e8f0; background:#f8fafc; padding:16px; grid-column: auto;">
        <div style="font-size:13px; font-weight:700; color:#0f3b79; margin-bottom:8px;">Note</div>
        <ul style="padding-left:16px; margin:0; font-size:11px; color:#3b82f6; line-height:1.6;">
            <li>Enter scores between 0 and 100 only.</li>
            <li>Total and Average are calculated automatically.</li>
            <li>Click Save to keep changes.</li>
        </ul>
    </div>
</div>

@endsection
