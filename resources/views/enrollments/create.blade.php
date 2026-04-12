@extends('layouts.app')
@section('title', __('Create Enrollment'))
@section('page-title', __('Create Enrollment'))

@section('content')
<style>
    /* ── Full Width Layout ── */
    .ce-container { width: 100%; max-width: 100%; padding-bottom: 40px; }
    
    .ce-card {
        background: #fff; border-radius: 16px; border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden;
    }

    .ce-header {
        padding: 20px 32px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        display: flex; justify-content: space-between; align-items: center;
    }
    .ce-title { font-size: 18px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 12px; }
    .ce-icon-box { 
        width: 40px; height: 40px; border-radius: 12px; background: #6366f1; color: #fff;
        display: flex; align-items: center; justify-content: center;
    }

    .ce-body { padding: 40px; }

    /* ── Selected Student Display ── */
    .selected-student-box {
        background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 16px;
        padding: 24px; display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 40px; transition: all 0.3s;
    }
    .selected-student-box.active { background: #eef2ff; border-color: #6366f1; border-style: solid; }
    
    .stu-profile { display: flex; align-items: center; gap: 16px; }
    .stu-avatar { 
        width: 56px; height: 56px; border-radius: 14px; background: #cbd5e1; 
        display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; color: #fff;
    }
    .stu-info h4 { margin: 0; font-size: 18px; font-weight: 800; color: #1e293b; }
    .stu-info p { margin: 2px 0 0; font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase; }

    /* ── Modal Styling ── */
    .stu-modal-overlay {
        position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px);
        display: none; align-items: center; justify-content: center; z-index: 9999; padding: 20px;
    }
    .stu-modal {
        background: #fff; width: 100%; max-width: 600px; border-radius: 20px; overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); animation: modalIn 0.3s ease-out;
    }
    @keyframes modalIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    
    .stu-modal-header { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .stu-modal-header h3 { font-size: 16px; font-weight: 800; margin: 0; }
    .stu-close { cursor: pointer; color: #94a3b8; border: none; background: none; }

    .stu-modal-search { padding: 20px 24px; position: relative; }
    .stu-modal-search input {
        width: 100%; padding: 12px 14px 12px 42px; border: 1px solid #e2e8f0; border-radius: 12px;
        outline: none; transition: all 0.2s; font-size: 14px;
    }
    .stu-modal-search input:focus { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99,102,241,0.1); }
    .stu-modal-search i { position: absolute; left: 38px; top: 50%; transform: translateY(-50%); color: #94a3b8; width: 18px; }

    .stu-modal-body { max-height: 400px; overflow-y: auto; padding: 0 12px 20px; }
    .stu-item { 
        display: flex; align-items: center; gap: 12px; padding: 10px 16px; 
        border-radius: 12px; cursor: pointer; transition: 0.2s;
    }
    .stu-item:hover { background: #f1f5f9; }
    .stu-item .av { width: 36px; height: 36px; border-radius: 8px; background: #cbd5e1; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:12px; }

    /* ── Form Inputs ── */
    .ce-label { font-size: 13px; font-weight: 700; color: #64748b; margin-bottom: 10px; display: block; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
    @media (max-width: 900px) { .form-grid { grid-template-columns: 1fr; } }

    .ce-select, .ce-input {
        width: 100%; padding: 14px 16px; border: 1px solid #e2e8f0; border-radius: 12px;
        font-size: 15px; font-weight: 600; color: #1e293b; background: #fff; outline: none; transition: 0.2s;
    }
    .ce-select:focus, .ce-input:focus { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99,102,241,0.1); }
    .ce-select:disabled { background: #f8fafc; opacity: 0.7; cursor: not-allowed; }

    .btn-select { 
        padding: 10px 20px; background: #6366f1; color: #fff; border: none; border-radius: 10px; 
        font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s;
    }
    .btn-select:hover { background: #4f46e5; }

    .submit-bar { margin-top: 50px; padding-top: 30px; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; }
    .btn-save { padding: 14px 40px; background: #0f172a; color: #fff; border: none; border-radius: 12px; font-weight: 700; font-size: 16px; cursor: pointer; }
</style>

<div class="ce-container">
    <div class="ce-card">
        <div class="ce-header">
            <div class="ce-title">
                <div class="ce-icon-box"><i data-lucide="user-plus"></i></div>
                <span>Student Enrollment Registration</span>
            </div>
            <a href="{{ route('enrollments.index') }}" class="btn btn-secondary btn-sm">
                <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Back to List
            </a>
        </div>

        <div class="ce-body">
            <form action="{{ route('enrollments.store') }}" method="POST" id="enrollmentForm">
                @csrf
                <input type="hidden" name="student_id" id="hiddenStudentId" value="{{ $pre_student_id }}">

                {{-- ── Step 1: Select Student ── --}}
                <label class="ce-label">Selected Student</label>
                <div class="selected-student-box {{ $pre_student_id ? 'active' : '' }}" id="selectedBox">
                    <div class="stu-profile">
                        <div class="stu-avatar" id="stuDisplayAvatar">
                            @if($pre_student_id)
                                @php $s = $students->find($pre_student_id); @endphp
                                {{ substr($s?->first_name,0,1) }}{{ substr($s?->last_name,0,1) }}
                            @else
                                ?
                            @endif
                        </div>
                        <div class="stu-info">
                            <h4 id="stuDisplayName">{{ $pre_student_id ? ($s?->first_name.' '.$s?->last_name) : 'No Student Selected' }}</h4>
                            <p id="stuDisplayCode">{{ $pre_student_id ? $s?->student_code : 'Please click choose button' }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-select" onclick="openStuModal()">
                        <i data-lucide="search" style="width:16px; vertical-align:middle; margin-right:6px;"></i> Choose Student
                    </button>
                </div>

                {{-- ── Step 2: Details ── --}}
                <div class="form-grid">
                    <div class="form-group">
                        <label class="ce-label">Academic Term</label>
                        <select name="term_id" id="termSelect" class="ce-select" required>
                            <option value="">Select Term</option>
                            @foreach($terms as $t)
                                <option value="{{ $t->id }}" {{ $t->status === 'active' ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="ce-label">Grade</label>
                        <select name="grade_id" id="gradeSelect" class="ce-select" required>
                            <option value="">Select Grade</option>
                            @foreach($grades as $g)
                                <option value="{{ $g->id }}" data-term-id="{{ $g->term_id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="ce-label">Classroom</label>
                        <select name="classroom_id" id="classroomSelect" class="ce-select" required disabled>
                            <option value="">Select Classroom</option>
                            @foreach($classrooms as $c)
                                <option value="{{ $c->id }}" data-grade-id="{{ $c->grade_id }}" data-term-id="{{ $c->grade->term_id ?? '' }}">
                                    {{ $c->name }} ({{ $c->turn->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="ce-label">Start Date</label>
                        <input type="date" name="start_date" class="ce-input" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="submit-bar">
                    <button type="submit" class="btn-save">
                        Submit Enrollment Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Student Selection Modal ── --}}
<div class="stu-modal-overlay" id="stuModal">
    <div class="stu-modal">
        <div class="stu-modal-header">
            <h3>Choose Student</h3>
            <button class="stu-close" onclick="closeStuModal()"><i data-lucide="x"></i></button>
        </div>
        <div class="stu-modal-search">
            <i data-lucide="search"></i>
            <input type="text" id="modalSearchInput" placeholder="Enter student name or code...">
        </div>
        <div class="stu-modal-body" id="modalStuList">
            @foreach($students as $s)
                <div class="stu-item" 
                     onclick="completeSelection('{{ $s->id }}', '{{ $s->first_name }} {{ $s->last_name }}', '{{ $s->student_code }}')"
                     data-search="{{ strtolower($s->student_code . ' ' . $s->first_name . ' ' . $s->last_name) }}">
                    <div class="av" style="background: {{ sprintf('#%06X', mt_rand(0x6366f1, 0xa855f7)) }}">
                        {{ substr($s->first_name,0,1) }}{{ substr($s->last_name,0,1) }}
                    </div>
                    <div>
                        <div style="font-weight:700; font-size:14px; color:#1e293b;">{{ $s->first_name }} {{ $s->last_name }}</div>
                        <div style="font-size:11px; color:#94a3b8; font-weight:600;">{{ $s->student_code }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    const stuModal = document.getElementById('stuModal');
    const modalSearchInput = document.getElementById('modalSearchInput');
    const selectedBox = document.getElementById('selectedBox');
    const hiddenStudentId = document.getElementById('hiddenStudentId');
    const stuDisplayName = document.getElementById('stuDisplayName');
    const stuDisplayCode = document.getElementById('stuDisplayCode');
    const stuDisplayAvatar = document.getElementById('stuDisplayAvatar');

    function openStuModal() { stuModal.style.display = 'flex'; modalSearchInput.focus(); }
    function closeStuModal() { stuModal.style.display = 'none'; }

    modalSearchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        document.querySelectorAll('.stu-item').forEach(item => {
            const text = item.dataset.search || "";
            item.style.display = text.includes(query) ? 'flex' : 'none';
        });
    });

    function completeSelection(id, name, code) {
        hiddenStudentId.value = id;
        stuDisplayName.textContent = name;
        stuDisplayCode.textContent = code;
        stuDisplayAvatar.textContent = name.split(' ').map(n => n[0]).join('').substr(0, 2);
        selectedBox.classList.add('active');
        closeStuModal();
    }

    // Cascading logic
    const termSelect = document.getElementById('termSelect');
    const gradeSelect = document.getElementById('gradeSelect');
    const classroomSelect = document.getElementById('classroomSelect');

    function filterGrades(termId) {
        gradeSelect.disabled = !termId;
        Array.from(gradeSelect.options).forEach(opt => {
            if (opt.value === "") return;
            opt.hidden = (opt.dataset.termId !== termId);
        });
    }

    termSelect.addEventListener('change', function() {
        filterGrades(this.value);
        gradeSelect.value = '';
        classroomSelect.disabled = true;
        classroomSelect.value = '';
    });

    gradeSelect.addEventListener('change', function() {
        const gradeId = this.value;
        const termId = termSelect.value;
        classroomSelect.disabled = !gradeId;
        classroomSelect.value = '';
        Array.from(classroomSelect.options).forEach(opt => {
            if (opt.value === "") return;
            const matchGrade = opt.dataset.gradeId === gradeId;
            const matchTerm = opt.dataset.termId === termId;
            opt.hidden = !(matchGrade && matchTerm);
        });
    });

    if (termSelect.value) filterGrades(termSelect.value);
</script>
@endpush
@endsection
