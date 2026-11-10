@extends('layout')
@section('title','Sign in')
@section('content')
<div class="signin">
<span class="signin-symbol">@include('workspace.icon',['name'=>'lock'])</span>
<p class="eyebrow">YOUR ACADEMIC WORKSPACE</p>
<h1>Welcome back.</h1>
<p>Use your assigned email and password to open your workspace.</p>
<form method="post" action="{{ route('login.store') }}" class="fields">
@csrf
<label>Email address <input name="email" type="email" autocomplete="username" placeholder="you@example.com" value="{{ old('email') }}" required></label>
<div class="password-field"><label for="signin-password">Password</label><div class="password-control"><input id="signin-password" name="password" type="password" autocomplete="current-password" required><button type="button" class="password-toggle" aria-controls="signin-password" aria-pressed="false" hidden>Show</button></div></div>
<button class="signin-submit" type="submit">Sign in to workspace @include('workspace.icon',['name'=>'arrow'])</button>
</form>
<details class="demo-details"><summary><span>Exploring the prototype?<small>View demo sign-in details</small></span><span class="disclosure-plus" aria-hidden="true">+</span></summary>
<ul><li><strong>Student</strong> <span>student@demo.test</span></li><li><strong>Instructor</strong> <span>instructor@demo.test</span></li><li><strong>Coordinator</strong> <span>coordinator@demo.test</span></li></ul>
<p>Demo password for all three accounts <code>Progress2026!</code></p>
</details>
<p class="signin-footnote">@include('workspace.icon',['name'=>'study']) One place for coursework, feedback, and progress.</p>
</div>
@endsection

