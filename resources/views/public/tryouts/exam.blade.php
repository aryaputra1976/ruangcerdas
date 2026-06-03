@extends('layouts.public')

@section('title', 'Ujian Tryout CPNS - Ruang Cerdas')
@section('meta_description', 'Halaman ujian tryout CPNS Ruang Cerdas.')
@section('robots', 'noindex,nofollow')

@section('content')
<section class="bg-slate-100 py-8 md:py-10">
    <div class="mx-auto max-w-7xl px-4 md:px-6">
        <div class="mb-6 rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3">
                <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Sesi Ujian</p>
                <h1 class="text-2xl font-black text-slate-950 md:text-3xl">
                    {{ $tryoutSession->package?->title ?? 'Tryout CPNS' }}
                </h1>
                <p class="text-sm text-slate-600">
                    Peserta:
                    <span class="font-semibold text-slate-900">
                        {{ $tryoutSession->participant_name }}
                    </span>
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div id="autosave-status" class="mb-6 hidden rounded-2xl border px-4 py-3 text-sm"></div>

        {{-- Mobile Timer + Progress Compact --}}
        <div class="mb-6 lg:hidden">
            <div class="rounded-[1.5rem] bg-slate-950 px-5 py-4 text-white shadow-lg">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-widest text-slate-300">
                            Sisa Waktu
                        </div>
                        <div id="exam-timer-mobile" class="mt-2 text-3xl font-black tabular-nums">
                            Memuat...
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="text-xs font-bold uppercase tracking-widest text-slate-400">
                            Progress
                        </div>
                        <div id="progress-text-mobile" class="mt-2 text-lg font-black text-blue-300">
                            0 / {{ $answers->count() }}
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="h-1.5 overflow-hidden rounded-full bg-slate-800">
                        <div id="progress-bar-mobile"
                             class="h-full rounded-full bg-blue-500 transition-all duration-300"
                             style="width: 0%;"></div>
                    </div>

                    <div class="mt-1 text-[11px] text-slate-400">
                        <span id="progress-percent-mobile">0%</span> selesai
                    </div>
                </div>
            </div>
        </div>

        <form id="exam-form"
              method="POST"
              action="{{ route('public.tryout-sessions.save', $tryoutSession) }}"
              class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_300px]">
            @csrf

            {{-- Soal --}}
            <div class="space-y-4">
                @foreach ($answers as $index => $answer)
                    @php
                        $question = $answer->question;
                    @endphp

                    <article id="question-{{ $index + 1 }}"
                             data-question-card
                             data-question-index="{{ $index }}"
                             data-answer-id="{{ $answer->id }}"
                             class="scroll-mt-24 rounded-[1.5rem] border border-slate-200 bg-white p-4 shadow-sm md:p-5">
                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-blue-700">
                                    Soal {{ $index + 1 }} • {{ $question?->section }}
                                </div>

                                <h2 class="mt-3 text-lg font-bold leading-7 text-slate-950">
                                    {!! nl2br(e($question?->question_text)) !!}
                                </h2>
                            </div>

                            <label class="inline-flex shrink-0 items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3.5 py-2 text-sm font-semibold text-amber-700">
                                <input type="checkbox"
                                       name="marked[{{ $answer->id }}]"
                                       value="1"
                                       data-marked-input
                                       data-answer-id="{{ $answer->id }}"
                                       class="rounded border-amber-300 text-amber-500 focus:ring-amber-400"
                                       @checked($answer->is_marked)>
                                Ragu-ragu
                            </label>
                        </div>

                        <div class="mt-4 space-y-2">
                            @foreach ($question?->options ?? [] as $option)
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 px-3.5 py-2.5 transition hover:border-blue-300 hover:bg-blue-50/50">
                                    <input type="radio"
                                           name="answers[{{ $answer->id }}]"
                                           value="{{ $option->id }}"
                                           data-answer-input
                                           data-answer-id="{{ $answer->id }}"
                                           data-question-index="{{ $index }}"
                                           class="mt-1 shrink-0 border-slate-300 text-blue-600 focus:ring-blue-500"
                                           @checked((int) $answer->question_option_id === (int) $option->id)>

                                    <div class="flex min-w-0 items-start gap-2 text-sm leading-6 text-slate-800">
                                        <span class="min-w-6 font-bold text-slate-950">
                                            {{ $option->option_label }}.
                                        </span>
                                        <span>
                                            {!! nl2br(e($option->option_text)) !!}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
                {{-- Desktop Timer + Progress Compact --}}
                <div class="hidden rounded-[1.5rem] bg-slate-950 px-5 py-4 text-white shadow-lg lg:block">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-widest text-slate-300">
                                Sisa Waktu
                            </div>
                            <div id="exam-timer" class="mt-2 text-3xl font-black tabular-nums">
                                Memuat...
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-xs font-bold uppercase tracking-widest text-slate-400">
                                Progress
                            </div>
                            <div id="progress-text" class="mt-2 text-lg font-black text-blue-300">
                                0 / {{ $answers->count() }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="h-1.5 overflow-hidden rounded-full bg-slate-800">
                            <div id="progress-bar"
                                 class="h-full rounded-full bg-blue-500 transition-all duration-300"
                                 style="width: 0%;"></div>
                        </div>

                        <div class="mt-1 text-[11px] text-slate-400">
                            <span id="progress-percent">0%</span> selesai
                        </div>
                    </div>
                </div>

                {{-- Navigasi Soal --}}
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-black text-slate-950">Navigasi Soal</h2>

                    <div class="mt-4 grid grid-cols-5 gap-2">
                        @foreach ($answers as $index => $answer)
                            @php
                                $isAnswered = filled($answer->question_option_id);
                                $isMarked = (bool) $answer->is_marked;

                                $navClass = $isMarked
                                    ? 'border-amber-200 bg-amber-500 text-white'
                                    : ($isAnswered
                                        ? 'border-emerald-200 bg-emerald-500 text-white'
                                        : 'border-red-200 bg-red-500 text-white');
                            @endphp

                            <a href="#question-{{ $index + 1 }}"
                               data-nav-item
                               data-answer-id="{{ $answer->id }}"
                               class="inline-flex h-10 items-center justify-center rounded-xl border text-sm font-bold transition {{ $navClass }}">
                                {{ $index + 1 }}
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-2 text-[11px] text-slate-500">
                        <div class="flex items-center gap-1.5">
                            <span class="inline-block h-2.5 w-2.5 shrink-0 rounded-full bg-red-500"></span>
                            <span>Belum</span>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <span class="inline-block h-2.5 w-2.5 shrink-0 rounded-full bg-emerald-500"></span>
                            <span>Sudah</span>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <span class="inline-block h-2.5 w-2.5 shrink-0 rounded-full bg-amber-500"></span>
                            <span>Ragu</span>
                        </div>
                    </div>
                </div>

                {{-- Ringkasan + Submit --}}
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="space-y-3 text-sm text-slate-600">
                        <div class="flex items-center justify-between">
                            <span>Total soal</span>
                            <span id="summary-total" class="font-black text-slate-950">
                                {{ $answers->count() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Sudah dijawab</span>
                            <span id="summary-answered" class="font-black text-slate-950">
                                {{ $answers->whereNotNull('question_option_id')->count() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Ragu-ragu</span>
                            <span id="summary-marked" class="font-black text-slate-950">
                                {{ $answers->where('is_marked', true)->count() }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-xs leading-6 text-blue-900">
                        Jawaban tersimpan otomatis saat dipilih.
                    </div>

                    <div class="mt-5 grid gap-3">
                        <button type="submit"
                                class="rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-blue-300 hover:text-blue-700">
                            Simpan Manual
                        </button>

                        <button id="submit-finish-button"
                                type="submit"
                                formaction="{{ route('public.tryout-sessions.submit', $tryoutSession) }}"
                                class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                            Submit Selesai
                        </button>
                    </div>
                </div>
            </aside>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timerElements = [
            document.getElementById('exam-timer'),
            document.getElementById('exam-timer-mobile'),
        ].filter(Boolean);

        const form = document.getElementById('exam-form');
        const autosaveStatus = document.getElementById('autosave-status');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const saveUrl = @json(route('public.tryout-sessions.save', $tryoutSession));
        const submitUrl = @json(route('public.tryout-sessions.submit', $tryoutSession));

        /**
         * Aman untuk timestamp detik atau milidetik.
         * - PHP timestamp biasa: 10 digit detik
         * - getTimestampMs(): 13 digit milidetik
         */
        const normalizeTimestamp = (value) => {
            const number = Number(value);

            if (!Number.isFinite(number) || number <= 0) {
                return 0;
            }

            return number < 1000000000000 ? number * 1000 : number;
        };

        const startTimestamp = normalizeTimestamp(@json($startTimestamp ?? 0));
        const endTimestamp = normalizeTimestamp(@json($endTimestamp ?? 0));
        const serverNowTimestamp = normalizeTimestamp(@json($serverNowTimestamp ?? 0));

        let autosaving = false;
        let queuedAutosave = false;
        let statusTimer = null;
        let autoNextTimer = null;
        let forcedSubmit = false;
        let timerInterval = null;

        if (!form || timerElements.length === 0) {
            return;
        }

        const hasValidTimestamps =
            Number.isFinite(startTimestamp) &&
            Number.isFinite(endTimestamp) &&
            Number.isFinite(serverNowTimestamp) &&
            startTimestamp > 0 &&
            endTimestamp > 0 &&
            serverNowTimestamp > 0 &&
            endTimestamp > startTimestamp;

        const clientServerOffset = hasValidTimestamps
            ? serverNowTimestamp - Date.now()
            : 0;

        const setTimerText = (value) => {
            timerElements.forEach((element) => {
                element.textContent = value;
            });
        };

        const showStatus = (message, type = 'info', persist = false) => {
            if (!autosaveStatus) {
                return;
            }

            const variants = {
                info: 'border-slate-200 bg-white text-slate-700',
                success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
                error: 'border-red-200 bg-red-50 text-red-700',
                warning: 'border-amber-200 bg-amber-50 text-amber-700',
            };

            autosaveStatus.className = `mb-6 rounded-2xl border px-4 py-3 text-sm ${variants[type] || variants.info}`;
            autosaveStatus.textContent = message;
            autosaveStatus.classList.remove('hidden');

            clearTimeout(statusTimer);

            if (!persist) {
                statusTimer = setTimeout(() => {
                    autosaveStatus.classList.add('hidden');
                }, 1800);
            }
        };

        const getAnsweredCount = () => {
            return document.querySelectorAll('[data-answer-input]:checked').length;
        };

        const getMarkedCount = () => {
            return document.querySelectorAll('[data-marked-input]:checked').length;
        };

        const getTotalCount = () => {
            return document.querySelectorAll('[data-nav-item]').length;
        };

        const setText = (id, value) => {
            const element = document.getElementById(id);

            if (element) {
                element.textContent = value;
            }
        };

        const setWidth = (id, value) => {
            const element = document.getElementById(id);

            if (element) {
                element.style.width = value;
            }
        };

        const updateProgress = () => {
            const total = getTotalCount();
            const answered = getAnsweredCount();
            const marked = getMarkedCount();
            const percent = total > 0 ? Math.round((answered / total) * 100) : 0;

            setText('summary-total', total);
            setText('summary-answered', answered);
            setText('summary-marked', marked);

            setText('progress-text', `${answered} / ${total}`);
            setText('progress-percent', `${percent}%`);
            setText('progress-text-mobile', `${answered} / ${total}`);
            setText('progress-percent-mobile', `${percent}%`);

            setWidth('progress-bar', `${percent}%`);
            setWidth('progress-bar-mobile', `${percent}%`);
        };

        const updateNavState = (answerId) => {
            const navItem = document.querySelector(`[data-nav-item][data-answer-id="${answerId}"]`);
            const checked = document.querySelector(`[data-answer-input][data-answer-id="${answerId}"]:checked`);
            const marked = document.querySelector(`[data-marked-input][data-answer-id="${answerId}"]`)?.checked;

            if (!navItem) {
                return;
            }

            navItem.classList.remove(
                'border-red-200',
                'bg-red-500',
                'border-emerald-200',
                'bg-emerald-500',
                'border-amber-200',
                'bg-amber-500',
                'text-white'
            );

            if (marked) {
                navItem.classList.add('border-amber-200', 'bg-amber-500', 'text-white');
            } else if (checked) {
                navItem.classList.add('border-emerald-200', 'bg-emerald-500', 'text-white');
            } else {
                navItem.classList.add('border-red-200', 'bg-red-500', 'text-white');
            }
        };

        const updateAllNavState = () => {
            document.querySelectorAll('[data-nav-item]').forEach((item) => {
                updateNavState(item.dataset.answerId);
            });

            updateProgress();
        };

        const buildPayload = () => {
            const formData = new FormData(form);

            return new URLSearchParams(formData);
        };

        const autosave = async () => {
            if (autosaving) {
                queuedAutosave = true;
                return;
            }

            autosaving = true;

            try {
                const response = await fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    },
                    body: buildPayload().toString(),
                });

                let data = {};

                try {
                    data = await response.json();
                } catch (error) {
                    data = {};
                }

                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                if (!response.ok) {
                    showStatus(data.message || 'Auto-save gagal. Coba lagi.', 'error');
                    return;
                }

                updateAllNavState();
                showStatus(data.message || 'Jawaban berhasil disimpan.', 'success');
            } catch (error) {
                showStatus('Auto-save gagal. Coba lagi.', 'error');
            } finally {
                autosaving = false;

                if (queuedAutosave) {
                    queuedAutosave = false;
                    autosave();
                }
            }
        };

        const scrollToNextQuestion = (currentIndex) => {
            const nextQuestion = document.querySelector(
                `[data-question-card][data-question-index="${currentIndex + 1}"]`
            );

            if (!nextQuestion) {
                return;
            }

            nextQuestion.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
            });
        };

        const formatDuration = (milliseconds) => {
            const totalSeconds = Math.max(0, Math.floor(milliseconds / 1000));
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            if (hours > 0) {
                return [hours, minutes, seconds]
                    .map((value) => String(value).padStart(2, '0'))
                    .join(':');
            }

            return [minutes, seconds]
                .map((value) => String(value).padStart(2, '0'))
                .join(':');
        };

        const forceSubmitBecauseTimeExpired = () => {
            if (forcedSubmit) {
                return;
            }

            forcedSubmit = true;
            showStatus('Waktu habis, jawaban sedang dikirim...', 'warning', true);

            if (timerInterval) {
                clearInterval(timerInterval);
            }

            form.action = submitUrl;
            form.submit();
        };

        const renderTime = () => {
            if (!hasValidTimestamps) {
                setTimerText('00:00');
                showStatus('Timer tidak dapat membaca waktu sesi. Silakan refresh halaman.', 'warning', true);
                return;
            }

            const effectiveNow = Date.now() + clientServerOffset;
            const remaining = endTimestamp - effectiveNow;

            if (remaining <= 0) {
                setTimerText('00:00');
                forceSubmitBecauseTimeExpired();
                return;
            }

            setTimerText(formatDuration(remaining));
        };

        document.querySelectorAll('[data-answer-input]').forEach((input) => {
            input.addEventListener('change', function () {
                const answerId = this.dataset.answerId;
                const questionIndex = Number(this.dataset.questionIndex || 0);

                updateNavState(answerId);
                updateProgress();
                autosave();

                clearTimeout(autoNextTimer);

                autoNextTimer = setTimeout(() => {
                    scrollToNextQuestion(questionIndex);
                }, 300);
            });
        });

        document.querySelectorAll('[data-marked-input]').forEach((input) => {
            input.addEventListener('change', function () {
                updateNavState(this.dataset.answerId);
                updateProgress();
                autosave();
            });
        });

        form.addEventListener('submit', function (event) {
            const submitter = event.submitter;

            if (!submitter || forcedSubmit) {
                return;
            }

            const isFinishButton = submitter.getAttribute('formaction') === submitUrl;

            if (!isFinishButton) {
                return;
            }

            const unanswered = getTotalCount() - getAnsweredCount();
            const message = unanswered > 0
                ? `Masih ada ${unanswered} soal belum dijawab. Yakin ingin menyelesaikan tryout?`
                : 'Yakin ingin menyelesaikan tryout?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });

        updateAllNavState();
        renderTime();

        timerInterval = setInterval(renderTime, 1000);
    });
</script>
@endpush