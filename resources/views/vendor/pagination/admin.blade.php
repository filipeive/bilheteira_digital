@if ($paginator->hasPages())
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
        <div style="font-size: 0.85rem; color: var(--text-muted);">
            A mostrar <strong style="color: var(--gold);">{{ $paginator->firstItem() }}</strong> a <strong style="color: var(--gold);">{{ $paginator->lastItem() }}</strong> de <strong style="color: var(--gold);">{{ $paginator->total() }}</strong>
        </div>

        <div style="display: flex; gap: 6px; align-items: center;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="btn-outline" style="opacity: 0.4; cursor: not-allowed; padding: 6px 10px;">&laquo;</span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" class="btn-outline" style="padding: 6px 10px;">&laquo;</button>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="btn-outline" style="opacity: 0.5; padding: 6px 10px;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="btn-gold" style="padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 0.86rem;">{{ $page }}</span>
                        @else
                            <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled" class="btn-outline" style="padding: 6px 12px;">{{ $page }}</button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled" class="btn-outline" style="padding: 6px 10px;">&raquo;</button>
            @else
                <span class="btn-outline" style="opacity: 0.4; cursor: not-allowed; padding: 6px 10px;">&raquo;</span>
            @endif
        </div>
    </div>
@endif
