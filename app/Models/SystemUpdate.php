<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemUpdate extends Model
{
    protected $table = 'system_updates';
    protected $fillable = ['versi_lama', 'versi_baru', 'changelog', 'checksum', 'user_id', 'status', 'log'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
