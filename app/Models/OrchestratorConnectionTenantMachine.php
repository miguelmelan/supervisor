<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrchestratorConnectionTenantMachine extends Model
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
            'automated_process_orchestrator_connection_tenant_machine',
            'automated_process_id',
            'machine_id',
        )->withTimestamps();
    }

    public function alerts()
    {
        return $this->belongsToMany(
            OrchestratorConnectionTenantAlert::class,
            'orchestrator_connection_tenant_alert_oct_machine',
            'orchestrator_connection_tenant_machine_id',
            'orchestrator_connection_tenant_alert_id',
        )->withTimestamps();
    }

    public function pendingAlerts()
    {
        return $this->alerts()->get()->whereNull('read_at');
    }
}
