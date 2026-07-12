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
            $this->validateZipEntries($zip);

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

            if (!is_array($manifest) || !isset($manifest['version']) || !isset($manifest['checksum'])) {
                File::deleteDirectory($tempDir);
                throw new \Exception('Struktur manifest.json tidak lengkap.');
            }

            $checksum = $this->hashExtractedPayload($tempDir);
            if (!hash_equals((string) $manifest['checksum'], $checksum)) {
                File::deleteDirectory($tempDir);
                throw new \Exception('Checksum file update tidak valid.');
            }

            return ['path' => $tempDir, 'manifest' => $manifest];
        } else {
            throw new \Exception('Gagal membuka file ZIP.');
        }
    }

    protected function validateZipEntries(ZipArchive $zip): void
    {
        $allowedRoots = ['app/', 'bootstrap/', 'config/', 'database/', 'public/', 'resources/', 'routes/', 'manifest.json'];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = str_replace('\\', '/', (string) $zip->getNameIndex($i));
            $normalized = ltrim($name, '/');

            if ($normalized === '' || str_contains($normalized, '../') || str_starts_with($name, '/') || preg_match('/^[A-Za-z]:\//', $name)) {
                throw new \Exception('File update mengandung path tidak aman: '.$name);
            }

            $isAllowed = false;
            foreach ($allowedRoots as $root) {
                if ($normalized === $root || str_starts_with($normalized, $root)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (! $isAllowed) {
                throw new \Exception('File update mengandung lokasi tidak diizinkan: '.$name);
            }
        }
    }

    protected function hashExtractedPayload(string $tempDir): string
    {
        $hashContext = hash_init('sha256');
        $files = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($tempDir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = str_replace('\\', '/', substr($file->getRealPath(), strlen($tempDir) + 1));
                if ($relativePath !== 'manifest.json') {
                    $files[$relativePath] = $file->getRealPath();
                }
            }
        }

        ksort($files);

        foreach ($files as $relativePath => $path) {
            hash_update($hashContext, $relativePath."\n");
            hash_update_file($hashContext, $path);
            hash_update($hashContext, "\n");
        }

        return hash_final($hashContext);
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
