<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrchestratorConnectionTenantQueue extends Model
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
            'automated_process_orchestrator_connection_tenant_queue',
            'automated_process_id',
            'queue_id',
        )->withTimestamps();
    }

    public function alerts()
    {
        return $this->hasMany(OrchestratorConnectionTenantAlert::class, 'queue_id');
    }
}
