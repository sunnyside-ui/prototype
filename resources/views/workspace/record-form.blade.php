@php($retry = $errors->any() && (string) old('record_id') === (string) $item?->id)
<form class="fields" method="post" action="{{ route($role.'.record.save', [$module, $item?->id]) }}">
@csrf
<input type="hidden" name="record_id" value="{{ $item?->id }}">
@if($scope === 'subject')
<label>Student and subject<select name="evaluation_id" required><option value="">Choose an assignment</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected(($retry ? old('evaluation_id') : $item?->evaluation_id) == $subject->id)>{{ $subject->subject_code }} · {{ $subject->student->name }} ({{ $subject->instructor->name }})</option>@endforeach</select></label>
@endif
@if($scope === 'student')
<label>Student<select name="student_id" required><option value="">Choose a student</option>@foreach($students as $student)<option value="{{ $student->id }}" @selected(($retry ? old('student_id') : $item?->student_id) == $student->id)>{{ $student->name }}</option>@endforeach</select></label>
@endif
<label>{{ $module === 'courses' ? 'Subject code and name / course name' : ($module === 'schedule' ? 'Class or meeting name' : 'Title') }}<input name="title" required maxlength="160" value="{{ $retry ? old('title') : $item?->title }}"></label>
@if($statuses)
<label>Status<select name="status" required>@foreach($statuses as $status)<option @selected(($retry ? old('status') : $item?->status) === $status)>{{ $status }}</option>@endforeach</select></label>
@endif
@if(in_array($module, ['schedule','attendance','assignments','projects'], true))
<label>{{ in_array($module, ['assignments','projects']) ? 'Due date (optional)' : 'Class date' }}<input type="date" name="scheduled_on" value="{{ $retry ? old('scheduled_on') : $item?->scheduled_on }}" @required(in_array($module, ['schedule','attendance']))></label>
@endif
<label>{{ $module === 'courses' ? 'Program (optional)' : ($module === 'schedule' ? 'Time and location or meeting link' : 'Details') }}<textarea name="details" rows="4" maxlength="5000">{{ $retry ? old('details') : $item?->details }}</textarea></label>
<button type="submit">{{ $item ? 'Save changes' : ($module === 'courses' ? 'Add subject or course' : ($module === 'schedule' ? 'Schedule class' : 'Add record')) }}</button>
</form>

