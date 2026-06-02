<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LeadMagnet;
use App\Models\LeadSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeadMagnetController extends Controller
{
    public function index()
    {
        $leadMagnets = LeadMagnet::query()
            ->where('is_active', true)
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('public.lead-magnets.index', compact('leadMagnets'));
    }

    public function show(LeadMagnet $leadMagnet)
    {
        abort_unless($leadMagnet->is_active, 404);

        return view('public.lead-magnets.show', compact('leadMagnet'));
    }

    public function download(Request $request, LeadMagnet $leadMagnet)
    {
        abort_unless($leadMagnet->is_active, 404);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
        ]);

        if (blank($leadMagnet->file_path) || ! Storage::disk('private')->exists($leadMagnet->file_path)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'File gratis belum tersedia. Silakan hubungi admin.');
        }

        LeadSubscriber::create([
            'lead_magnet_id' => $leadMagnet->id,
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'downloaded_at' => now(),
        ]);

        $leadMagnet->increment('download_count');

        return Storage::disk('private')->download(
            $leadMagnet->file_path,
            basename($leadMagnet->file_path)
        );
    }
}
