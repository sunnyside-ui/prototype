@extends('layout')
@section('title', 'Dashboard')
@section('content')
@php
$first = explode(' ', auth()->user()->name)[0];
$hour = (int) now('Asia/Manila')->format('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
$roleLabel = ['student'=>'Student','instructor'=>'Teacher','coordinator'=>'Coordinator'][$role];
$next = $upcoming ? \Illuminate\Support\Carbon::parse($upcoming->scheduled_on)->format('M j') : 'None';

$stats = match ($role) {
    'student' => [
        ['study','Subjects', $total, 'Assigned to you', route($role.'.module','subjects'), 'lilac'],
        ['circle-check','Results published', $published.' of '.$total, 'Ready to read', route($role.'.grades'), 'mint'],
        ['clipboard','Coursework to submit', $openTasks, $openTasks ? 'Not yet turned in' : 'All turned in', route($role.'.module','submissions'), 'amber'],
        ['calendar','Next class', $next, $upcoming?->title ?? 'Nothing scheduled', route($role.'.module','schedule'), 'sky'],
    ],
    'instructor' => [
        ['study','Assigned records', $total, 'Learner and subject pairs', route($role.'.module','subjects'), 'lilac'],
        ['edit','To evaluate', $attention, 'Drafts or returned', route($role.'.grades',['status'=>'action']), 'amber'],
        ['clock','Awaiting review', $awaiting, 'With the coordinator', route($role.'.grades'), 'sky'],
        ['circle-check','Published', $published, 'Visible to students', route($role.'.grades'), 'mint'],
    ],
    'coordinator' => [
        ['file','Pending applications', $attention, 'Need a decision', route($role.'.module','applications'), 'amber'],
        ['clock','Awaiting review', $awaiting, 'Evaluations to check', route($role.'.grades',['status'=>'action']), 'sky'],
        ['circle-check','Published', $published, 'Results released', route($role.'.grades'), 'mint'],
        ['users','Evaluation records', $total, 'Across all learners', route($role.'.grades'), 'lilac'],
    ],
};

$tiles = match ($role) {
    'student' => [
        ['clipboard','Assignments','See what is due','assignments'],
        ['upload','Submit work','Turn in a task','submissions'],
        ['results','Grades and feedback','Read published results','grades'],
        ['circle-check','Attendance','Check your record','attendance'],
        ['file','Requirements','What is still needed','requirements'],
        ['user','My profile','Details and password','profile'],
    ],
    'instructor' => [
        ['results','Evaluations','Scores and feedback','grades'],
        ['circle-check','Attendance','Mark who attended','attendance'],
        ['clipboard','Coursework','Set tasks for learners','assignments'],
        ['upload','Submissions','Review turned-in work','submissions'],
        ['calendar','Schedule','Class dates','schedule'],
        ['users','Students','Your learners','people'],
    ],
    'coordinator' => [
        ['file','Applications','Review and decide','applications'],
        ['circle-check','Requirements','Track what is missing','requirements'],
        ['users','People','Teachers and students','people'],
        ['results','Reports','Printable progress','reports'],
        ['announcements','Announcements','Post program news','announcements'],
    ],
};
$tileLink = fn ($key) => $key === 'grades' ? route($role.'.grades') : ($key === 'profile' ? route($role.'.profile') : route($role.'.module', $key));

[$todoTitle, $todoText, $todoLabel, $todoLink] = match ($role) {
    'student' => $openTasks
        ? ['You have '.$openTasks.' '.Str::plural('task', $openTasks).' to turn in', 'Open your coursework to read the instructions and due dates.', 'Submit work', route($role.'.module','submissions')]
        : ['You are up to date', 'Nothing is waiting for you. You can read your results or check the schedule.', 'View grades', route($role.'.grades')],
    'instructor' => $attention
        ? [$attention.' '.Str::plural('evaluation', $attention).' to complete', 'Enter scores and feedback, or revise ones the coordinator returned.', 'Start evaluations', route($role.'.grades',['status'=>'action'])]
        : ['Nothing to evaluate right now', 'All of your evaluations are submitted or published.', 'View evaluations', route($role.'.grades')],
    'coordinator' => ($attention + $awaiting)
        ? [$attention.' '.Str::plural('application', $attention).' and '.$awaiting.' '.Str::plural('evaluation', $awaiting).' waiting', 'Decide on applications first, then review submitted evaluations.', $attention ? 'Review applications' : 'Review evaluations', $attention ? route($role.'.module','applications') : route($role.'.grades',['status'=>'action'])]
        : ['Nothing is waiting for you', 'No pending applications or evaluations to review.', 'View reports', route($role.'.module','reports')],
};
@endphp

<section class="welcome" aria-labelledby="welcome-title">
<div>
<p class="chip">{{ $roleLabel }} · {{ now('Asia/Manila')->format('l, F j') }}</p>
<h1 id="welcome-title">{{ $greeting }}, {{ $first }}</h1>
<p>{{ match($role) {'student'=>'Here is where your coursework and results stand today.','instructor'=>'Here is what needs your attention in your classes.','coordinator'=>'Here is what needs review across the ETEEAP program.'} }}</p>
</div>
<svg class="welcome-art" viewBox="0 0 160 110" aria-hidden="true"><rect x="14" y="30" width="86" height="64" rx="10" fill="#fff" opacity=".85"/><rect x="26" y="44" width="46" height="7" rx="3.5" fill="#513375"/><rect x="26" y="58" width="62" height="6" rx="3" fill="#b9a2d6"/><rect x="26" y="70" width="40" height="6" rx="3" fill="#d9c9ec"/><circle cx="118" cy="40" r="26" fill="#513375"/><path d="M106 40l8 8 14-16" fill="none" stroke="#fff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/><rect x="104" y="72" width="40" height="22" rx="8" fill="#e8a94a"/></svg>
</section>

<h2 class="visually-hidden">Summary</h2>
<ul class="stat-cards">
@foreach($stats as [$icon, $label, $value, $note, $link, $tone])
<li><a class="stat-card tone-{{ $tone }}" href="{{ $link }}"><span class="icon-chip">@include('workspace.icon',['name'=>$icon])</span><span class="stat-label">{{ $label }}</span><strong class="stat-value">{{ $value }}</strong><span class="stat-note">{{ $note }}</span></a></li>
@endforeach
</ul>

<div class="dash-grid">
<div class="dash-main">
<section class="todo" aria-labelledby="todo-title">
<span class="icon-chip big">@include('workspace.icon',['name'=>'clock'])</span>
<div><p class="label">Next step</p><h2 id="todo-title">{{ $todoTitle }}</h2><p>{{ $todoText }}</p></div>
<a class="button" href="{{ $todoLink }}">{{ $todoLabel }}</a>
</section>

<section aria-labelledby="tiles-title">
<h2 id="tiles-title" class="block-title">Go to</h2>
<ul class="tiles">
@foreach($tiles as [$icon, $name, $hint, $key])
<li><a class="tile" href="{{ $tileLink($key) }}"><span class="icon-chip">@include('workspace.icon',['name'=>$icon])</span><span><strong>{{ $name }}</strong><small>{{ $hint }}</small></span></a></li>
@endforeach
</ul>
</section>
</div>

<aside class="dash-side">
<section class="card" aria-labelledby="home-progress-title">
<p class="label">Progress</p>
<h2 id="home-progress-title">{{ $role === 'student' ? 'My academic progress' : 'Published results' }}</h2>
<p class="big-number">{{ $percent }}%</p>
<progress value="{{ $percent }}" max="100" aria-label="Percentage of assigned subject results published">{{ $percent }}%</progress>
<p class="muted">{{ $published }} of {{ $total }} subject results published</p>
<a href="{{ $role === 'coordinator' ? route($role.'.module','reports') : route($role.'.grades') }}">{{ $role === 'coordinator' ? 'View student progress reports' : 'View grades and feedback' }}</a>
</section>
<section class="card" aria-labelledby="upcoming-title">
<p class="label">Schedule</p>
<h2 id="upcoming-title">Next on the schedule</h2>
@if($upcoming)<p class="next-class"><span class="icon-chip">@include('workspace.icon',['name'=>'calendar'])</span><span><strong>{{ $upcoming->title }}</strong><small><time datetime="{{ $upcoming->scheduled_on }}">{{ \Illuminate\Support\Carbon::parse($upcoming->scheduled_on)->format('l, M j, Y') }}</time></small></span></p>
@else <p class="muted">No upcoming classes are scheduled.</p> @endif
<a href="{{ route($role.'.module','schedule') }}">View class schedule</a>
</section>
</aside>
</div>
@endsection
