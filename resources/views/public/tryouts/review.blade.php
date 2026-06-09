@extends('layouts.public')

@section('title', 'Pembahasan ' . ($tryoutSession->package?->tryout_type_label ?? 'Tryout') . ' - Ruang Cerdas')
@section('meta_description', 'Pembahasan hasil tryout Ruang Cerdas.')
@section('robots', 'noindex,nofollow')

@section('content')
<section class="bg-slate-50 py-14 md:py-16">
    <div class="mx-auto max-w-6xl px-6">
        <div class="mb-8 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Pembahasan</p>
            <h1 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">{{ $tryoutSession->participant_name }}</h1>
            <p class="mt-2 text-slate-600">{{ $tryoutSession->package?->title ?? 'Tryout' }}</p>
            <div class="mt-5">
                <a href="{{ route('public.tryout-sessions.result', $tryoutSession) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-300 hover:text-blue-700">
                    Kembali ke Hasil
                </a>
            </div>
        </div>

        <div class="space-y-5">
            @foreach ($answers as $index => $answer)
                @php
                    $question = $answer->question;
                    $selectedOptionId = (int) $answer->question_option_id;
                    $isWeighted = $question?->usesWeightedScoring() ?? false;
                @endphp
                <article class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm md:p-6">
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div>
                            <div class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-blue-700">
                                Soal {{ $index + 1 }} · {{ $question?->section_label }}
                            </div>
                            <h2 class="mt-4 text-lg font-bold leading-8 text-slate-950">{!! nl2br(e($question?->question_text)) !!}</h2>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700">
                            Skor: {{ $answer->score }}
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        @foreach ($question?->options ?? [] as $option)
                            @php
                                $isSelected = $selectedOptionId === (int) $option->id;
                                $isCorrect = (bool) $option->is_correct;
                            @endphp
                            <div class="rounded-2xl border px-4 py-4 {{ $isSelected ? 'border-blue-300 bg-blue-50' : 'border-slate-200 bg-white' }}">
                                <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                                    <div>
                                        <div class="text-sm font-bold text-slate-900">{{ $option->option_label }}.</div>
                                        <div class="mt-1 text-sm leading-7 text-slate-700">{!! nl2br(e($option->option_text)) !!}</div>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @if ($isSelected)
                                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">Jawaban Peserta</span>
                                        @endif
                                        @if (! $isWeighted && $isCorrect)
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Jawaban Benar</span>
                                        @endif
                                        @if ($isWeighted)
                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">Skor {{ $option->score }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <div class="text-sm font-bold uppercase tracking-widest text-slate-500">Pembahasan</div>
                        <div class="mt-2 text-sm leading-7 text-slate-700">
                            {{ $question?->explanation ?: 'Pembahasan belum tersedia untuk soal ini.' }}
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
