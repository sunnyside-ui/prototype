@extends('layout')
@section('title', 'My profile')
@section('content')
@php
$roleLabel = ['student'=>'Student','instructor'=>'Teacher','coordinator'=>'Coordinator'][$role];
$links = match ($role) {
    'student' => [['file','Application status','applications'],['circle-check','Requirements checklist','requirements'],['study','My subjects','subjects'],['award','Competency results','competencies']],
    'instructor' => [['users','My students','people'],['study','Assigned subjects','subjects'],['calendar','Class schedule','schedule'],['circle-check','Attendance','attendance']],
    'coordinator' => [['users','Teachers and students','people'],['file','Applications','applications'],['results','Student reports','reports'],['announcements','Announcements','announcements']],
};
$passwordErrors = $errors->getBag('password');
@endphp
<nav class="crumbs" aria-label="Breadcrumb"><a href="{{ route($role.'.index') }}">Dashboard</a> <span aria-hidden="true">/</span> <span>My profile</span></nav>

<section class="profile-head">
<span class="avatar xl" aria-hidden="true">{{ strtoupper(substr($user->name,0,1)) }}</span>
<div>
<h1>{{ $user->name }}</h1>
<p class="chip">{{ $roleLabel }}@if($role === 'student') · {{ $user->student_status }}@endif</p>
<p>{{ $user->email }}</p>
</div>
<ul class="profile-stats">
<li><strong>{{ $total }}</strong><span>Subject records</span></li>
<li><strong>{{ $published }}</strong><span>Published</span></li>
<li><strong>{{ $percent }}%</strong><span>Progress</span></li>
</ul>
</section>

<div class="dash-grid">
<div class="dash-main">
<section class="card" aria-labelledby="details-title">
<div class="card-head"><span class="icon-chip">@include('workspace.icon',['name'=>'user'])</span><h2 id="details-title">Personal details</h2></div>
<dl class="detail-list"><dt>Full name</dt><dd>{{ $user->name }}</dd><dt>Email address</dt><dd>{{ $user->email }}</dd><dt>Role</dt><dd>{{ $roleLabel }}</dd></dl>
<details @if($errors->any() && !$passwordErrors->any()) open @endif>
<summary class="button secondary">@include('workspace.icon',['name'=>'edit']) Edit details</summary>
<form class="fields" method="post" action="{{ route($role.'.profile.update') }}">
@csrf
<label>Full name<input name="name" value="{{ old('name', $user->name) }}" required maxlength="120" autocomplete="name"></label>
<label>Email address<input name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="email"></label>
<button type="submit">Save changes</button>
</form>
</details>
</section>

<section class="card" aria-labelledby="security-title">
<div class="card-head"><span class="icon-chip">@include('workspace.icon',['name'=>'lock'])</span><h2 id="security-title">Password</h2></div>
<p class="muted">Use at least 10 characters. You will stay signed in after changing it.</p>
<details @if($passwordErrors->any()) open @endif>
<summary class="button secondary">@include('workspace.icon',['name'=>'lock']) Change password</summary>
@if($passwordErrors->any())<div class="error" role="alert"><strong>Check the following and try again.</strong><ul>@foreach($passwordErrors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form class="fields" method="post" action="{{ route($role.'.profile.password') }}">
@csrf
<label>Current password<input name="current_password" type="password" required autocomplete="current-password"></label>
<label>New password<input name="password" type="password" required minlength="10" maxlength="72" autocomplete="new-password"></label>
<label>Confirm new password<input name="password_confirmation" type="password" required minlength="10" maxlength="72" autocomplete="new-password"></label>
<button type="submit">Change password</button>
</form>
</details>
</section>
</div>

<aside class="dash-side">
<section class="card" aria-labelledby="quick-title">
<h2 id="quick-title">Quick links</h2>
<ul class="tiles one">
@foreach($links as [$icon, $name, $key])
<li><a class="tile" href="{{ route($role.'.module', $key) }}"><span class="icon-chip">@include('workspace.icon',['name'=>$icon])</span><span><strong>{{ $name }}</strong></span></a></li>
@endforeach
</ul>
</section>
</aside>
</div>
@endsection
