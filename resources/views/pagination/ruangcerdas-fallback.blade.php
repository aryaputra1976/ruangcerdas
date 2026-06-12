@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:14px;margin-top:10px;padding-top:4px;">
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:6px;font-size:13px;color:#64748b;line-height:1.6;">
            <span>Menampilkan</span>
            <span style="display:inline-flex;align-items:center;justify-content:center;padding:4px 10px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-weight:700;">{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }}</span>
            <span>dari</span>
            <span style="font-weight:800;color:#0f172a;">{{ $paginator->total() }}</span>
            <span>hasil</span>
        </div>

        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="@lang('pagination.previous')" style="display:inline-flex;height:40px;align-items:center;justify-content:center;padding:0 14px;border:1px solid #e2e8f0;border-radius:999px;background:#f8fafc;color:#94a3b8;font-size:13px;font-weight:700;cursor:not-allowed;">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" style="display:inline-flex;height:40px;align-items:center;justify-content:center;padding:0 14px;border:1px solid #cbd5e1;border-radius:999px;background:#fff;color:#334155;font-size:13px;font-weight:700;text-decoration:none;box-shadow:0 1px 2px rgba(15, 23, 42, 0.04);">
                    Sebelumnya
                </a>
            @endif

            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-disabled="true" style="display:inline-flex;min-width:34px;height:34px;align-items:center;justify-content:center;padding:0 6px;color:#94a3b8;font-size:13px;font-weight:700;">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" style="display:inline-flex;min-width:38px;height:38px;align-items:center;justify-content:center;padding:0 12px;border:1px solid #2563eb;border-radius:999px;background:#2563eb;color:#fff;font-size:13px;font-weight:800;box-shadow:0 8px 20px rgba(37, 99, 235, 0.22);">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" aria-label="Go to page {{ $page }}" style="display:inline-flex;min-width:38px;height:38px;align-items:center;justify-content:center;padding:0 12px;border:1px solid #cbd5e1;border-radius:999px;background:#fff;color:#334155;font-size:13px;font-weight:700;text-decoration:none;box-shadow:0 1px 2px rgba(15, 23, 42, 0.04);">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')" style="display:inline-flex;height:40px;align-items:center;justify-content:center;padding:0 14px;border:1px solid #cbd5e1;border-radius:999px;background:#fff;color:#334155;font-size:13px;font-weight:700;text-decoration:none;box-shadow:0 1px 2px rgba(15, 23, 42, 0.04);">
                    Berikutnya
                </a>
            @else
                <span aria-disabled="true" aria-label="@lang('pagination.next')" style="display:inline-flex;height:40px;align-items:center;justify-content:center;padding:0 14px;border:1px solid #e2e8f0;border-radius:999px;background:#f8fafc;color:#94a3b8;font-size:13px;font-weight:700;cursor:not-allowed;">
                    Berikutnya
                </span>
            @endif
        </div>
    </nav>
@endif
