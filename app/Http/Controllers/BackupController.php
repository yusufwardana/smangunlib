<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BackupService;
use App\Models\Backup;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function index()
    {
        $backups = Backup::orderBy('created_at', 'desc')->get();
        return view('system.backup.index', compact('backups'));
    }

    public function process(Request $request)
    {
        $tipe = $request->tipe;
        
        try {
            $result = [];
            if ($tipe === 'database' || $tipe === 'full') {
                $result = $this->backupService->backupDatabase();
            }
            if ($tipe === 'storage' || $tipe === 'full') {
                $result = $this->backupService->backupStorage();
                // Jika full, kita asumsikan 2 proses terpisah tapi dicatat, di sini disederhanakan
            }

            if (!empty($result)) {
                Backup::create([
                    'nama_file' => $result['name'],
                    'tipe' => $tipe === 'full' ? 'storage' : $tipe, // simplified
                    'ukuran_mb' => $result['size_mb'],
                    'user_id' => auth()->id(),
                    'status' => 'completed'
                ]);
            }
            
            return back()->with('success', 'Backup ' . strtoupper($tipe) . ' berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        $backup = Backup::findOrFail($id);
        $path = storage_path('app/backups/' . $backup->nama_file);
        if (File::exists($path)) {
            return response()->download($path);
        }
        return back()->with('error', 'File tidak ditemukan di server.');
    }

    public function destroy($id)
    {
        $backup = Backup::findOrFail($id);
        $path = storage_path('app/backups/' . $backup->nama_file);
        if (File::exists($path)) {
            File::delete($path);
        }
        $backup->delete();
        return back()->with('success', 'Backup dihapus.');
    }
}
