<?php

namespace App\Services;

use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Backup;
use Carbon\Carbon;

class BackupService
{
    protected $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!File::exists($this->backupDir)) File::makeDirectory($this->backupDir, 0755, true);
    }

    public function backupDatabase()
    {
        $filename = 'backup_db_' . date('Ymd_His') . '.sql';
        $path = $this->backupDir . '/' . $filename;
        
        // Native PDO Backup implementation (simplified for cPanel compatibility)
        $tables = DB::select('SHOW TABLES');
        $dbName = env('DB_DATABASE');
        $key = 'Tables_in_' . $dbName;
        
        $sql = "-- Database Backup\n-- Date: " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$key;
            $sql .= "DROP TABLE IF EXISTS `$tableName`;\n";
            $createTable = DB::select("SHOW CREATE TABLE `$tableName`")[0]->{'Create Table'};
            $sql .= $createTable . ";\n\n";

            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $values = array_map(function($value) {
                    if (is_null($value)) return 'NULL';
                    return "'" . addslashes($value) . "'";
                }, (array)$row);
                $sql .= "INSERT INTO `$tableName` VALUES (" . implode(',', $values) . ");\n";
            }
            $sql .= "\n\n";
        }

        File::put($path, $sql);
        
        // Zip the SQL
        $zipFile = str_replace('.sql', '.zip', $path);
        $zip = new ZipArchive;
        if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($path, $filename);
            $zip->close();
            File::delete($path); // Delete raw SQL
            
            return [
                'path' => $zipFile,
                'name' => basename($zipFile),
                'size_mb' => round(filesize($zipFile) / 1024 / 1024, 2)
            ];
        }

        throw new \Exception('Gagal membuat ZIP Database Backup');
    }

    public function backupStorage()
    {
        $filename = 'backup_storage_' . date('Ymd_His') . '.zip';
        $path = $this->backupDir . '/' . $filename;

        $zip = new ZipArchive;
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $storagePath = storage_path('app');
            // Menghindari rekursi backup dirinya sendiri
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($storagePath), \RecursiveIteratorIterator::LEAVES_ONLY);
            
            foreach ($files as $name => $file) {
                if (!$file->isDir() && strpos($file->getRealPath(), 'backups') === false) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($storagePath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
            
            return [
                'path' => $path,
                'name' => $filename,
                'size_mb' => round(filesize($path) / 1024 / 1024, 2)
            ];
        }
        throw new \Exception('Gagal membuat ZIP Storage Backup');
    }
}
