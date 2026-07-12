<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $news->seo_description ?? Str::limit(strip_tags($news->description ?? $news->body), 155) }}">
    <title>{{ $news->seo_title ?? $news->title }} - Berita Perpustakaan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #1e293b; }
        .article-hero { background: linear-gradient(135deg, #0f766e, #134e4a); color: #fff; padding: 90px 0 60px; }
        .article-hero .breadcrumb a { color: rgba(255,255,255,.85); text-decoration: none; }
        .article-hero h1 { font-weight: 800; font-size: clamp(1.8rem, 4vw, 2.8rem); }
        .article-meta { display: flex; flex-wrap: wrap; gap: 18px; color: rgba(255,255,255,.85); font-size: .95rem; margin-top: 12px; }
        .article-wrap { max-width: 820px; margin: -40px auto 0; background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(15,23,42,.08); padding: 40px; }
        .article-cover { width: 100%; border-radius: 16px; margin-bottom: 28px; object-fit: cover; max-height: 440px; }
        .article-body { font-size: 1.05rem; line-height: 1.85; color: #334155; }
        .article-body img { max-width: 100%; height: auto; border-radius: 12px; margin: 14px 0; }
        .article-body h1, .article-body h2, .article-body h3 { font-weight: 700; margin-top: 1.6rem; }
        .article-body table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .article-body table td, .article-body table th { border: 1px solid #e2e8f0; padding: 8px 12px; }
        .article-body blockquote { border-left: 4px solid #0f766e; padding-left: 16px; color: #475569; font-style: italic; }
        .related-card { display: block; text-decoration: none; color: inherit; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 24px rgba(15,23,42,.06); height: 100%; transition: transform .2s; }
        .related-card:hover { transform: translateY(-4px); }
        .related-card img { width: 100%; height: 160px; object-fit: cover; }
        .related-card .p { padding: 16px; }
    </style>
</head>
<body>

<section class="article-hero">
    <div class="container">
        <nav class="breadcrumb small mb-3">
            <a href="{{ route('landing') }}"><i class="bi bi-house-door me-1"></i>Beranda</a>
            <span class="mx-2 text-white-50">/</span>
            <a href="{{ route('landing') }}#berita">Berita</a>
        </nav>
        @if($news->category)
            <span class="badge rounded-pill bg-light text-dark mb-2">{{ $news->category }}</span>
        @endif
        <h1>{{ $news->title }}</h1>
        <div class="article-meta">
            <span><i class="bi bi-calendar3 me-1"></i>{{ optional($news->published_at ?? $news->content_date ?? $news->created_at)->format('d F Y') }}</span>
            @if($news->author)<span><i class="bi bi-person me-1"></i>{{ $news->author }}</span>@endif
        </div>
    </div>
</section>

<div class="container pb-5">
    <article class="article-wrap">
        @if($news->image)
            <img class="article-cover" src="{{ asset('storage/'.$news->image) }}" alt="{{ $news->title }}">
        @endif

        @if($news->subtitle)
            <p class="lead text-muted">{{ $news->subtitle }}</p>
        @endif

        <div class="article-body">
            @if($news->body)
                {!! $news->body !!}
            @else
                <p>{{ $news->description }}</p>
            @endif
        </div>

        @if($news->attachment)
            <hr class="my-4">
            <a href="{{ asset('storage/'.$news->attachment) }}" class="btn btn-outline-primary" download>
                <i class="bi bi-paperclip me-1"></i>Unduh Lampiran
            </a>
        @endif

        <hr class="my-4">
        <a href="{{ route('landing') }}#berita" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Kembali ke Berita</a>
    </article>

    @if($related->isNotEmpty())
        <div class="mt-5" style="max-width: 1000px; margin-inline: auto;">
            <h4 class="fw-bold mb-3">Berita Lainnya</h4>
            <div class="row g-4">
                @foreach($related as $item)
                    <div class="col-md-4">
                        <a href="{{ route('berita.show', $item->slug) }}" class="related-card">
                            <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://placehold.co/640x420/e0f2fe/1d4ed8?text=Berita' }}" alt="{{ $item->title }}">
                            <div class="p">
                                <small class="text-muted">{{ optional($item->published_at ?? $item->content_date ?? $item->created_at)->format('d M Y') }}</small>
                                <h6 class="fw-bold mt-1 mb-0">{{ Str::limit($item->title, 60) }}</h6>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</body>
</html>
