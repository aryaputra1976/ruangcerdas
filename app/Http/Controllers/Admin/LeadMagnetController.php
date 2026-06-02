<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadMagnet;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LeadMagnetController extends Controller
{
    public function index()
    {
        $leadMagnets = LeadMagnet::query()->latest()->paginate(10)->withQueryString();

        return view('admin.lead-magnets.index', compact('leadMagnets'));
    }

    public function create()
    {
        return view('admin.lead-magnets.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateLeadMagnet($request);
        $validated['slug'] = $this->makeUniqueSlug($validated['slug'] ?: $validated['title']);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('lead-magnets/covers', 'public');
        }

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('lead-magnets/files', 'private');
        }

        $leadMagnet = LeadMagnet::create($validated);

        ActivityLogger::log('lead_magnet.created', $leadMagnet, 'Admin menambahkan lead magnet.', ['title' => $leadMagnet->title]);

        return redirect()->route('admin.lead-magnets.index')->with('success', 'Lead magnet berhasil ditambahkan.');
    }

    public function edit(LeadMagnet $leadMagnet)
    {
        return view('admin.lead-magnets.edit', compact('leadMagnet'));
    }

    public function update(Request $request, LeadMagnet $leadMagnet)
    {
        $validated = $this->validateLeadMagnet($request, $leadMagnet);
        $validated['slug'] = $this->makeUniqueSlug($validated['slug'] ?: $validated['title'], $leadMagnet->id);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('cover_image')) {
            if ($leadMagnet->cover_image) {
                Storage::disk('public')->delete($leadMagnet->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('lead-magnets/covers', 'public');
        }

        if ($request->hasFile('file')) {
            if ($leadMagnet->file_path) {
                Storage::disk('private')->delete($leadMagnet->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('lead-magnets/files', 'private');
        }

        $leadMagnet->update($validated);

        ActivityLogger::log('lead_magnet.updated', $leadMagnet, 'Admin memperbarui lead magnet.', ['title' => $leadMagnet->title]);

        return redirect()->route('admin.lead-magnets.edit', $leadMagnet)->with('success', 'Lead magnet berhasil diperbarui.');
    }

    public function destroy(LeadMagnet $leadMagnet)
    {
        if ($leadMagnet->cover_image) {
            Storage::disk('public')->delete($leadMagnet->cover_image);
        }

        if ($leadMagnet->file_path) {
            Storage::disk('private')->delete($leadMagnet->file_path);
        }

        $title = $leadMagnet->title;
        $leadMagnet->delete();

        ActivityLogger::log('lead_magnet.deleted', $leadMagnet, 'Admin menghapus lead magnet.', ['title' => $title]);

        return redirect()->route('admin.lead-magnets.index')->with('success', 'Lead magnet berhasil dihapus.');
    }

    private function validateLeadMagnet(Request $request, ?LeadMagnet $leadMagnet = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('lead_magnets', 'slug')->ignore($leadMagnet?->id)],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'file' => ['nullable', 'file', 'max:20480'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function makeUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        $slug = $baseSlug;
        $counter = 2;

        while (LeadMagnet::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
