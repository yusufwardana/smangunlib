<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $table = 'licenses';
    protected $fillable = [
        'license_key', 'nama_sekolah', 'domain', 'email', 
        'tanggal_aktivasi', 'expired_date', 'status', 
        'versi_aplikasi', 'max_user', 'max_storage_mb'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_aktivasi' => 'date',
            'expired_date' => 'date',
        ];
    }
}
