@props(['paginator', 'label' => 'Data'])
<nav aria-label="Halaman {{ $label }}" style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:12px;margin-top:18px">
    <span class="muted">{{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }} dari {{ $paginator->total() }} data</span>
    @if($paginator->hasPages())
        <div class="actions">
            @if($paginator->previousPageUrl())<a class="btn small" href="{{ $paginator->previousPageUrl() }}" aria-label="Halaman sebelumnya">←</a>@endif
            @foreach($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $number => $url)
                @if($number === $paginator->currentPage())<span class="btn primary small" aria-current="page">{{ $number }}</span>@else<a class="btn small" href="{{ $url }}" aria-label="Halaman {{ $number }}">{{ $number }}</a>@endif
            @endforeach
            @if($paginator->nextPageUrl())<a class="btn small" href="{{ $paginator->nextPageUrl() }}" aria-label="Halaman berikutnya">→</a>@endif
        </div>
    @endif
</nav>
