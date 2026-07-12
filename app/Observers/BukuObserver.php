<?php

namespace App\Observers;

use App\Models\Buku;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class BukuObserver
{
    public function created(Buku $buku): void
    {
        $this->logActivity('create', $buku, null, $buku->toArray());
    }

    public function updated(Buku $buku): void
    {
        $this->logActivity('update', $buku, $buku->getOriginal(), $buku->getChanges());
    }

    public function deleted(Buku $buku): void
    {
        $this->logActivity('delete', $buku, $buku->toArray(), null);
    }

    private function logActivity(string $action, Buku $buku, ?array $before, ?array $after): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => Buku::class,
            'model_id' => $buku->id,
            'before_data' => $before,
            'after_data' => $after,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
