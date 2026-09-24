@if($paginator->hasPages())
    <nav class="mini-pagination" aria-label="Pagination {{ $label }}">
        <span>{{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }} dari {{ $paginator->total() }}</span>
        <div class="mini-pagination-links">
            @if($paginator->onFirstPage())<span class="disabled">←</span>@else<a href="{{ $paginator->previousPageUrl() }}" aria-label="{{ $label }} sebelumnya">←</a>@endif
            @foreach($paginator->getUrlRange(max(1, $paginator->currentPage() - 1), min($paginator->lastPage(), $paginator->currentPage() + 1)) as $page => $url)
                @if($page === $paginator->currentPage())<span class="active">{{ $page }}</span>@else<a href="{{ $url }}">{{ $page }}</a>@endif
            @endforeach
            @if($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" aria-label="{{ $label }} berikutnya">→</a>@else<span class="disabled">→</span>@endif
        </div>
    </nav>
@endif
