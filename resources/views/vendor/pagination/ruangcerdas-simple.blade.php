@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:14px;margin-top:10px;padding-top:4px;">
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:6px;font-size:13px;color:#64748b;line-height:1.6;">
            <span>Halaman aktif</span>
            <span style="display:inline-flex;align-items:center;justify-content:center;padding:4px 10px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-weight:700;">{{ $paginator->currentPage() }}</span>
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
