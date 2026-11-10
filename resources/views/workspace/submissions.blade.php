@extends('layout')
@section('title', $role === 'student' ? 'Submit coursework' : 'Student submissions')
@section('content')
@include('workspace.back')
<section class="page-heading section-heading">
<span class="section-symbol">@include('workspace.icon',['name'=>'work'])</span>
<div><h1>{{ $role === 'student' ? 'Submit coursework' : 'Student submissions' }}</h1>
<p>{{ $role === 'student' ? 'Choose a task to read the instructions and turn in your work.' : 'Open submitted work to read the response or download its attachment.' }}</p></div>
</section>

@if($role === 'student')
<div class="coursework-list">
@forelse($tasks->sortBy(fn ($task) => $task->scheduled_on ?? '9999-12-31') as $task)
@php($receipt = $submitted->get($task->id))
<article class="panel coursework-card" id="task-{{ $task->id }}">
<header><div><p class="eyebrow">{{ $task->evaluation->subject_code }} · {{ $task->module === 'projects' ? 'Project' : 'Assignment' }}</p>
<h2>{{ $task->title }}</h2>
<p>{{ $task->scheduled_on ? 'Due '.\Illuminate\Support\Carbon::parse($task->scheduled_on)->format('M j, Y') : 'No due date set' }}</p></div>
<span class="status {{ $receipt ? 'published' : 'draft' }}">{{ $receipt ? 'Submitted' : 'To submit' }}</span></header>
<details @if(old('task') == $task->id && $errors->any()) open @endif>
<summary class="button secondary">{{ $receipt ? 'View instructions and submission' : 'View instructions and submit' }}</summary>
@if($task->details)<p class="prose">{{ $task->details }}</p>@endif
@if($receipt)
<p><strong>Submitted</strong> · {{ $receipt->created_at->timezone('Asia/Manila')->format('M j, Y · g:i A') }}</p>
@include('workspace.submission-content',['item'=>$receipt])
@else
<form class="fields" method="post" enctype="multipart/form-data" action="{{ route('student.work.submit') }}">
@csrf
<input type="hidden" name="task" value="{{ $task->id }}">
<p>Enter a response, paste a share link, or attach a file.</p>
<label>Response or share link<textarea name="details" maxlength="5000" rows="4">{{ old('task') == $task->id ? old('details') : '' }}</textarea></label>
<label>Attach a file<input type="file" name="attachment" accept=".pdf,.docx,.pptx,.xlsx,.txt,.png,.jpg,.jpeg" aria-describedby="file-help-{{ $task->id }}"></label>
<small id="file-help-{{ $task->id }}">PDF, Word, PowerPoint, Excel, text or image. Maximum 10 MB.</small>
<button type="submit">Submit work</button>
</form>
@endif
</details>
</article>
@empty
<section class="panel detail"><h2>No coursework to submit yet</h2><p>Your assignments and projects will appear here when a teacher adds them.</p></section>
@endforelse
</div>
@endif

@if($role !== 'student' || $records->total() > 0)
<section class="panel detail">
<h2>{{ $role === 'student' ? 'Earlier submissions' : 'Received work' }}</h2>
@forelse($records as $item)
@continue($role === 'student' && $item->task_id)
<article class="workspace-item">
<h3>{{ $item->title }}</h3>
<p>{{ $item->evaluation->subject_code }} @if($role !== 'student') · {{ $item->evaluation->student->name }} @endif · {{ $item->created_at->timezone('Asia/Manila')->format('M j, Y · g:i A') }}</p>
<details><summary class="button secondary">Open submission</summary>@include('workspace.submission-content')</details>
</article>
@empty
<p>No work has been submitted yet.</p>
@endforelse
{{ $records->links('workspace.pagination') }}
</section>
@endif
@endsection

