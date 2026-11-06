@if ($paginator->hasPages())
<div class="pagination">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <span style="color:#9ca3af;cursor:not-allowed;min-width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;">
            <i class="fas fa-chevron-left" style="font-size:12px;"></i>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="min-width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;color:var(--gray);text-decoration:none;transition:background 0.2s;" onmouseover="this.style.background='var(--green-pale)'" onmouseout="this.style.background=''">
            <i class="fas fa-chevron-left" style="font-size:12px;"></i>
        </a>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="color:#9ca3af;padding:0 4px;">...</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="min-width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;background:var(--green-main);color:white;font-weight:600;font-size:14px;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="min-width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;color:var(--gray);text-decoration:none;font-size:14px;transition:background 0.2s;" onmouseover="this.style.background='var(--green-pale)'" onmouseout="this.style.background=''">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="min-width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;color:var(--gray);text-decoration:none;transition:background 0.2s;" onmouseover="this.style.background='var(--green-pale)'" onmouseout="this.style.background=''">
            <i class="fas fa-chevron-right" style="font-size:12px;"></i>
        </a>
    @else
        <span style="color:#9ca3af;cursor:not-allowed;min-width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;">
            <i class="fas fa-chevron-right" style="font-size:12px;"></i>
        </span>
    @endif
</div>
@endif
