@extends('layout')
@section('title', $title)
@section('content')
<section class="page-heading section-heading">
<span class="section-symbol">@include('workspace.icon',['name'=>$section])</span>
<div><p class="eyebrow">{{ $role === 'instructor' ? 'Teacher workspace' : ucfirst($role).' workspace' }}</p><h1>{{ $title }}</h1><p>{{ $description }}</p></div>
</section>
<section class="section-directory" aria-label="Pages in {{ $title }}">
<ul class="section-links">
@foreach($pages as $key => $label)
<li><a href="{{ $key === 'grades' ? route($role.'.grades') : route($role.'.module',$key) }}"><strong>{{ $label }}</strong><span class="section-arrow">@include('workspace.icon',['name'=>'arrow'])</span></a></li>
@endforeach
</ul>
</section>
@endsection
