<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaAssetRequest;
use App\Models\MediaAsset;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class MediaManagerController extends Controller
{
    public function index(Request $request)
    {
        $media = MediaAsset::query()
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->q.'%'))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->category))
            ->latest()
            ->paginate(24);

        return view('system.media.index', compact('media'));
    }

    public function store(StoreMediaAssetRequest $request)
    {
        foreach ($request->file('files', []) as $file) {
            $folder = trim((string) $request->input('folder', 'media'), '/');
            $path = $file->store($folder ?: 'media', 'public');

            $asset = MediaAsset::create([
                'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'category' => $request->input('category'),
                'folder' => $folder,
                'disk' => 'public',
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);

            ActivityLogger::log('upload', MediaAsset::class, $asset->id, null, $asset);
        }

        return back()->with('success', 'Media berhasil diunggah.');
    }

    public function destroy(MediaAsset $media)
    {
        $before = $media->replicate();
        $media->delete();
        ActivityLogger::log('delete', MediaAsset::class, $media->id, $before, null);

        return back()->with('success', 'Media berhasil dihapus.');
    }
}
