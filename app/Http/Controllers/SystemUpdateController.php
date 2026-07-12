<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SystemUpdateService;
use App\Models\SystemUpdate;
use Illuminate\Support\Facades\Log;

class SystemUpdateController extends Controller
{
    protected $updateService;

    public function __construct(SystemUpdateService $updateService)
    {
        $this->updateService = $updateService;
    }

    public function index()
    {
        $updates = SystemUpdate::orderBy('created_at', 'desc')->get();
        return view('system.update.index', compact('updates'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'update_file' => 'required|file|mimes:zip|max:50000', // 50MB
        ]);

        try {
            $zipPath = $request->file('update_file')->getRealPath();
            
            // 1. Ekstrak dan validasi manifest
            $extraction = $this->updateService->validateAndExtract($zipPath);
            $tempDir = $extraction['path'];
            $manifest = $extraction['manifest'];

            // 2. Catat niat update ke DB
            $updateLog = SystemUpdate::create([
                'versi_lama' => env('APP_VERSION', '1.0.0'),
                'versi_baru' => $manifest['version'],
                'changelog' => $manifest['changelog'] ?? '',
                'checksum' => $manifest['checksum'],
                'user_id' => auth()->id(),
                'status' => 'extracting'
            ]);

            // 3. Backup Core
            $this->updateService->backupCurrentCore();

            // 4. Apply Update (Replace & Migrate)
            $this->updateService->applyUpdate($tempDir);

            // 5. Success
            $updateLog->update(['status' => 'success', 'log' => 'Update berhasil di-apply beserta migrasi.']);

            return back()->with('success', 'Sistem berhasil diperbarui ke versi ' . $manifest['version']);
        } catch (\Exception $e) {
            Log::error('System Update Error: ' . $e->getMessage());
            return back()->with('error', 'Update Gagal: ' . $e->getMessage());
        }
    }
}
