<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title') · ETEEAP Progress</title>
<link rel="stylesheet" href="{{ asset('progress.css') }}">
<link rel="stylesheet" href="{{ asset('refinement.css') }}">
<script src="{{ asset('signin.js') }}" defer></script>
</head>
<body class="{{ auth()->check() ? 'workspace-page' : 'entry-page' }}">
<a class="skip" href="#main">Skip to content</a>
<div class="{{ auth()->check() ? 'shell' : 'entry' }}">
<aside class="rail" @auth data-navigation-key="{{ auth()->user()->id }}-{{ auth()->user()->role }}" @endauth>
<div class="brand"><span class="mark"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="M5 9c5-1 8 0 11 3 3-3 6-4 11-3v15c-5-1-8 0-11 3-3-3-6-4-11-3zM16 12v15M12 6l4-3 4 3"/></svg></span><div><strong>ETEEAP Progress</strong><small>SPUP · Academic portal</small></div></div>
@auth
@if(in_array(auth()->user()->role, ['student','instructor','coordinator'], true))
@include('workspace.nav')@endif
<a class="account" href="{{ route(auth()->user()->role.'.profile') }}" @if(request()->routeIs(auth()->user()->role.'.profile')) aria-current="page" @endif><span class="avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->role === 'instructor' ? 'Teacher' : ucfirst(auth()->user()->role) }} · View profile</small></div></a>
<form class="signout" method="post" action="{{ route('logout') }}">@csrf <button class="secondary" type="submit">Sign out</button></form>
@else
<div class="entry-copy">
<p class="entry-kicker"><span></span> Your academic journey</p>
<h2 class="display">Your experience.<br>Your progress.<br><em>Your next chapter.</em></h2>
<p>A clearer view of your ETEEAP journey, from the work you submit to the milestones you reach.</p>
<div class="journey-preview" aria-hidden="true">
<div class="journey-preview-head"><span class="preview-symbol">@include('workspace.icon',['name'=>'study'])</span><div><strong>A little more clarity.</strong><small>Every step of the way.</small></div><span class="preview-spark">@include('workspace.icon',['name'=>'award'])</span></div>
<div class="journey-line"><span></span><span></span><span></span><span></span></div>
<div class="journey-labels"><span>Learn</span><span>Submit</span><span>Grow</span></div>
</div>
</div>
<div class="entry-footer"><span>Saint Paul University Philippines</span><span>ETEEAP</span></div>
@endif
</aside>
<script src="{{ asset('navigation.js') }}"></script>
<main id="main">

<div class="content">
@if(session('notice')) <p class="notice" role="status">{{ session('notice') }}</p> @endif
@if($errors->any())
<div class="error" role="alert"><strong>Check the following and try again.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
@yield('content')
</div>
</main>
</div>
</body>
</html>






