@php($retry = $errors->any() && (string) old('assignment_id') === (string) $item?->id)
<form method="post" class="fields" action="{{ route('coordinator.enrollment.save', [$item?->id]) }}">
@csrf
<input type="hidden" name="assignment_id" value="{{ $item?->id }}">
@if(!$item)
<label>Student<select name="student_id" required><option value="">Choose a student</option>@foreach($students as $student)<option value="{{ $student->id }}" @selected($retry && old('student_id') == $student->id)>{{ $student->name }}</option>@endforeach</select></label>
<label>Subject code<input name="subject_code" value="{{ $retry ? old('subject_code') : '' }}" required maxlength="20" placeholder="For example, ITE 116"></label>
@else
<p><strong>{{ $item->student->name }}</strong> &middot; {{ $item->subject_code }}</p>
@endif
<label>Subject name<input name="subject" value="{{ $retry ? old('subject') : $item?->subject }}" required maxlength="160"></label>
<label>Teacher<select name="instructor_id" required><option value="">Choose a teacher</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}" @selected(($retry ? old('instructor_id') : $item?->instructor_id) == $teacher->id)>{{ $teacher->name }}</option>@endforeach</select></label>
<button>{{ $item ? 'Save assignment' : 'Assign student and teacher' }}</button>
</form>
