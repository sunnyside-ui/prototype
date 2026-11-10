@php
    $sections = match ($role) {
        'student' => ['profile'=>'My profile', 'applications'=>'Application status', 'subjects'=>'Subjects and teachers', 'lessons'=>'Lessons and materials', 'schedule'=>'Class schedule', 'attendance'=>'Attendance records', 'assignments'=>'Assignments and activities', 'projects'=>'Projects', 'submissions'=>'Submit coursework', 'competencies'=>'Competency results', 'requirements'=>'Requirements checklist', 'accreditation'=>'Accreditation status'],
        'instructor' => ['people'=>'Student list and status', 'subjects'=>'Assigned subjects', 'lessons'=>'Lessons and topics', 'schedule'=>'Class schedule', 'attendance'=>'Record attendance', 'assignments'=>'Assignments and activities', 'projects'=>'Projects', 'submissions'=>'Student submissions', 'competencies'=>'Competency evaluation'],
        'coordinator' => ['people'=>'Manage teachers and students', 'applications'=>'Application forms and decisions', 'requirements'=>'Student requirements', 'schedule'=>'Class schedules', 'attendance'=>'Attendance overview', 'competencies'=>'Competency results', 'accreditation'=>'Accreditation status', 'reports'=>'Student progress and reports'],
    };
    $sections += ['announcements'=>'Announcements', 'notifications'=>'Notifications'];
    $total = $counts['Assigned subjects'];
    $published = $counts['Published results'];
    $percent = $total ? round(100 * $published / $total) : 0;
@endphp
<section class="panel detail progress-summary" aria-labelledby="progress-title">
<h2 id="progress-title">{{ $role === 'student' ? 'My academic progress' : 'Academic progress overview' }}</h2>
<p>{{ $published }} of {{ $total }} assigned subject results published · <strong>{{ $percent }}%</strong></p>
<progress value="{{ $percent }}" max="100" aria-label="Percentage of subject results published">{{ $percent }}%</progress>
</section>
<section class="panel detail" aria-labelledby="workspace-links">
<h2 id="workspace-links">Open a workspace section</h2>
<ul class="module-grid">
@foreach($sections as $key => $label)
<li><a class="button secondary" href="{{ route($role.'.module', $key) }}">{{ $label }} <span aria-hidden="true">→</span></a></li>
@endforeach
</ul>
</section>


