@if($paginator->hasPages())
<nav class="pager" aria-label="Record pages">
@if($paginator->previousPageUrl()) <a class="button secondary" href="{{ $paginator->previousPageUrl() }}">Previous</a> @endif
<span>Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>
@if($paginator->nextPageUrl()) <a class="button secondary" href="{{ $paginator->nextPageUrl() }}">Next</a> @endif
</nav>
@endif

