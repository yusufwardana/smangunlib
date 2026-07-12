<?php

namespace App\Observers;

use App\Models\Denda;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class DendaObserver
{
    public function created(Denda $denda): void
    {
        $this->logActivity('create', $denda, null, $denda->toArray());
    }

    public function updated(Denda $denda): void
    {
        $this->logActivity('update', $denda, $denda->getOriginal(), $denda->getChanges());
    }

    public function deleted(Denda $denda): void
    {
        $this->logActivity('delete', $denda, $denda->toArray(), null);
    }

    private function logActivity(string $action, Denda $denda, ?array $before, ?array $after): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => Denda::class,
            'model_id' => $denda->id,
            'before_data' => $before,
            'after_data' => $after,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
