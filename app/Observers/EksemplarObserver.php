<?php

namespace App\Observers;

use App\Models\Eksemplar;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class EksemplarObserver
{
    public function created(Eksemplar $eksemplar): void
    {
        $this->logActivity('create', $eksemplar, null, $eksemplar->toArray());
    }

    public function updated(Eksemplar $eksemplar): void
    {
        $this->logActivity('update', $eksemplar, $eksemplar->getOriginal(), $eksemplar->getChanges());
    }

    public function deleted(Eksemplar $eksemplar): void
    {
        $this->logActivity('delete', $eksemplar, $eksemplar->toArray(), null);
    }

    private function logActivity(string $action, Eksemplar $eksemplar, ?array $before, ?array $after): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => Eksemplar::class,
            'model_id' => $eksemplar->id,
            'before_data' => $before,
            'after_data' => $after,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
