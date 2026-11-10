<form method="post" class="fields" action="{{ route('coordinator.person.save', [$person?->id]) }}">
@csrf
<label>Full name<input name="name" value="{{ $person?->name }}" required maxlength="120"></label>
<label>Email address<input name="email" value="{{ $person?->email }}" type="email" required></label>
@if(!$person)<label>Role<select name="role"><option value="student">Student</option><option value="instructor">Teacher</option></select></label>@endif
@if($person?->role !== 'instructor')<label>Student status<select name="student_status">@foreach(['Active','Inactive','Completed'] as $status)<option @selected($person?->student_status === $status)>{{ $status }}</option>@endforeach</select></label>@endif
<label>{{ $person ? 'New password (optional)' : 'Temporary password (at least 10 characters)' }}<input name="password" type="password" autocomplete="new-password" minlength="10" maxlength="72" @required(!$person)></label>
<button>Save account</button>
</form>

