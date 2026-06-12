@extends('layouts.public')

@section('title', 'Ruang Akses - ' . $matchedInvoice)
@section('robots', 'noindex,nofollow')

@section('content')
<section class="bg-slate-50 pt-3 pb-6 md:pt-4 md:pb-8">
    <div class="mx-auto max-w-7xl px-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm md:p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Ruang Akses</p>
                    <h1 class="mt-2 text-2xl font-black text-slate-950 md:text-3xl">Semua pembelian dalam satu halaman</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Email akses: <span class="font-semibold text-slate-900 break-all">{{ $buyerEmail }}</span>
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="inline-flex rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700">
                        Total: <span class="ml-1 font-black text-slate-950">{{ count($orders) }} order</span>
                    </div>
                    <a href="{{ route('public.download-room.index') }}" class="rc-btn-neutral px-4 py-2.5 text-sm font-semibold">
                        Cek Email Lain
                    </a>
                </div>
            </div>

            <div class="mt-5 space-y-3 md:hidden">
                @foreach ($orders as $item)
                    @php
                        /** @var \App\Models\Order $listedOrder */
                        $listedOrder = $item['order'];
                    @endphp
                    <div class="rounded-[1.75rem] border {{ $item['matched_invoice'] ? 'border-blue-200 bg-blue-50/60' : 'border-slate-200 bg-white' }} p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-bold text-slate-950">{{ $listedOrder->invoice_number }}</div>
                                <div class="mt-1 text-xs text-slate-500">{{ $listedOrder->created_at?->format('d M Y H:i') ?? '-' }}</div>
                            </div>
                            @if ($item['matched_invoice'])
                                <span class="shrink-0 rounded-full bg-blue-100 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-blue-700">
                                    Dicari
                                </span>
                            @endif
                        </div>

                        <div class="mt-3">
                            <div class="font-semibold leading-6 text-slate-950">{{ $listedOrder->product?->name ?? '-' }}</div>
                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                <span class="inline-flex rounded-full {{ $item['is_tryout'] ? 'bg-violet-100 text-violet-700' : 'bg-slate-100 text-slate-700' }} px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide">
                                    {{ $item['type_label'] }}
                                </span>
                                <span class="text-sm font-semibold text-slate-700">{{ \App\Support\Money::rupiah($listedOrder->price) }}</span>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase {{ $item['status_class'] }}">
                                {{ $item['status_label'] }}
                            </span>
                            <span class="text-xs text-slate-500">
                                {{ $item['is_tryout'] ? 'Akses tryout' : 'Akses file digital' }}
                            </span>
                        </div>

                        <div class="mt-3 space-y-2">
                            @if ($item['action_label'] && $item['action_url'])
                                <a href="{{ $item['action_url'] }}" class="{{ $item['action_class'] }} w-full px-4 py-2.5 text-sm">
                                    {{ $item['action_label'] }}
                                </a>
                            @endif
                            @if ($item['action_note'])
                                <p class="text-xs leading-5 text-slate-500">{{ $item['action_note'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 hidden overflow-hidden rounded-[2rem] border border-slate-200 bg-white md:block">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-bold uppercase tracking-widest text-slate-500">
                                <th class="px-4 py-3.5">Invoice</th>
                                <th class="px-4 py-3.5">Produk</th>
                                <th class="px-4 py-3.5">Status</th>
                                <th class="px-4 py-3.5">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($orders as $item)
                                @php
                                    /** @var \App\Models\Order $listedOrder */
                                    $listedOrder = $item['order'];
                                @endphp
                                <tr class="{{ $item['matched_invoice'] ? 'bg-blue-50/60' : 'bg-white' }}">
                                    <td class="px-4 py-3.5 align-top">
                                        <div class="font-bold text-slate-950">{{ $listedOrder->invoice_number }}</div>
                                        <div class="mt-1 text-xs text-slate-500">{{ $listedOrder->created_at?->format('d M Y H:i') ?? '-' }}</div>
                                        @if ($item['matched_invoice'])
                                            <span class="mt-2 inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-blue-700">
                                                Invoice yang dicari
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 align-top">
                                        <div class="font-semibold text-slate-950">{{ $listedOrder->product?->name ?? '-' }}</div>
                                        <div class="mt-1 flex flex-wrap items-center gap-2">
                                            <span class="inline-flex rounded-full {{ $item['is_tryout'] ? 'bg-violet-100 text-violet-700' : 'bg-slate-100 text-slate-700' }} px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide">
                                                {{ $item['type_label'] }}
                                            </span>
                                            <span class="text-sm text-slate-500">{{ \App\Support\Money::rupiah($listedOrder->price) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 align-top">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase {{ $item['status_class'] }}">
                                            {{ $item['status_label'] }}
                                        </span>
                                        <div class="mt-2 text-xs leading-5 text-slate-500">
                                            @if ($item['is_tryout'])
                                                Akses lewat halaman tryout
                                            @else
                                                File digital dibuka dari halaman ini
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 align-top">
                                        <div class="flex min-w-[210px] flex-col gap-2">
                                            @if ($item['action_label'] && $item['action_url'])
                                                <a href="{{ $item['action_url'] }}" class="{{ $item['action_class'] }} px-4 py-2.5 text-sm">
                                                    {{ $item['action_label'] }}
                                                </a>
                                            @endif
                                            @if ($item['action_note'])
                                                <p class="text-xs leading-5 text-slate-500">{{ $item['action_note'] }}</p>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 text-sm leading-6 text-slate-600">
                Gunakan tombol <span class="font-semibold text-slate-900">Download</span> untuk produk digital dan <span class="font-semibold text-slate-900">Mulai Tryout</span> untuk akses tryout. Jika status masih pending atau ditolak, lanjutkan lewat tombol <span class="font-semibold text-slate-900">Upload Bukti</span>.
            </div>
        </div>
    </div>
</section>
@endsection
