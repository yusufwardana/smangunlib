<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $action, string $modelType, ?int $modelId = null, mixed $before = null, mixed $after = null): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId ?? 0,
            'before_data' => $before instanceof Model ? $before->toArray() : $before,
            'after_data' => $after instanceof Model ? $after->toArray() : $after,
            'ip_address' => request()?->ip(),
            'user_agent' => substr((string) request()?->userAgent(), 0, 255),
            'created_at' => now(),
        ]);
    }
}
