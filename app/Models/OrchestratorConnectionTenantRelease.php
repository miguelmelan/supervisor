<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrchestratorConnectionTenantRelease extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id', 'external_folder_id',
    ];

    public function tenant()
    {
        return $this->belongsTo(OrchestratorConnectionTenant::class);
    }

    public function automatedProcesses()
    {
        return $this->belongsToMany(
            AutomatedProcess::class,
            'automated_process_orchestrator_connection_tenant_release',
            'automated_process_id',
            'release_id',
        )->withTimestamps();
    }

    public function alerts()
    {
        return $this->hasMany(OrchestratorConnectionTenantAlert::class, 'release_id');
    }

    public function pendingAlerts()
    {
        return $this->alerts()->get()->whereNull('read_at');
    }
}
