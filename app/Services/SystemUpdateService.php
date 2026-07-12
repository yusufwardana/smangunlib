<?php

namespace App\Services;

use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Models\SystemUpdate;

class SystemUpdateService
{
    protected $updateDir;
    protected $backupDir;

    public function __construct()
    {
        $this->updateDir = storage_path('app/updates');
        $this->backupDir = storage_path('app/backups/updates');
        
        if (!File::exists($this->updateDir)) File::makeDirectory($this->updateDir, 0755, true);
        if (!File::exists($this->backupDir)) File::makeDirectory($this->backupDir, 0755, true);
    }

    public function validateAndExtract($zipPath)
    {
        $zip = new ZipArchive;
        if ($zip->open($zipPath) === TRUE) {
            // Check for manifest
            if ($zip->locateName('manifest.json') === false) {
                $zip->close();
                throw new \Exception('File update tidak valid. manifest.json tidak ditemukan.');
            }

            // Extract to temporary update folder
            $tempDir = $this->updateDir . '/temp_' . time();
            File::makeDirectory($tempDir);
            $zip->extractTo($tempDir);
            $zip->close();

            $manifestContent = File::get($tempDir . '/manifest.json');
            $manifest = json_decode($manifestContent, true);

            if (!isset($manifest['version']) || !isset($manifest['checksum'])) {
                File::deleteDirectory($tempDir);
                throw new \Exception('Struktur manifest.json tidak lengkap.');
            }

            return ['path' => $tempDir, 'manifest' => $manifest];
        } else {
            throw new \Exception('Gagal membuka file ZIP.');
        }
    }

    public function backupCurrentCore()
    {
        // Membackup folder app, resources, routes, database, public sebelum ditimpa
        $folders = ['app', 'resources', 'routes', 'database', 'public'];
        $backupName = 'core_backup_' . time() . '.zip';
        $backupPath = $this->backupDir . '/' . $backupName;

        $zip = new ZipArchive;
        if ($zip->open($backupPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($folders as $folder) {
                $dirPath = base_path($folder);
                if (File::exists($dirPath)) {
                    $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPath), \RecursiveIteratorIterator::LEAVES_ONLY);
                    foreach ($files as $name => $file) {
                        if (!$file->isDir()) {
                            $filePath = $file->getRealPath();
                            $relativePath = substr($filePath, strlen(base_path()) + 1);
                            $zip->addFile($filePath, $relativePath);
                        }
                    }
                }
            }
            $zip->close();
            return $backupPath;
        }
        throw new \Exception('Gagal mem-backup sistem core saat ini.');
    }

    public function applyUpdate($tempDir)
    {
        $foldersToReplace = ['app', 'resources', 'routes', 'database', 'public'];
        
        foreach ($foldersToReplace as $folder) {
            $source = $tempDir . '/' . $folder;
            $destination = base_path($folder);
            
            if (File::exists($source)) {
                File::copyDirectory($source, $destination);
            }
        }

        // Jalankan perintah post-update
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('optimize:clear');

        // Bersihkan temp
        File::deleteDirectory($tempDir);

        return true;
    }
}
