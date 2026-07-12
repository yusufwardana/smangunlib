<?php

namespace App\Observers;

use App\Models\Anggota;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class AnggotaObserver
{
    public function created(Anggota $anggota): void
    {
        $this->logActivity('create', $anggota, null, $anggota->toArray());
    }

    public function updated(Anggota $anggota): void
    {
        $this->logActivity('update', $anggota, $anggota->getOriginal(), $anggota->getChanges());
    }

    public function deleted(Anggota $anggota): void
    {
        $this->logActivity('delete', $anggota, $anggota->toArray(), null);
    }

    private function logActivity(string $action, Anggota $anggota, ?array $before, ?array $after): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => Anggota::class,
            'model_id' => $anggota->id,
            'before_data' => $before,
            'after_data' => $after,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
