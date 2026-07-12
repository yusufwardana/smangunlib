@extends('layouts.app')

@section('title', ($item->exists ? 'Edit ' : 'Tambah ').$typeLabel)

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame { border-radius: .5rem; border-color: #dee2e6; }
    .note-editor .note-toolbar { background: #f8f9fa; border-bottom: 1px solid #dee2e6; }
    .note-editor .note-editable { min-height: 320px; font-size: .95rem; line-height: 1.7; }
    .note-editor .note-editable img { max-width: 100%; height: auto; }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">{{ $item->exists ? 'Edit' : 'Tambah' }} {{ $typeLabel }}</h4>
            <p class="text-muted mb-0">Gunakan editor untuk konten panjang seperti berita, sambutan, pengumuman, dan FAQ.</p>
        </div>
        <a href="{{ route('system.contents.index', $type) }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
    <form action="{{ $item->exists ? route('system.contents.update', $item) : route('system.contents.store') }}" method="POST" enctype="multipart/form-data" class="card">
        @csrf
        @if($item->exists) @method('PUT') @endif
        <div class="card-body">
            @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="row g-3">
                <div class="col-md-8"><label class="form-label">Judul</label><input name="title" class="form-control" value="{{ old('title', $item->title) }}"></div>
                <div class="col-md-4"><label class="form-label">Slug</label><input name="slug" class="form-control" value="{{ old('slug', $item->slug) }}"></div>
                <div class="col-md-6"><label class="form-label">Subjudul/Caption</label><input name="subtitle" class="form-control" value="{{ old('subtitle', $item->subtitle) }}"></div>
                <div class="col-md-3"><label class="form-label">Kategori</label><input name="category" class="form-control" value="{{ old('category', $item->category) }}"></div>
                <div class="col-md-3"><label class="form-label">Icon Bootstrap</label><input name="icon" class="form-control" placeholder="bi-book" value="{{ old('icon', $item->icon) }}"></div>
                <div class="col-12"><label class="form-label">Deskripsi Singkat</label><textarea name="description" class="form-control" rows="3">{{ old('description', $item->description) }}</textarea></div>
                <div class="col-12"><label class="form-label">Isi Konten</label><textarea name="body" class="form-control rich-editor" rows="8">{{ old('body', $item->body) }}</textarea></div>
                <div class="col-md-4"><label class="form-label">Gambar/Foto</label><input type="file" name="image" class="form-control">@if($item->image)<a target="_blank" class="small" href="{{ asset('storage/'.$item->image) }}">Lihat gambar</a>@endif</div>
                <div class="col-md-4"><label class="form-label">Lampiran</label><input type="file" name="attachment" class="form-control">@if($item->attachment)<a target="_blank" class="small" href="{{ asset('storage/'.$item->attachment) }}">Lihat lampiran</a>@endif</div>
                <div class="col-md-4"><label class="form-label">Video URL</label><input name="video_url" class="form-control" value="{{ old('video_url', $item->video_url) }}"></div>
                <div class="col-md-3"><label class="form-label">Button</label><input name="button_text" class="form-control" value="{{ old('button_text', $item->button_text) }}"></div>
                <div class="col-md-3"><label class="form-label">Link Button</label><input name="button_url" class="form-control" value="{{ old('button_url', $item->button_url) }}"></div>
                <div class="col-md-3"><label class="form-label">Penulis/Nama</label><input name="author" class="form-control" value="{{ old('author', $item->author) }}"></div>
                <div class="col-md-3"><label class="form-label">Tanggal</label><input type="date" name="content_date" class="form-control" value="{{ old('content_date', $item->content_date?->format('Y-m-d')) }}"></div>
                <div class="col-md-3"><label class="form-label">Publish At</label><input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', $item->published_at?->format('Y-m-d\\TH:i')) }}"></div>
                <div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['draft','active','inactive','published','archived'] as $status)<option value="{{ $status }}" @selected(old('status',$item->status)===$status)>{{ $status }}</option>@endforeach</select></div>
                <div class="col-md-3"><label class="form-label">Urutan</label><input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $item->sort_order ?? 0) }}"></div>
                <div class="col-md-3"><label class="form-label">SEO Title</label><input name="seo_title" class="form-control" value="{{ old('seo_title', $item->seo_title) }}"></div>
                <div class="col-12"><label class="form-label">SEO Description</label><textarea name="seo_description" class="form-control" rows="2">{{ old('seo_description', $item->seo_description) }}</textarea></div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-end gap-2">
            <button class="btn btn-primary">Simpan Konten</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
    $('.rich-editor').summernote({
        height: 360,
        placeholder: 'Tulis isi konten lengkap di sini... (mendukung heading, tabel, gambar, video, dsb.)',
        tabsize: 2,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video', 'hr']],
            ['view', ['fullscreen', 'codeview', 'help']],
        ],
        callbacks: {
            onImageUpload: function (files) {
                for (let i = 0; i < files.length; i++) {
                    uploadEditorImage(files[i], $(this));
                }
            }
        }
    });

    function uploadEditorImage(file, $editor) {
        const data = new FormData();
        data.append('image', file);

        $.ajax({
            url: "{{ route('system.contents.upload-image') }}",
            method: 'POST',
            data: data,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res && res.url) {
                    $editor.summernote('insertImage', res.url);
                }
            },
            error: function () {
                alert('Gagal mengunggah gambar. Pastikan file berupa gambar dan ukuran maksimal 4MB.');
            }
        });
    }
</script>
@endpush
