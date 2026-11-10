@extends('layout')
@section('title','Access restricted')
@section('content')
<section class="panel detail"><h1>Access restricted</h1><p>This page belongs to a different workspace. Return to the workspace for your account.</p><a class="button" href="{{ route('home') }}">Go to my workspace</a></section>
@endsection

