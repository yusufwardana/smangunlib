<?php

namespace App\Services;

use App\Models\License;
use Carbon\Carbon;

class LicenseService
{
    /**
     * Memvalidasi format License Key
     */
    public function validateFormat($key)
    {
        // Format: LIBSYS-XXXX-XXXX-XXXX-XXXX
        return preg_match('/^LIBSYS-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $key);
    }

    /**
     * Aktivasi lisensi (Simulasi offline validation untuk cPanel lokal)
     */
    public function activate($key, $namaSekolah, $email)
    {
        if (!$this->validateFormat($key)) {
            throw new \Exception('Format License Key tidak valid.');
        }

        // Cek apakah key sudah digunakan di sistem ini
        $exists = License::where('license_key', $key)->first();
        if ($exists) {
            throw new \Exception('Lisensi ini sudah terdaftar di sistem.');
        }

        // Simulasi: Buat masa aktif 1 tahun (jika API remote tidak ada)
        $license = License::create([
            'license_key' => $key,
            'nama_sekolah' => $namaSekolah,
            'domain' => request()->getHost(),
            'email' => $email,
            'tanggal_aktivasi' => now(),
            'expired_date' => now()->addYear(),
            'status' => 'active',
            'versi_aplikasi' => env('APP_VERSION', '1.0.0'),
            'max_user' => 1000,
            'max_storage_mb' => 5000,
        ]);

        \App\Models\Setting::set('active_license_key', $key);
        
        return $license;
    }

    /**
     * Memeriksa status lisensi aktif saat ini
     */
    public function checkCurrentLicense()
    {
        $key = \App\Models\Setting::get('active_license_key');
        if (!$key) return null;

        $license = License::where('license_key', $key)->first();
        if (!$license) return null;

        if (Carbon::now()->gt($license->expired_date) && $license->status === 'active') {
            $license->update(['status' => 'expired']);
        }

        return $license;
    }
}
