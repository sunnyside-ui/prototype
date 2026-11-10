@php
$role = auth()->user()->role;
$module = request()->route('module');
$pageIcons = ['people'=>'users','subjects'=>'study','lessons'=>'study','schedule'=>'calendar','attendance'=>'circle-check','assignments'=>'clipboard','projects'=>'folder','submissions'=>'upload','grades'=>'results','competencies'=>'award','profile'=>'user','applications'=>'file','requirements'=>'circle-check','accreditation'=>'award','courses'=>'study','reports'=>'results'];
@endphp
<details class="menu" id="menu" open>
<summary class="menu-toggle button secondary">@include('workspace.icon',['name'=>'dashboard']) Menu</summary>
<nav class="workspace-nav" aria-label="Main navigation">
<a class="nav-link" href="{{ route($role.'.index') }}" @if(request()->routeIs($role.'.index')) aria-current="page" @endif>@include('workspace.icon',['name'=>'dashboard']) <span>Dashboard</span></a>
@foreach(config('navigation.'.$role) as $key => [$label, $description, $pages])
<p class="nav-heading">{{ $label }}</p>
@foreach($pages as $page => $pageLabel)
@php($current = $page === 'grades' ? request()->routeIs($role.'.grades', $role.'.show') : ($page === 'profile' ? request()->routeIs($role.'.profile') : $module === $page))
<a class="nav-link" href="{{ $page === 'grades' ? route($role.'.grades') : ($page === 'profile' ? route($role.'.profile') : route($role.'.module', $page)) }}" @if($current) aria-current="page" @endif>@include('workspace.icon',['name'=>$pageIcons[$page] ?? 'arrow']) <span>{{ $pageLabel }}</span></a>
@endforeach
@endforeach
<p class="nav-heading">Updates</p>
<a class="nav-link" href="{{ route($role.'.module','announcements') }}" @if($module === 'announcements') aria-current="page" @endif>@include('workspace.icon',['name'=>'announcements']) <span>Announcements</span></a>
<a class="nav-link" href="{{ route($role.'.module','notifications') }}" @if($module === 'notifications') aria-current="page" @endif>@include('workspace.icon',['name'=>'notifications']) <span>Notifications</span></a>
@if($role === 'coordinator')
<a class="nav-link" href="{{ route('coordinator.trackers.index') }}" @if(request()->routeIs('coordinator.trackers.*')) aria-current="location" @endif>@include('workspace.icon',['name'=>'results']) <span>Academic trackers</span></a>
@endif
</nav>
</details>


