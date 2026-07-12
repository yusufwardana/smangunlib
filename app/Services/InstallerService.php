<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InstallerService
{
    /**
     * Check if minimal requirements are met
     */
    public function checkRequirements()
    {
        $requirements = [
            'PHP >= 8.3' => version_compare(PHP_VERSION, '8.3.0', '>='),
            'BCMath' => extension_loaded('bcmath'),
            'Ctype' => extension_loaded('ctype'),
            'Fileinfo' => extension_loaded('fileinfo'),
            'JSON' => extension_loaded('json'),
            'Mbstring' => extension_loaded('mbstring'),
            'OpenSSL' => extension_loaded('openssl'),
            'PDO' => extension_loaded('pdo'),
            'PDO MySQL' => extension_loaded('pdo_mysql'),
            'Tokenizer' => extension_loaded('tokenizer'),
            'XML' => extension_loaded('xml'),
            'ZIP' => extension_loaded('zip'),
            'GD' => extension_loaded('gd'),
            'Intl' => extension_loaded('intl'),
        ];

        return $requirements;
    }

    /**
     * Check directory permissions
     */
    public function checkPermissions()
    {
        return [
            'storage/' => is_writable(storage_path()),
            'bootstrap/cache/' => is_writable(base_path('bootstrap/cache')),
        ];
    }

    /**
     * Test database connection
     */
    public function testDbConnection($host, $port, $database, $username, $password)
    {
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database}";
            $pdo = new \PDO($dsn, $username, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
            return true;
        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Write .env file
     */
    public function setEnv($data)
    {
        $envPath = base_path('.env');
        
        // Fallback: If .env doesn't exist, copy from .env.example
        if (!File::exists($envPath)) {
            File::copy(base_path('.env.example'), $envPath);
        }

        $envContent = File::get($envPath);

        foreach ($data as $key => $value) {
            // Escape values containing spaces
            if (preg_match('/\s/', $value)) {
                $value = '"' . $value . '"';
            }

            // Replace existing key or append
            if (preg_match("/^{$key}=/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        try {
            File::put($envPath, $envContent);
            return true;
        } catch (\Exception $e) {
            return false; // Permission denied
        }
    }

    /**
     * Create generic symlink (workaround for cPanel)
     */
    public function createSymlink()
    {
        $target = storage_path('app/public');
        $link = public_path('storage');

        if (file_exists($link)) {
            return true; // Already exists
        }

        try {
            symlink($target, $link);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
