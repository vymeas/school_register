@extends('layouts.app')
@section('title', 'លទ្ធផលសិស្ស')
@section('page-title', 'លទ្ធផលសិស្ស')

@section('content')
<div class="card" style="border:none; box-shadow:none; background:transparent;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
        <div style="display:flex; align-items:center; gap:16px;">
            <div style="width:56px;height:56px;background:var(--accent-primary);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;box-shadow: 0 4px 12px rgba(60,80,224,0.2);">
                <i data-lucide="bar-chart-2" style="width:28px;height:28px;"></i>
            </div>
            <div>
                <h2 style="font-family:'Moul', 'Khmer OS Moul Light', cursive; color:#1e293b; font-size:24px; margin-bottom:4px;">លទ្ធផលសិស្ស</h2>
                <div style="color:var(--text-muted); font-size:14px;">បញ្ជីលទ្ធផលសិស្សតាមថ្នាក់ និងមុខវិជ្ជា</div>
            </div>
        </div>
        <div style="display:flex; gap:12px; align-items:center;">
            <button class="btn" style="background:#10b981; color:white; border:none; padding:10px 16px; border-radius:8px; font-weight:600;"><i data-lucide="file-spreadsheet" style="width:18px;height:18px;"></i> នាំចេញ Excel</button>
            <button class="btn" style="background:#ef4444; color:white; border:none; padding:10px 16px; border-radius:8px; font-weight:600;"><i data-lucide="file-text" style="width:18px;height:18px;"></i> នាំចេញ PDF</button>
            <button class="btn" style="background:var(--accent-primary); color:white; border:none; padding:10px 16px; border-radius:8px; font-weight:600;"><i data-lucide="printer" style="width:18px;height:18px;"></i> បោះពុម្ព</button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6" style="padding:20px; margin-bottom:24px; border-radius:12px;">
        <div class="toolbar" style="align-items:flex-end;">
            <div class="form-group" style="margin-bottom:0; flex:1; min-width:250px;">
                <label class="form-label" style="font-size:13px;font-weight:600;margin-bottom:8px;color:#1e293b;">ស្វែងរកសិស្ស</label>
                <div class="search-box" style="max-width:none;">
                    <i data-lucide="search" class="search-icon"></i>
                    <input type="text" placeholder="ស្វែងរកដោយឈ្មោះ ឬលេខកូដ..." style="height:46px; border-radius:8px;">
                </div>
            </div>
            <div class="form-group" style="margin-bottom:0; width:180px;">
                <label class="form-label" style="font-size:13px;font-weight:600;margin-bottom:8px;color:#1e293b;">ថ្នាក់</label>
                <select class="form-control" style="height:46px; border-radius:8px;">
                    <option>ថ្នាក់ទី ៩</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0; width:180px;">
                <label class="form-label" style="font-size:13px;font-weight:600;margin-bottom:8px;color:#1e293b;">ថ្នាក់រៀន</label>
                <select class="form-control" style="height:46px; border-radius:8px;">
                    <option>9A</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0; width:180px;">
                <label class="form-label" style="font-size:13px;font-weight:600;margin-bottom:8px;color:#1e293b;">ឆមាស</label>
                <select class="form-control" style="height:46px; border-radius:8px;">
                    <option>ឆមាសទី ១</option>
                </select>
            </div>
            <div style="display:flex; gap:12px;">
                <button class="btn" style="background:var(--accent-primary); color:white; height:46px; padding:0 20px; border-radius:8px; font-weight:600;"><i data-lucide="search" style="width:18px;height:18px;"></i> ស្វែងរក</button>
                <button class="btn" style="background:#f1f5f9; color:#475569; height:46px; padding:0 20px; border-radius:8px; font-weight:600; border:1px solid #e2e8f0;"><i data-lucide="rotate-ccw" style="width:18px;height:18px;"></i> សម្អាត</button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid" style="gap:24px; margin-bottom:24px;">
        <div class="stat-card" style="background:#f8fafc; border-color:#e2e8f0; display:flex; align-items:center; padding:24px; gap:20px; border-radius:12px;">
            <div style="color:#3b82f6;"><i data-lucide="users" style="width:54px;height:54px;"></i></div>
            <div>
                <div style="font-size:14px; color:var(--text-secondary); font-weight:600; margin-bottom:6px;">សិស្សសរុប</div>
                <div style="font-size:28px; font-weight:800; color:var(--text-heading);">{{ $students->total() }} <span style="font-size:15px; font-weight:600; color:var(--text-muted);">នាក់</span></div>
            </div>
        </div>
        <div class="stat-card" style="background:#f0fdf4; border-color:#dcfce7; display:flex; align-items:center; padding:24px; gap:20px; border-radius:12px;">
            <div style="color:#10b981;"><i data-lucide="award" style="width:54px;height:54px;"></i></div>
            <div>
                <div style="font-size:14px; color:var(--text-secondary); font-weight:600; margin-bottom:6px;">ពិន្ទុខ្ពស់បំផុត</div>
                <div style="font-size:28px; font-weight:800; color:var(--success);">98.5 <span style="font-size:15px; font-weight:600; color:var(--text-muted);">ពិន្ទុ</span></div>
            </div>
        </div>
        <div class="stat-card" style="background:#fff7ed; border-color:#ffedd5; display:flex; align-items:center; padding:24px; gap:20px; border-radius:12px;">
            <div style="color:#f59e0b;"><i data-lucide="trending-down" style="width:54px;height:54px;"></i></div>
            <div>
                <div style="font-size:14px; color:var(--text-secondary); font-weight:600; margin-bottom:6px;">ពិន្ទុទាបបំផុត</div>
                <div style="font-size:28px; font-weight:800; color:var(--warning);">45.0 <span style="font-size:15px; font-weight:600; color:var(--text-muted);">ពិន្ទុ</span></div>
            </div>
        </div>
        <div class="stat-card" style="background:#faf5ff; border-color:#f3e8ff; display:flex; align-items:center; padding:24px; gap:20px; border-radius:12px;">
            <div style="color:#8b5cf6;"><i data-lucide="trending-up" style="width:54px;height:54px;"></i></div>
            <div>
                <div style="font-size:14px; color:var(--text-secondary); font-weight:600; margin-bottom:6px;">មធ្យមភាគ</div>
                <div style="font-size:28px; font-weight:800; color:#4c1d95;">78.65 <span style="font-size:15px; font-weight:600; color:var(--text-muted);">ពិន្ទុ</span></div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card" style="border-radius:12px; overflow:hidden;">
        <div class="table-responsive">
            <table class="data-table">
                <thead style="background:var(--accent-primary);">
                    <tr>
                        <th style="color:white; border-bottom:none; font-weight:600;">ល.រ</th>
                        <th style="color:white; border-bottom:none; font-weight:600;">លេខកូដសិស្ស</th>
                        <th style="color:white; border-bottom:none; font-weight:600;">ឈ្មោះសិស្ស</th>
                        <th style="color:white; border-bottom:none; font-weight:600;">ភេទ</th>
                        <th style="color:white; border-bottom:none; font-weight:600; text-align:center;">គណិតវិទ្យា</th>
                        <th style="color:white; border-bottom:none; font-weight:600; text-align:center;">ភាសាខ្មែរ</th>
                        <th style="color:white; border-bottom:none; font-weight:600; text-align:center;">ភាសាអង់គ្លេស</th>
                        <th style="color:white; border-bottom:none; font-weight:600; text-align:center;">វិទ្យាសាស្ត្រ</th>
                        <th style="color:white; border-bottom:none; font-weight:600; text-align:center;">សរុប</th>
                        <th style="color:white; border-bottom:none; font-weight:600; text-align:center;">មធ្យមភាគ</th>
                        <th style="color:white; border-bottom:none; font-weight:600; text-align:center;">និទ្ទេស</th>
                        <th style="color:white; border-bottom:none; font-weight:600; text-align:center;">ចំណាត់ថ្នាក់</th>
                        <th style="color:white; border-bottom:none; font-weight:600; text-align:center;">សកម្មភាព</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    @php
                        $score = $student->scores->first();
                    @endphp
                    <tr>
                        <td>{{ $students->firstItem() + $index }}</td>
                        <td>{{ $student->student_code }}</td>
                        <td style="font-weight:600; color:#1e293b;">{{ $student->full_name }}</td>
                        <td>
                            @if($student->gender === 'male' || strtolower($student->gender) === 'ប្រុស')
                                <span style="color:var(--info); font-weight:600;">ប្រុស</span>
                            @else
                                <span style="color:#ec4899; font-weight:600;">ស្រី</span>
                            @endif
                        </td>
                        <td style="text-align:center;">{{ $score->math_score ?? '-' }}</td>
                        <td style="text-align:center;">{{ $score->khmer_score ?? '-' }}</td>
                        <td style="text-align:center;">{{ $score->science_score ?? '-' }}</td>
                        <td style="text-align:center;">{{ $score->sociology_score ?? '-' }}</td>
                        <td style="text-align:center; font-weight:600;">{{ $score ? $score->total : '-' }}</td>
                        <td style="text-align:center; color:var(--text-muted); font-weight:700;">{{ $score ? $score->average : '-' }}</td>
                        <td style="text-align:center; color:var(--text-muted); font-weight:700;">
                            @if($score)
                                <span class="grade-badge grade-{{ $score->grade }}">{{ $score->grade }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align:center; font-weight:600; color:#475569;">-</td>
                        <td style="text-align:center;"><button class="btn btn-icon" style="background:#f1f5f9; color:var(--accent-primary); border:1px solid #e2e8f0;"><i data-lucide="eye" style="width:16px;height:16px;"></i></button></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" style="text-align:center; padding: 20px; color:#64748b;">គ្មានទិន្នន័យសិស្សទេ</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding:16px 24px; display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:14px; color:var(--text-secondary);">
                បង្ហាញ {{ $students->firstItem() ?? 0 }} ដល់ {{ $students->lastItem() ?? 0 }} នៃ {{ $students->total() }} កំណត់ត្រា
            </div>
            <div style="display:flex; gap:20px; align-items:center;">
                <div>
                    {{ $students->links('pagination::bootstrap-4') }}
                </div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:14px; color:var(--text-secondary);">បង្ហាញ</span>
                    <select class="form-control" style="width:80px; height:40px; padding:6px 28px 6px 12px; border-radius:8px;">
                        <option>10</option>
                        <option>20</option>
                        <option>50</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
