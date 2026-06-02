@php
    $isEdit = isset($article);
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Judul <span class="text-danger">*</span></label>
        <input type="text" name="title" value="{{ old('title', $article->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $article->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror" placeholder="otomatis jika kosong">
        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label">Excerpt</label>
        <textarea name="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
        @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label">Content <span class="text-danger">*</span></label>
        <textarea name="content" rows="10" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $article->content ?? '') }}</textarea>
        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Cover Image</label>
        <input type="file" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/*">
        @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Published At</label>
        <input type="datetime-local" name="published_at" value="{{ old('published_at', isset($article) && $article->published_at ? $article->published_at->format('Y-m-d\\TH:i') : '') }}" class="form-control @error('published_at') is-invalid @enderror">
        @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" @checked(old('is_published', $article->is_published ?? false))>
            <label class="form-check-label" for="is_published">Published</label>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">SEO Title</label>
        <input type="text" name="seo_title" value="{{ old('seo_title', $article->seo_title ?? '') }}" class="form-control @error('seo_title') is-invalid @enderror">
        @error('seo_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">SEO Description</label>
        <textarea name="seo_description" rows="2" class="form-control @error('seo_description') is-invalid @enderror">{{ old('seo_description', $article->seo_description ?? '') }}</textarea>
        @error('seo_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Artikel' }}</button>
    <a href="{{ route('admin.articles.index') }}" class="btn bg-secondary-subtle text-secondary rounded-pill px-4">Batal</a>
</div>
