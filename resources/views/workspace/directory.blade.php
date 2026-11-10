@extends('layout')
@php($title = ['people' => $role === 'coordinator' ? 'Teachers and students' : 'Student list', 'subjects' => $role === 'coordinator' ? 'Student and teacher assignments' : 'Assigned subjects', 'reports' => 'Student reports', 'profile' => 'My profile'][$module])
@section('title', $title)
@section('content')
@include('workspace.back')
<section class="page-heading"><h1>{{ $title }}</h1>@if($role === 'coordinator')<p>See what each teacher teaches and the students learning each subject.</p>@endif</section>
<section class="panel detail">
@if($module === 'profile')
<dl class="result"><dt>Name</dt><dd>{{ auth()->user()->name }}</dd><dt>Email</dt><dd>{{ auth()->user()->email }}</dd><dt>Enrollment status</dt><dd>{{ auth()->user()->student_status }}</dd></dl>
@elseif($module === 'people')
@if($role === 'coordinator')
<details><summary class="button secondary">Add a teacher or student</summary>@include('workspace.person-form',['person' => null])</details>
<details @if($errors->any() && old('subject')) open @endif><summary class="button secondary">Add student to a subject</summary>@include('workspace.enrollment-form',['item' => null])</details>
@endif
@foreach(['Teachers' => $teachers, 'Students' => $students] as $heading => $people)
@continue($heading === 'Teachers' && $role !== 'coordinator')
<h2 class="section-title">{{ $heading }}</h2>
@forelse($people as $person)
<article class="workspace-item"><h3>{{ $person->name }}</h3><p>{{ $person->email }}</p>
@if($heading === 'Students')<span class="status">{{ $person->student_status }}</span>@endif
@if($role === 'coordinator')
@php($assigned = $subjects->where($heading === 'Teachers' ? 'instructor_id' : 'student_id', $person->id))
<h4>{{ $heading === 'Teachers' ? 'Courses / subjects taught' : 'Currently learning' }}</h4>
@forelse($assigned->groupBy($heading === 'Teachers' ? 'subject_code' : 'instructor_id') as $classes)
<section class="class-roster">
<h5>{{ $heading === 'Teachers' ? $classes->first()->subject_code.' · '.$classes->first()->subject : $classes->first()->instructor->name }}</h5>
@if($heading === 'Teachers')<p>{{ $classes->unique('student_id')->count() }} {{ Str::plural('student', $classes->unique('student_id')->count()) }}</p>@endif
<ul class="roster-list">
@foreach($classes as $assignment)
<li>@if($heading === 'Teachers')<strong>{{ $assignment->student->name }}</strong>@endif<span>{{ $assignment->subject_code }} &middot; {{ $assignment->subject }}</span></li>
@endforeach
</ul>
</section>
@empty<p>{{ $heading === 'Teachers' ? 'No students or subjects yet.' : 'No subjects yet.' }}</p>@endforelse
<details><summary class="button secondary">Edit {{ $person->name }}</summary>@include('workspace.person-form')</details>@endif
</article>
@empty <p>No accounts in this group.</p> @endforelse
@endforeach
@elseif($module === 'reports')
<h2>Evaluation progress by student</h2><p>Progress is the share of assigned subjects with a published result. Use your browser’s Print command to save a PDF.</p>
@forelse($students as $student)
@php($enrollments = $subjects->where('student_id', $student->id))
@php($published = $enrollments->where('status','published')->count())
<article class="workspace-item"><h3>{{ $student->name }}</h3><p>{{ $student->student_status }} · {{ $published }} of {{ $enrollments->count() }} results published · {{ $enrollments->isEmpty() ? 0 : round(100*$published/$enrollments->count()) }}%</p>
<ul>@foreach($enrollments as $enrollment)<li>{{ $enrollment->subject }} · {{ $enrollment->statusLabel() }} @if($enrollment->status === 'published') · {{ $enrollment->score }}/100 @endif</li>@endforeach</ul></article>
@empty <p>No student reports are available.</p> @endforelse
@else
@if($role === 'coordinator')
<details @if($errors->any() && !old('assignment_id')) open @endif><summary class="button">Assign student and teacher</summary>@include('workspace.enrollment-form',['item' => null])</details>
@endif
@forelse($subjects as $item)
<article class="workspace-item"><h3>{{ $item->subject_code }} · {{ $item->subject }}</h3>
<p>Teacher: {{ $item->instructor->name }} @if($role !== 'student') · {{ $item->student->name }} @endif</p>
<a href="{{ route($role.'.show', $item) }}">Open evaluation</a>
@if($role === 'coordinator')<details @if($errors->any() && old('assignment_id') == $item->id) open @endif><summary class="button secondary">Change assignment</summary>@include('workspace.enrollment-form')</details>@endif
</article>
@empty <p>No subjects have been assigned.</p> @endforelse
@endif
</section>
@endsection


