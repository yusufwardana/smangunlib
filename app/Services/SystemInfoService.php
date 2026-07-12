<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SystemInfoService
{
    public function getAppInfo()
    {
        return [
            'app_name' => config('app.name'),
            'app_version' => env('APP_VERSION', '1.0.0'),
            'build_number' => env('APP_BUILD', '1000'),
            'release_date' => env('APP_RELEASE_DATE', '2026-07-08'),
        ];
    }

    public function getServerInfo()
    {
        return [
            'laravel_version' => app()->version(),
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'mysql_version' => $this->getDatabaseVersion(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_upload_size' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'free_disk_space' => $this->formatBytes(disk_free_space(base_path())),
            'total_disk_space' => $this->formatBytes(disk_total_space(base_path())),
        ];
    }

    public function getPhpExtensions()
    {
        $required = ['bcmath', 'openssl', 'pdo', 'gd', 'zip', 'xml', 'intl', 'mbstring', 'curl', 'fileinfo', 'json'];
        $status = [];
        foreach ($required as $ext) {
            $status[$ext] = extension_loaded($ext);
        }
        return $status;
    }

    public function getFolderPermissions()
    {
        return [
            'storage' => is_writable(storage_path()),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            'public/storage' => is_writable(public_path('storage')),
        ];
    }

    public function getDatabaseInfo()
    {
        $connection = DB::connection();

        if ($connection->getDriverName() === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'");
            $pageSize = DB::select('PRAGMA page_size')[0]->page_size ?? 0;
            $pageCount = DB::select('PRAGMA page_count')[0]->page_count ?? 0;

            return [
                'total_tables' => count($tables),
                'size_mb' => round(($pageSize * $pageCount) / 1024 / 1024, 2),
                'charset' => 'UTF-8',
                'collation' => 'binary',
            ];
        }

        // Mendapatkan total table dan size di MySQL
        $dbName = $connection->getDatabaseName();
        $tables = DB::select('SHOW TABLES');
        $sizeQuery = DB::select(
            'SELECT SUM(data_length + index_length) / 1024 / 1024 AS size_mb FROM information_schema.TABLES WHERE table_schema = ?',
            [$dbName]
        );

        $dbSize = $sizeQuery[0]->size_mb ?? 0;
        
        return [
            'total_tables' => count($tables),
            'size_mb' => round($dbSize, 2),
            'charset' => config('database.connections.mysql.charset'),
            'collation' => config('database.connections.mysql.collation'),
        ];
    }

    public function getStatistics()
    {
        return [
            'total_users' => \App\Models\User::count(),
            'total_buku' => \App\Models\Buku::count(),
            'total_anggota' => \App\Models\Anggota::count(),
            'total_dokumen' => \App\Models\DokumenAdministrasi::count(),
            'total_backup' => \App\Models\Backup::count(),
        ];
    }

    public function getHealthCheck()
    {
        return [
            'database' => $this->checkDatabase(),
            'storage' => is_writable(storage_path()) ? 'ok' : 'error',
            'cache' => is_writable(base_path('bootstrap/cache')) ? 'ok' : 'error',
            'session' => $this->checkSession(),
        ];
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return 'ok';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function checkSession()
    {
        return session()->has('_token') ? 'ok' : 'warning';
    }

    private function getDatabaseVersion(): string
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return DB::select('select sqlite_version() as version')[0]->version ?? 'Unknown';
        }

        return DB::select('select version() as version')[0]->version ?? 'Unknown';
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
