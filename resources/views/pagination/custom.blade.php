@if ($paginator->hasPages() || $paginator->total() > 0)
    <nav role="navigation" aria-label="Navigasi Halaman" class="pagination-nav" style="display: flex; gap: 0.35rem; align-items: center;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="page-link disabled" style="padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); color: #94a3b8; background: #f8fafc; font-size: 0.85rem; cursor: not-allowed;">
                &laquo; Sebelumnya
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev" style="padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); color: #334155; background: #ffffff; text-decoration: none; font-size: 0.85rem;">
                &laquo; Sebelumnya
            </a>
        @endif

        {{-- Page Numbers --}}
        @for ($page = 1; $page <= max(1, $paginator->lastPage()); $page++)
            @if ($page == $paginator->currentPage())
                <a href="{{ $paginator->url($page) }}" class="page-link active" style="min-width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #2563eb; border-radius: var(--radius-sm); background-color: #2563eb; color: #ffffff; font-weight: 700; text-decoration: none; font-size: 0.85rem;">
                    {{ $page }}
                </a>
            @else
                <a href="{{ $paginator->url($page) }}" class="page-link" style="min-width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background-color: #ffffff; color: #334155; font-weight: 600; text-decoration: none; font-size: 0.85rem;">
                    {{ $page }}
                </a>
            @endif
        @endfor

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next" style="padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); color: #334155; background: #ffffff; text-decoration: none; font-size: 0.85rem;">
                Selanjutnya &raquo;
            </a>
        @else
            <span class="page-link disabled" style="padding: 0.5rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); color: #94a3b8; background: #f8fafc; font-size: 0.85rem; cursor: not-allowed;">
                Selanjutnya &raquo;
            </span>
        @endif
    </nav>
@endif

