@extends('layouts.app')
@section('title', __('Classrooms'))
@section('page-title', __('Classrooms'))

@section('content')
<div class="card">
    <div class="card-header">
        <div class="toolbar">
            <h2>All Classrooms</h2>
            <select class="form-control" style="width:auto;" id="termFilter">
                <option value="">All Terms</option>
                @foreach($terms as $term)
                    <option value="{{ $term->id }}" {{ (string) request('term_id') === (string) $term->id ? 'selected' : '' }}>{{ $term->name }}</option>
                @endforeach
            </select>
            <select class="form-control" style="width:auto;" id="turnFilter">
                <option value="">All Turns</option>
                @foreach($turns as $turn)
                    <option value="{{ $turn->id }}" {{ (string) request('turn_id') === (string) $turn->id ? 'selected' : '' }}>
                        {{ $turn->name }}
                    </option>
                @endforeach
            </select>
            <select class="form-control" style="width:auto;" id="gradeFilter">
                <option value="">All Grades</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade->id }}" 
                            data-term-id="{{ $grade->term_id }}"
                            {{ (string) request('grade_id') === (string) $grade->id ? 'selected' : '' }}
                            style="{{ request('term_id') && (string) request('term_id') !== (string) $grade->term_id ? 'display:none;' : '' }}">
                        {{ $grade->name }} ({{ $grade->term->name ?? '' }})
                    </option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary" onclick="openAddModal()">+ Add Classroom</button>
        @if(!in_array(auth()->user()->role, ['accountant', 'admin']))
        <a href="{{ route('classrooms.archived') }}" class="btn btn-secondary" style="margin-left:8px;">Archived</a>
        @endif
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead><tr><th>{{ __('Name') }}</th><th>{{ __('Grade') }}</th><th>{{ __('Turn') }}</th><th>{{ __('Capacity') }}</th><th>{{ __('Teacher') }}</th><th>{{ __('Students') }}</th><th>{{ __('Actions') }}</th></tr></thead>
            <tbody>
            @forelse($classrooms as $classroom)
                <tr>
                    <td><strong>{{ $classroom->name }}</strong></td>
                    <td>{{ $classroom->grade->name ?? '—' }}</td>
                    <td>
                        @if($classroom->turn)
                            {{ $classroom->turn->name }} ({{ date('h:i A', strtotime($classroom->turn->start_time)) }} - {{ date('h:i A', strtotime($classroom->turn->end_time)) }})
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $classroom->capacity }}</td>
                    <td>{{ $classroom->teacher->name ?? '—' }}</td>
                    <td>{{ $classroom->enrollment_students_count ?? 0 }}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-secondary" onclick="viewClassroom({{ $classroom->id }})" data-tip="View Details"><i data-lucide="eye" style="width:14px;height:14px;"></i></button>
                            @if(auth()->user()->role !== 'accountant')
                            <button class="btn btn-sm btn-secondary" onclick="editClassroom({{ $classroom->id }}, '{{ $classroom->name }}', {{ $classroom->grade_id }}, {{ $classroom->capacity }}, {{ $classroom->grade->term_id ?? 'null' }}, {{ $classroom->turn_id ?? 'null' }}, {{ $classroom->teacher_id ?? 'null' }})" data-tip="Edit Classroom"><i data-lucide="pencil" style="width:14px;height:14px;"></i></button>
                            @endif
                            @if(auth()->user()->role !== 'accountant')
                            <button class="btn btn-sm btn-danger" onclick="confirmArchive({{ $classroom->id }}, '{{ addslashes($classroom->name) }}')" data-tip="Delete Classroom"><i data-lucide="trash-2"></i></button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><i data-lucide="school"></i></div><h3>No classrooms</h3><p>Create your first classroom.</p><button class="btn btn-primary" onclick="openAddModal()">+ Add Classroom</button></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
/* ---- Classroom View Modal (large) ---- */
#classroomViewModal .modal {
    max-width: 900px;
    width: 95%;
    max-height: 90vh;
}

.cr-modal-banner {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    padding: 28px 28px 20px;
    border-radius: var(--radius-xl) var(--radius-xl) 0 0;
    position: relative;
    overflow: hidden;
}
.cr-modal-banner::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.cr-banner-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
    z-index: 1;
}
.cr-banner-icon {
    width: 52px;
    height: 52px;
    background: rgba(255,255,255,0.15);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.2);
}
.cr-banner-title {
    flex: 1;
    margin-left: 16px;
}
.cr-banner-title h2 {
    font-size: 22px;
    font-weight: 800;
    color: #fff;
    margin-bottom: 4px;
    letter-spacing: -0.4px;
}
.cr-banner-title p {
    font-size: 13px;
    color: rgba(255,255,255,0.7);
}
.cr-banner-close {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.25);
    background: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.8);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    transition: var(--transition);
    backdrop-filter: blur(8px);
}
.cr-banner-close:hover { background: rgba(255,255,255,0.25); color: #fff; }

.cr-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 18px;
    position: relative;
    z-index: 1;
}
.cr-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: rgba(255,255,255,0.9);
    backdrop-filter: blur(4px);
}
.cr-chip span { opacity: 0.65; }

/* Stat cards row */
.cr-stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0;
    border-bottom: 1px solid var(--border-color);
}
.cr-stat-item {
    padding: 20px 24px;
    text-align: center;
    border-right: 1px solid var(--border-color);
    position: relative;
}
.cr-stat-item:last-child { border-right: none; }
.cr-stat-value {
    font-size: 32px;
    font-weight: 800;
    letter-spacing: -1px;
    line-height: 1;
    margin-bottom: 4px;
}
.cr-stat-value.blue  { color: #60a5fa; }
.cr-stat-value.green { color: #34d399; }
.cr-stat-value.amber { color: #fbbf24; }
.cr-stat-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--text-muted);
}
.cr-progress-bar {
    margin-top: 10px;
    height: 4px;
    background: var(--border-color);
    border-radius: 4px;
    overflow: hidden;
}
.cr-progress-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.6s ease;
}

/* Info grid */
.cr-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    border-bottom: 1px solid var(--border-color);
}
.cr-info-item {
    padding: 16px 24px;
    border-right: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 12px;
}
.cr-info-item:nth-child(2n) { border-right: none; }
.cr-info-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: rgba(99,102,241,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.cr-info-content label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    display: block;
    margin-bottom: 2px;
}
.cr-info-content span {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
}

/* Student section */
.cr-student-section {
    padding: 20px 24px;
}
.cr-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}
.cr-section-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-heading);
    display: flex;
    align-items: center;
    gap: 8px;
}
.cr-student-count-badge {
    padding: 2px 10px;
    background: rgba(99,102,241,0.12);
    color: #818cf8;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
}
.cr-search-input {
    padding: 7px 12px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border-light);
    background: var(--bg-input);
    color: var(--text-primary);
    font-size: 12px;
    font-family: inherit;
    outline: none;
    width: 200px;
    transition: var(--transition);
}
.cr-search-input:focus { border-color: var(--accent-primary); }
</style>

<div class="modal-overlay" id="classroomViewModal">
    <div class="modal" style="padding:0; overflow:hidden;">

        {{-- Banner Header --}}
        <div class="cr-modal-banner">
            <div class="cr-banner-top">
                <div class="cr-banner-icon"><i data-lucide="school"></i></div>
                <div class="cr-banner-title">
                    <h2 id="viewClassroomName">—</h2>
                    <p id="viewClassroomSub">Classroom Details</p>
                </div>
                <button class="cr-banner-close" onclick="closeModal('classroomViewModal')"><i data-lucide="x" style="width:18px;height:18px;"></i></button>
            </div>
            <div class="cr-chips">
                <div class="cr-chip"><span>Term:</span> <strong id="viewTermName">—</strong></div>
                <div class="cr-chip"><span>Grade:</span> <strong id="viewGradeName">—</strong></div>
                <div class="cr-chip"><span>Turn:</span> <strong id="viewTurnName">—</strong></div>
            </div>
        </div>

        {{-- Stats Row --}}
        <div class="cr-stats-row">
            <div class="cr-stat-item">
                <div class="cr-stat-value blue" id="viewCapacity">—</div>
                <div class="cr-stat-label">Total Capacity</div>
                <div class="cr-progress-bar"><div class="cr-progress-fill" id="viewCapacityBar" style="width:100%; background:#60a5fa;"></div></div>
            </div>
            <div class="cr-stat-item">
                <div class="cr-stat-value green" id="viewStudentCount">—</div>
                <div class="cr-stat-label">Enrolled Students</div>
                <div class="cr-progress-bar"><div class="cr-progress-fill" id="viewEnrolledBar" style="width:0%; background:#34d399;"></div></div>
            </div>
            <div class="cr-stat-item">
                <div class="cr-stat-value amber" id="viewAvailableSeats">—</div>
                <div class="cr-stat-label">Available Seats</div>
                <div class="cr-progress-bar"><div class="cr-progress-fill" id="viewAvailableBar" style="width:100%; background:#fbbf24;"></div></div>
            </div>
        </div>

        {{-- Info Grid --}}
        <div class="cr-info-grid">
            <div class="cr-info-item">
                <div class="cr-info-icon"><i data-lucide="user-round-check"></i></div>
                <div class="cr-info-content">
                    <label>Teacher</label>
                    <span id="viewTeacherName">—</span>
                </div>
            </div>
            <div class="cr-info-item">
                <div class="cr-info-icon"><i data-lucide="layout-dashboard"></i></div>
                <div class="cr-info-content">
                    <label>Fill Rate</label>
                    <span id="viewFillRate">—</span>
                </div>
            </div>
        </div>

        {{-- Student List --}}
        <div class="cr-student-section">
            <div class="cr-section-header">
                <div class="cr-section-title">
                    Students Roster
                    <span class="cr-student-count-badge" id="viewStudentBadge">0</span>
                </div>
                <input type="text" class="cr-search-input" id="crStudentSearch" placeholder="Search students..." oninput="filterCrStudents(this.value)">
            </div>
            <div id="viewStudentList" class="table-responsive" style="max-height: 320px; overflow-y: auto;"></div>
        </div>

    </div>
</div>

<div class="modal-overlay" id="classroomModal">
    <div class="modal">
        <div class="modal-header"><h3 id="modalTitle">Add Classroom</h3><button class="modal-close" onclick="closeModal('classroomModal')"><i data-lucide="x" style="width:18px;height:18px;"></i></button></div>
        <div class="modal-body">
            <form id="classroomForm" method="POST" action="{{ route('classrooms.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="form-group">
                    <label class="form-label">Term *</label>
                    <select id="termSelect" class="form-control" required>
                        <option value="">Select Term</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}">{{ $term->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Grade *</label>
                    <select name="grade_id" id="gradeSelect" class="form-control" required disabled>
                        <option value="">Select Term first</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" data-term-id="{{ $grade->term_id }}">{{ $grade->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Turn *</label>
                    <select name="turn_id" id="turnSelect" class="form-control" required>
                        <option value="">Select Turn</option>
                        @foreach($turns as $turn)
                            <option value="{{ $turn->id }}">{{ $turn->name }} ({{ date('h:i A', strtotime($turn->start_time)) }} - {{ date('h:i A', strtotime($turn->end_time)) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Teacher</label>
                    <select name="teacher_id" id="teacherSelect" class="form-control">
                        <option value="">No Teacher Assigned</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Name *</label><input type="text" name="name" id="classroomName" class="form-control" placeholder="e.g. Grade 1A" required></div>
                    <div class="form-group"><label class="form-label">Capacity</label><input type="number" name="capacity" id="classroomCapacity" class="form-control" value="30" min="1"></div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('classroomModal')">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('classroomForm').submit()">Save</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editClassroom(id, name, gradeId, capacity, termId, turnId, teacherId) {
    document.getElementById('modalTitle').innerText = 'Edit Classroom';
    document.getElementById('classroomForm').action = `/classrooms/${id}`;
    document.getElementById('formMethod').value = 'PUT';
    
    document.getElementById('classroomName').value = name;
    document.getElementById('classroomCapacity').value = capacity;
    document.getElementById('turnSelect').value = turnId || '';
    document.getElementById('teacherSelect').value = teacherId || '';
    
    const termSelect = document.getElementById('termSelect');
    termSelect.value = termId;
    
    // Trigger term change to filter grades
    const event = new Event('change');
    termSelect.dispatchEvent(event);
    
    // Set grade after filtering
    document.getElementById('gradeSelect').value = gradeId;
    
    openModal('classroomModal');
}

let crAllStudentsHtml = [];

async function viewClassroom(id) {
    try {
        const response = await fetch(`/api/classrooms/${id}`);
        const data = await response.json();
        const classroom = data.classroom;

        if (!classroom) return;

        const students = classroom.enrollment_students || [];
        const capacity = Number(classroom.capacity || 0);
        const enrolled = Number(classroom.enrollment_students_count ?? students.length);
        const available = Math.max(capacity - enrolled, 0);
        const fillPct = capacity > 0 ? Math.min((enrolled / capacity) * 100, 100) : 0;
        const availPct = capacity > 0 ? (available / capacity) * 100 : 0;

        // Banner
        document.getElementById('viewClassroomName').innerText = classroom.name || '—';
        document.getElementById('viewClassroomSub').innerText = 'Classroom Details';

        // Chips
        document.getElementById('viewTermName').innerText  = classroom.grade?.term?.name ?? '—';
        document.getElementById('viewGradeName').innerText = classroom.grade?.name ?? '—';
        const turn = classroom.turn;
        document.getElementById('viewTurnName').innerText  = turn
            ? `${turn.name} (${formatTime(turn.start_time)} – ${formatTime(turn.end_time)})`
            : '—';

        // Stats
        document.getElementById('viewCapacity').innerText     = capacity || '—';
        document.getElementById('viewStudentCount').innerText = enrolled;
        document.getElementById('viewAvailableSeats').innerText = available;
        document.getElementById('viewStudentBadge').innerText   = enrolled;

        // Progress bars
        document.getElementById('viewCapacityBar').style.width  = '100%';
        document.getElementById('viewEnrolledBar').style.width  = fillPct + '%';
        document.getElementById('viewAvailableBar').style.width = availPct + '%';

        // Info grid
        document.getElementById('viewTeacherName').innerText = classroom.teacher?.name || 'No Teacher Assigned';
        document.getElementById('viewFillRate').innerText    = capacity > 0 ? fillPct.toFixed(1) + '% filled' : '—';

        // Student rows
        crAllStudentsHtml = students.map(student => {
            const code   = student.student_code || '—';
            const name   = `${student.first_name || ''} ${student.last_name || ''}`.trim() || '—';
            const status = student.status || '';
            const statusLabel = status ? status.charAt(0).toUpperCase() + status.slice(1) : '—';
            return `<tr data-search="${(code + ' ' + name).toLowerCase()}">
                <td><strong>${code}</strong></td>
                <td>${name}</td>
                <td><span class="badge ${status}">${statusLabel}</span></td>
            </tr>`;
        });

        renderCrStudentTable(crAllStudentsHtml);

        // Reset search
        const searchEl = document.getElementById('crStudentSearch');
        if (searchEl) searchEl.value = '';

        openModal('classroomViewModal');
    } catch (error) {
        alert('Failed to load classroom details.');
    }
}

function renderCrStudentTable(rows) {
    const container = document.getElementById('viewStudentList');
    if (!rows.length) {
        container.innerHTML = '<div class="empty-state" style="padding:30px;"><div class="empty-icon"><i data-lucide="users"></i></div><h3>No students enrolled</h3><p>No matching enrollments found for this classroom.</p></div>';
        return;
    }
    container.innerHTML = `
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Code</th>
                    <th>Full Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                ${rows.map((row, i) => row.replace('<tr ', `<tr `).replace('<td>', `<td>${i + 1}</td><td>`)).join('')}
            </tbody>
        </table>`;
}

function filterCrStudents(query) {
    const q = query.toLowerCase().trim();
    const filtered = q ? crAllStudentsHtml.filter(r => r.includes(`data-search`) && r.split('data-search="')[1].split('"')[0].includes(q)) : crAllStudentsHtml;
    renderCrStudentTable(filtered);
}

function formatTime(timeStr) {
    if (!timeStr) return '—';
    const [h, m] = timeStr.split(':');
    const hour = parseInt(h);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const h12  = hour % 12 || 12;
    return `${h12}:${m} ${ampm}`;
}

// Function to handle "Add" button click
function openAddModal() {
    document.getElementById('modalTitle').innerText = 'Add Classroom';
    document.getElementById('classroomForm').action = "{{ route('classrooms.store') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('classroomForm').reset();
    document.getElementById('gradeSelect').disabled = true;
    document.getElementById('turnSelect').value = '';
    document.getElementById('teacherSelect').value = '';
    openModal('classroomModal');
}

document.getElementById('termSelect').addEventListener('change', function () {
    const termId = this.value;
    const gradeSelect = document.getElementById('gradeSelect');
    const options = gradeSelect.querySelectorAll('option[data-term-id]');

    // Reset grade select
    gradeSelect.value = '';

    if (!termId) {
        gradeSelect.disabled = true;
        gradeSelect.querySelector('option[value=""]').textContent = 'Select Term first';
        options.forEach(opt => opt.style.display = 'none');
        return;
    }

    gradeSelect.disabled = false;
    gradeSelect.querySelector('option[value=""]').textContent = 'Select Grade';

    let hasVisible = false;
    options.forEach(opt => {
        if (opt.dataset.termId === termId) {
            opt.style.display = '';
            hasVisible = true;
        } else {
            opt.style.display = 'none';
        }
    });

    if (!hasVisible) {
        gradeSelect.querySelector('option[value=""]').textContent = 'No grades for this term';
    }
});

document.getElementById('termFilter').addEventListener('change', function() {
    // When term changes, we filter the grade options
    const termId = this.value;
    const gradeFilter = document.getElementById('gradeFilter');
    const options = gradeFilter.querySelectorAll('option:not([value=""])');
    
    let hasMatchingSelected = false;
    options.forEach(opt => {
        if (!termId || opt.dataset.termId === termId) {
            opt.style.display = '';
            if (opt.selected) hasMatchingSelected = true;
        } else {
            opt.style.display = 'none';
        }
    });
    
    // If current selected grade doesn't match new term, reset it
    if (termId && !hasMatchingSelected && gradeFilter.value !== "") {
        gradeFilter.value = "";
    }
    
    applyClassroomFilters();
});
document.getElementById('turnFilter').addEventListener('change', applyClassroomFilters);
document.getElementById('gradeFilter').addEventListener('change', applyClassroomFilters);

function applyClassroomFilters() {
    const params = new URLSearchParams(window.location.search);
    const termFilter = document.getElementById('termFilter');
    const turnFilter = document.getElementById('turnFilter');
    const gradeFilter = document.getElementById('gradeFilter');

    if (termFilter.value) params.set('term_id', termFilter.value);
    else params.delete('term_id');

    if (turnFilter.value) params.set('turn_id', turnFilter.value);
    else params.delete('turn_id');

    if (gradeFilter.value) params.set('grade_id', gradeFilter.value);
    else params.delete('grade_id');

    params.delete('page'); // Reset to first page on filter change
    window.location.search = params.toString();
}

function confirmArchive(id, name) {
    showConfirmModal(
        `Are you sure you want to delete classroom "${name}"?<br><br><span style="font-size:13px; color:var(--text-muted);">It will be hidden from the list but can be restored from the Archive later.</span>`,
        async function() {
            try {
                const response = await fetch(`/api/classrooms/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });

                if (response.ok) {
                    showAlert('Classroom deleted successfully.');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    const data = await response.json();
                    showAlert(data.message || 'Failed to delete classroom.', 'danger');
                }
            } catch (err) {
                console.error(err);
                showAlert('An error occurred. Please try again.', 'danger');
            }
        },
        'Yes, Delete'
    );
}
</script>
@endpush
@endsection
