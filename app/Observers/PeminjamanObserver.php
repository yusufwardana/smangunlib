<?php

namespace App\Observers;

use App\Models\Peminjaman;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class PeminjamanObserver
{
    public function created(Peminjaman $peminjaman): void
    {
        $this->logActivity('create', $peminjaman, null, $peminjaman->toArray());
    }

    public function updated(Peminjaman $peminjaman): void
    {
        $this->logActivity('update', $peminjaman, $peminjaman->getOriginal(), $peminjaman->getChanges());
    }

    public function deleted(Peminjaman $peminjaman): void
    {
        $this->logActivity('delete', $peminjaman, $peminjaman->toArray(), null);
    }

    private function logActivity(string $action, Peminjaman $peminjaman, ?array $before, ?array $after): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => Peminjaman::class,
            'model_id' => $peminjaman->id,
            'before_data' => $before,
            'after_data' => $after,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
