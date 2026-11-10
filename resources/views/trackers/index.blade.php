@extends('layout')
@section('title','Academic trackers')
@section('content')
<section class="page-heading"><h1>Academic trackers</h1><p>Create a separate form for each student or enrollment plan.</p></section>
<section class="panel detail">
<h2>Create a tracker</h2>
<form class="fields" method="post" action="{{ route('coordinator.trackers.store') }}">
@csrf
<label>Student<select name="student_id" required><option value="">Choose a student</option>@foreach($students as $student)<option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>{{ $student->name }} · {{ $student->email }}</option>@endforeach</select></label>
<label>Form name<input name="name" value="{{ old('name') }}" required maxlength="120" placeholder="For example, BSIT enrollment plan"></label>
<button>Create tracker</button>
</form>
</section>
<section class="panel detail"><h2>Saved forms</h2>
<ul class="section-links">
@forelse($trackers as $tracker)
<li><a href="{{ route('coordinator.trackers.show',$tracker) }}"><span><strong>{{ $tracker->name }}</strong><small>{{ $tracker->student->name }}</small></span>Open →</a></li>
@empty <li>No saved trackers yet.</li> @endforelse
</ul>
{{ $trackers->links('workspace.pagination') }}
</section>
@endsection
