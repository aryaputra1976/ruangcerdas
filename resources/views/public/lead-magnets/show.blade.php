@extends('layouts.public')

@section('title', $leadMagnet->title . ' - Panduan Gratis Ruang Cerdas')
@section('meta_description', $leadMagnet->description ?: 'Unduh panduan gratis dari Ruang Cerdas.')
@section('canonical', route('lead-magnets.show', $leadMagnet))
@if ($leadMagnet->cover_image)
    @section('og_image', asset('storage/' . $leadMagnet->cover_image))
@endif

@section('content')
<section class="bg-white py-14 md:py-16">
    <div class="mx-auto grid max-w-7xl gap-8 px-6 lg:grid-cols-2 lg:items-start">
        <div>
            @if ($leadMagnet->cover_image)
                <img src="{{ asset('storage/' . $leadMagnet->cover_image) }}" alt="{{ $leadMagnet->title }}" class="h-auto w-full rounded-3xl border border-slate-200 object-cover">
            @endif
        </div>

        <div>
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">Panduan Gratis</p>
            <h1 class="mt-3 text-4xl font-black text-slate-950">{{ $leadMagnet->title }}</h1>
            <p class="mt-4 text-slate-600 leading-8">{{ $leadMagnet->description ?: 'Isi data singkat untuk mengunduh panduan gratis ini.' }}</p>

            @if (session('error'))
                <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('lead-magnets.download', $leadMagnet) }}" class="mt-6 space-y-4 rounded-3xl border border-slate-200 bg-slate-50 p-5">
                @csrf

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Nama (opsional)</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">WhatsApp (opsional)</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-700">
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-6 py-4 text-base font-bold text-white hover:bg-blue-700">
                    Download Panduan Gratis
                </button>
            </form>
        </div>
    </div>
</section>
@endsection

