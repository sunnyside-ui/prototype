@extends('layout')
@section('title', $role === 'student' ? 'My subjects' : 'Course evaluations')
@section('content')
<section class="page-heading" aria-labelledby="workspace-title">
<div>
<h1 id="workspace-title">{{ $role === 'student' ? 'My course progress' : ($role === 'instructor' ? 'Course evaluations' : 'Evaluation review') }}</h1>
<p>{{ match($role) {
'student' => 'See your assigned subjects and read published results.',
'instructor' => 'Record scores and feedback for your assigned learners.',
'coordinator' => 'Review submitted evaluations before students see them.',
} }}</p>
</div>

</section>
<section class="panel" aria-labelledby="records-title">
<div class="panel-head"><div><h2 id="records-title">{{ $role === 'student' ? 'Assigned subjects' : 'Evaluation records' }}</h2><p>{{ $role === 'coordinator' ? 'Evaluations awaiting review appear first.' : 'Choose a subject to see the details.' }}</p></div>
<form method="get" role="search" class="search">
@if($actionOnly)<input type="hidden" name="status" value="action">@endif
<label for="q">Find a record</label>
<div><input id="q" name="q" type="search" maxlength="100" value="{{ $query }}" placeholder="{{ $role === 'student' ? 'Subject name or code' : 'Student or subject' }}"><button type="submit">Search</button></div>
</form></div>
@if($query !== '') <p class="query">{{ $records->total() }} matching records · <a href="{{ route($role.'.grades') }}">Clear search</a></p> @endif
@if($actionOnly)<p class="query">Showing evaluations that need your attention · <a href="{{ route($role.'.grades') }}">Show all evaluations</a></p>@endif
<ul class="records">
@forelse($records as $record)
<li><div><small>{{ $record->subject_code }}</small><strong>{{ $record->subject }}</strong>
<span>{{ $role === 'student' ? $record->instructor->name : $record->student->name }}</span></div>
<span class="status {{ $record->status }}">{{ $role === 'student' && $record->status !== 'published' ? 'Result not yet available' : $record->statusLabel() }}</span>
<a class="button secondary" href="{{ route($role.'.show',$record) }}" aria-label="Open {{ $record->subject }}{{ $role === 'student' ? '' : ' for '.$record->student->name }}">Open</a></li>
@empty <li class="empty">{{ $query !== '' ? 'No matching records. Try a different search.' : 'No evaluations in this view.' }}</li>
@endforelse
</ul>
@if($records->hasPages()) <nav class="pager" aria-label="Results pages">@if($records->previousPageUrl()) <a class="button secondary" href="{{ $records->previousPageUrl() }}">Previous</a> @endif <span>Page {{ $records->currentPage() }} of {{ $records->lastPage() }}</span> @if($records->nextPageUrl()) <a class="button secondary" href="{{ $records->nextPageUrl() }}">Next</a> @endif</nav> @endif
</section>

@endsection



