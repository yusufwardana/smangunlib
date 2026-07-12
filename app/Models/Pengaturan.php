<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = [
        'key',
        'value',
        'description',
        'type'
    ];

    // No soft deletes for configuration
}
