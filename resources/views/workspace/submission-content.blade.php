@if($item->details)<p class="prose">{{ $item->details }}</p>@endif
@if($item->attachment_path)
<a class="button secondary" href="{{ route($role.'.work.attachment',$item->id) }}">Download {{ $item->attachment_name }}</a>
@endif
