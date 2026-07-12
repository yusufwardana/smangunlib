<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    public $timestamps = false; // Karena hanya ada created_at, kita tangani manual

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'before_data',
        'after_data',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    protected function casts(): array
    {
        return [
            'before_data' => 'array',
            'after_data' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
