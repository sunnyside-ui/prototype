@php
    $parent = collect(config('navigation.'.$role))->first(fn ($group) => isset($group[2][$module]));
@endphp
<nav class="crumbs" aria-label="Breadcrumb"><a href="{{ route($role.'.index') }}">Dashboard</a>@if($parent) <span aria-hidden="true">/</span> <span>{{ $parent[0] }}</span>@endif</nav>
