<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLandingContentRequest;
use App\Models\LandingContent;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LandingContentController extends Controller
{
    public const TYPES = [
        'service' => 'Layanan',
        'literacy_program' => 'Program Literasi',
        'banner' => 'Banner',
        'slider' => 'Slider',
        'announcement' => 'Pengumuman',
        'news' => 'Berita',
        'gallery' => 'Galeri',
        'faq' => 'FAQ',
        'book_highlight' => 'Buku Pilihan',
        'stat' => 'Statistik',
        'book_category' => 'Kategori Buku',
        'calendar_event' => 'Kalender Kegiatan',
        'download' => 'Unduhan',
    ];

    /**
     * Halaman publik detail berita.
     */
    public function showNews(string $slug)
    {
        $news = LandingContent::where('type', 'news')
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $related = LandingContent::where('type', 'news')
            ->where('id', '!=', $news->id)
            ->active()
            ->latest()
            ->take(3)
            ->get();

        return view('berita.show', compact('news', 'related'));
    }

    public function index(?string $type = null)
    {
        $type = $type ?: 'service';
        abort_unless(array_key_exists($type, self::TYPES), 404);

        $items = LandingContent::where('type', $type)
            ->latest()
            ->paginate(15);

        return view('system.contents.index', [
            'items' => $items,
            'type' => $type,
            'typeLabel' => self::TYPES[$type],
            'types' => self::TYPES,
        ]);
    }

    public function create(string $type)
    {
        abort_unless(array_key_exists($type, self::TYPES), 404);

        return view('system.contents.form', [
            'item' => new LandingContent(['type' => $type, 'status' => 'active']),
            'type' => $type,
            'typeLabel' => self::TYPES[$type],
            'types' => self::TYPES,
        ]);
    }

    public function store(StoreLandingContentRequest $request)
    {
        $data = $this->payload($request);
        $item = LandingContent::create($data);
        ActivityLogger::log('create', LandingContent::class, $item->id, null, $item);

        return redirect()->route('system.contents.index', $item->type)->with('success', 'Konten berhasil ditambahkan.');
    }

    public function edit(LandingContent $content)
    {
        return view('system.contents.form', [
            'item' => $content,
            'type' => $content->type,
            'typeLabel' => self::TYPES[$content->type] ?? $content->type,
            'types' => self::TYPES,
        ]);
    }

    public function update(StoreLandingContentRequest $request, LandingContent $content)
    {
        $before = $content->replicate();
        $content->update($this->payload($request, $content));
        ActivityLogger::log('update', LandingContent::class, $content->id, $before, $content);

        return redirect()->route('system.contents.index', $content->type)->with('success', 'Konten berhasil diperbarui.');
    }

    public function destroy(LandingContent $content)
    {
        $before = $content->replicate();
        $content->delete();
        ActivityLogger::log('delete', LandingContent::class, $content->id, $before, null);

        return back()->with('success', 'Konten berhasil dihapus.');
    }

    /**
     * Upload gambar inline dari editor (Summernote) dan kembalikan URL publiknya.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:4096'],
        ]);

        $path = $request->file('image')->store('landing/editor', 'public');

        return response()->json([
            'url' => asset('storage/'.$path),
            'path' => $path,
        ]);
    }

    private function payload(StoreLandingContentRequest $request, ?LandingContent $content = null): array
    {
        $data = Arr::except($request->validated(), ['image', 'attachment']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['slug'] = $data['slug'] ?? Str::slug((string) ($data['title'] ?? ''));

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('landing/'.$data['type'].'/images', 'public');
        } elseif ($content) {
            $data['image'] = $content->image;
        }

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('landing/'.$data['type'].'/attachments', 'public');
        } elseif ($content) {
            $data['attachment'] = $content->attachment;
        }

        return $data;
    }
}
