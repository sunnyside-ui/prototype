@extends('layout')
@section('title', $evaluation->subject)
@section('content')
<nav class="crumbs" aria-label="Breadcrumb"><a href="{{ route($role.'.index') }}">Dashboard</a> <span aria-hidden="true">/</span> <a href="{{ route($role.'.grades') }}">{{ $role === 'student' ? 'My grades' : 'Course evaluations' }}</a></nav>
<section class="page-heading">
<div><p class="eyebrow">{{ $evaluation->subject_code }}</p><h1>{{ $evaluation->subject }}</h1>
<p>{{ $role === 'student' ? 'Instructor · '.$evaluation->instructor->name : 'Student · '.$evaluation->student->name }}</p>
<span class="status {{ $evaluation->status }}">{{ $role === 'student' && $evaluation->status !== 'published' ? 'Result not yet available' : $evaluation->statusLabel() }}</span></div>
</section>
<section class="panel detail">
@if($role === 'student' && $evaluation->status !== 'published')
<h2>Your result will appear here</h2><p>Once the coordinator publishes the evaluation, you can read your score and feedback on this page.</p>
@elseif($role === 'instructor' && in_array($evaluation->status, ['draft','returned']))
<h2>{{ $evaluation->status === 'returned' ? 'Revise evaluation' : 'Record evaluation' }}</h2>
@if($evaluation->review_note)<div class="notice"><strong>Coordinator feedback</strong><p>{{ $evaluation->review_note }}</p></div>@endif
<p>The coordinator reviews your evaluation before the student can see it.</p>
<form class="fields" method="post" action="{{ route('instructor.submit',$evaluation) }}">
@csrf
<label>Score out of 100 <input name="score" type="number" min="0" max="100" step="1" value="{{ old('score',$evaluation->score) }}" required @error('score') aria-invalid="true" @enderror></label>
<label>Feedback for the student <textarea name="feedback" rows="5" maxlength="2000" required @error('feedback') aria-invalid="true" @enderror>{{ old('feedback',$evaluation->feedback) }}</textarea></label>
<button type="submit">Send for review</button>
</form>
@else
<h2>Evaluation details</h2>
@if($evaluation->score !== null)
<dl class="result"><dt>Score</dt><dd><strong>{{ $evaluation->score }}</strong> / 100</dd><dt>Instructor feedback</dt><dd class="prose">{{ $evaluation->feedback }}</dd></dl>
@else
<p>The instructor has not submitted an evaluation yet.</p>
@endif
@if($evaluation->review_note)<div class="notice"><strong>Changes requested</strong><p>{{ $evaluation->review_note }}</p></div>@endif
@if($role === 'coordinator' && $evaluation->status === 'submitted')
<p>Publishing makes the score and feedback visible to the student.</p>
<form method="post" action="{{ route('coordinator.review',$evaluation) }}" class="fields">
@csrf <input type="hidden" name="decision" value="published"><button type="submit">Publish result</button>
</form>
<details @error('review_note') open @enderror><summary class="button secondary">Return for changes</summary>
<form method="post" action="{{ route('coordinator.review',$evaluation) }}" class="fields">
@csrf <input type="hidden" name="decision" value="returned">
<label>What needs to change? <textarea name="review_note" rows="3" maxlength="1000" required>{{ old('review_note') }}</textarea></label>
<button class="secondary" type="submit">Send back to instructor</button>
</form>
</details>
@endif
@endif
</section>
@endsection


