<?php

namespace App\Observers;

use App\Models\DokumenAdministrasi;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class DokumenAdministrasiObserver
{
    public function created(DokumenAdministrasi $dokumen): void
    {
        $this->logActivity('create', $dokumen, null, $dokumen->toArray());
    }

    public function updated(DokumenAdministrasi $dokumen): void
    {
        $this->logActivity('update', $dokumen, $dokumen->getOriginal(), $dokumen->getChanges());
    }

    public function deleted(DokumenAdministrasi $dokumen): void
    {
        $this->logActivity('delete', $dokumen, $dokumen->toArray(), null);
    }

    private function logActivity(string $action, DokumenAdministrasi $dokumen, ?array $before, ?array $after): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => DokumenAdministrasi::class,
            'model_id' => $dokumen->id,
            'before_data' => $before,
            'after_data' => $after,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
