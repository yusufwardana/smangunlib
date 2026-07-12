<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $table = 'backups';
    protected $fillable = ['nama_file', 'tipe', 'ukuran_mb', 'user_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
