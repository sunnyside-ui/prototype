@extends('layout')
@section('title', $title)
@section('content')
@if($module !== 'announcements')
@include('workspace.back')
@endif
<section class="page-heading"><h1>{{ $title }}</h1>@if($module === 'courses')<p>Maintain subject and course names here. Assign students and teachers on the <a href="{{ route('coordinator.module', 'subjects') }}">assignments page</a>.</p>@elseif($module === 'schedule')<p>{{ $role === 'coordinator' ? 'Schedule classes for assigned students and teachers.' : 'Class dates arranged by your coordinator.' }}</p>@endif</section>

@if($module === 'notifications')
<section class="panel detail"><h2>Recent updates</h2><ul class="updates">
@foreach($subjects->sortByDesc('updated_at')->take(10) as $subject)
<li><a href="{{ route($role.'.show', $subject) }}">{{ $subject->subject }}</a> · {{ $role === 'student' && $subject->status !== 'published' ? 'Result not yet available' : $subject->statusLabel() }}</li>
@endforeach
@foreach($announcements as $announcement)
<li><a href="{{ route($role.'.module','announcements') }}">{{ $announcement->title }}</a> · Program announcement</li>
@endforeach
</ul></section>
@else
<section class="panel detail">
@if($canEdit)
<details @if($errors->any() && !old('record_id')) open @endif><summary class="button">{{ $module === 'courses' ? 'Add subject or course' : ($module === 'schedule' ? 'Schedule class' : 'Add record') }}</summary>
@include('workspace.record-form', ['item' => null])
</details>
@endif
@if($module === 'applications' && $role === 'student' && $records->total() === 0)
<h2>Apply to the ETEEAP program</h2>
<form class="fields" method="post" action="{{ route('student.application.submit') }}">
@csrf
<label>Program<input name="program" required maxlength="120" value="{{ old('program') }}" placeholder="For example, BS Information Technology"></label>
<label>Relevant experience and reason for applying<textarea name="details" required rows="5" maxlength="5000">{{ old('details') }}</textarea></label>
<button>Send application</button>
</form>
@endif
<h2 class="section-title">{{ match ($module) { 'courses' => 'Subjects and courses', 'schedule' => 'Scheduled classes', 'submissions' => 'Submitted work', default => 'Records' } }}</h2>
@forelse($records as $item)
<article class="workspace-item">
<h3>@if($module === 'lessons' && $item->status === 'Completed')<span aria-label="Completed">✓</span> @endif{{ $item->title }}</h3>
@if($item->evaluation || $item->student || $item->scheduled_on)
<p>{{ collect([$item->evaluation?->subject_code, $role !== 'student' ? ($item->student?->name ?? $item->evaluation?->student?->name) : null, $item->scheduled_on])->filter()->join(' · ') }}</p>
@endif
@if($module === 'schedule' && $item->evaluation)<p>Teacher: {{ $item->evaluation->instructor->name }}</p>@endif
@if($item->status)<span class="status">{{ $item->status }}</span>@endif
@if($item->details)<p class="prose">{{ $item->details }}</p>@endif
@if($role === 'student' && in_array($module, ['assignments', 'projects'], true))
<a class="button secondary" href="{{ route('student.module','submissions') }}#task-{{ $item->id }}">Submit work<span class="sr"> for {{ $item->title }}</span></a>
@endif
@if($canEdit)
<details @if($errors->any() && old('record_id') == $item->id) open @endif><summary class="button secondary">Edit<span class="sr"> {{ $item->title }}</span></summary>
@include('workspace.record-form')
</details>
@endif
</article>
@empty
<p>No records have been added here yet.</p>
@endforelse
{{ $records->links('workspace.pagination') }}
</section>
@endif
@endsection





