@extends('layout')
@section('title',$tracker->name)
@section('content')
@php
$term = request('term');
$selected = is_string($term) && isset($terms[$term]) ? $term : array_key_first($terms);
$period = $periods[$selected] ?? [];
$rows = old('rows', $period['rows'] ?? []);
@endphp
<a class="back tracker-screen" href="{{ route('coordinator.trackers.index') }}">← Academic trackers</a>
<header class="page-heading"><p class="eyebrow">Academic tracker</p><h1>{{ $tracker->name }}</h1><p>{{ $tracker->student->name }}</p></header>
<div class="tracker-screen task-actions">
<a class="button secondary" href="{{ route('coordinator.trackers.download',$tracker) }}">Download CSV</a>
<button class="secondary" type="button" onclick="window.print()">Print or save PDF</button>
</div>
@php([$currentYear, $currentSemester] = explode('-', $selected))
<div class="tracker-screen tracker-picker" id="tracker-navigation">
<nav class="tracker-picker-row" aria-label="Year level">
<span class="tracker-picker-label">Year level</span>
@foreach(['1'=>'First year','2'=>'Second year','3'=>'Third year','4'=>'Fourth year'] as $year=>$label)
@php($target = isset($terms[$year.'-'.$currentSemester]) ? $year.'-'.$currentSemester : $year.'-first')
<a href="{{ route('coordinator.trackers.show',[$tracker,'term'=>$target]) }}#tracker-navigation" @if((string) $year === $currentYear) aria-current="true" @endif>{{ $label }}</a>
@endforeach
</nav>
<nav class="tracker-picker-row" aria-label="Semester">
<span class="tracker-picker-label">Semester</span>
@foreach(['first'=>'First semester','second'=>'Second semester','summer'=>'Summer class'] as $semester=>$label)
@php($target = $currentYear.'-'.$semester)
@continue(!isset($terms[$target]))
<a class="{{ $semester === 'summer' ? 'tracker-summer' : '' }}" href="{{ route('coordinator.trackers.show',[$tracker,'term'=>$target]) }}#tracker-navigation" @if($target === $selected) aria-current="page" @endif>{{ $label }}</a>
@endforeach
</nav>
<small>Save your edits before switching terms.</small>
</div>
<section class="panel detail tracker-screen" id="tracker-term-panel">
<h2 class="paper-heading">{{ $terms[$selected] }}</h2>
<form method="post" action="{{ route('coordinator.trackers.update',[$tracker,$selected]) }}">
@csrf
<label>Academic year <input class="tracker-year" name="academic_year" value="{{ old('academic_year',$period['academic_year'] ?? '') }}" placeholder="2026-2027" maxlength="9"></label>
<p>Review the prefilled courses and tick the applicable boxes. Leave unused rows blank.
@if($selected === '1-summer') Verify the three course codes against the original. Their prefixes are cropped in the photo. @endif
@if($selected === '4-first') The paper leaves individual unit values blank for this term. @endif</p>
<div class="tracker-table" tabindex="0" role="region" aria-label="Editable courses">
<table class="paper-tracker">
<colgroup><col class="paper-code"><col class="paper-title"><col class="paper-units"><col span="4" class="paper-check"><col class="paper-term"></colgroup>
@include('trackers.table-header')
<tbody>@foreach(array_pad($rows, min(100,count($rows)+5), []) as $i=>$row)
<tr><td><input aria-label="Course code, row {{ $i+1 }}" name="rows[{{ $i }}][code]" maxlength="30" value="{{ $row['code'] ?? '' }}"></td>
<td><textarea aria-label="Descriptive title, row {{ $i+1 }}" name="rows[{{ $i }}][title]" maxlength="240" rows="2">{{ $row['title'] ?? '' }}</textarea></td>
<td><input type="number" min="0" max="99" aria-label="Units, row {{ $i+1 }}" name="rows[{{ $i }}][units]" value="{{ $row['units'] ?? '' }}"></td>
@foreach($checks as $key=>$label)
<td><input type="checkbox" aria-label="{{ $label }}, row {{ $i+1 }}" name="rows[{{ $i }}][{{ $key }}]" value="1" @checked(!empty($row[$key]))></td>
@endforeach<td><input aria-label="Semester and academic year, row {{ $i+1 }}" name="rows[{{ $i }}][planned_term]" maxlength="60" value="{{ $row['planned_term'] ?? '' }}"></td></tr>
@endforeach</tbody></table>
</div><button type="submit">Save term</button>
</form>
</section>
<div class="tracker-print">
@foreach($terms as $key=>$label)
@php($saved = $periods[$key] ?? [])
<section><h2>{{ $label }}</h2><p>Academic year {{ $saved['academic_year'] ?? '____________' }}</p>
<table class="paper-tracker">
<colgroup><col class="paper-code"><col class="paper-title"><col class="paper-units"><col span="4" class="paper-check"><col class="paper-term"></colgroup>
@include('trackers.table-header')
<tbody>@forelse($saved['rows'] ?? [] as $row)
<tr><td>{{ $row['code'] }}</td><td>{{ $row['title'] }}</td><td>{{ $row['units'] ?? '' }}</td>@foreach($checks as $field=>$check)<td>{{ empty($row[$field]) ? '☐' : '☑' }}</td>@endforeach<td>{{ $row['planned_term'] ?? '' }}</td></tr>
@empty <tr><td colspan="8">No courses recorded.</td></tr> @endforelse</tbody></table>
</section>
@endforeach
</div>
<script src="{{ asset('tracker-navigation.js') }}" defer></script>
@endsection








