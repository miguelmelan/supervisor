<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use BeyondCode\Comments\Traits\HasComments;
use Laravel\Scout\Searchable;

class OrchestratorConnectionTenantAlert extends Model
{
    use HasFactory, HasComments, Searchable;

    protected $fillable = [
        'external_id',
        'notification_name',
        'data',
        'component',
        'severity',
        'creation_time',
        'deep_link_relative_url',
        'read_at',
        'resolution_time_in_seconds',
        'locked_at',
        'resolution_details',
        'false_positive',
        'trigger_id',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (OrchestratorConnectionTenantAlert $alert) {
            $user = User::find(1);
            $alert->commentAsUser($user, 'Alert created');
        });
    }

    public function tenant()
    {
        return $this->belongsTo(OrchestratorConnectionTenant::class);
    }

    public function automatedProcesses()
    {
        return $this->belongsToMany(
            AutomatedProcess::class,
            'automated_process_orchestrator_connection_tenant_alert',
            'orchestrator_connection_tenant_alert_id',
            'automated_process_id',
        )->withTimestamps();
    }

    public function releases()
    {
        return $this->belongsToMany(
            OrchestratorConnectionTenantRelease::class,
            'orchestrator_connection_tenant_alert_oct_release',
            'orchestrator_connection_tenant_alert_id',
            'orchestrator_connection_tenant_release_id',
        )->withTimestamps();
    }

    public function machines()
    {
        return $this->belongsToMany(
            OrchestratorConnectionTenantMachine::class,
            'orchestrator_connection_tenant_alert_oct_machine',
            'orchestrator_connection_tenant_alert_id',
            'orchestrator_connection_tenant_machine_id',
        )->withTimestamps();
    }

    public function queues()
    {
        return $this->belongsToMany(
            OrchestratorConnectionTenantQueue::class,
            'orchestrator_connection_tenant_alert_oct_queue',
            'orchestrator_connection_tenant_alert_id',
            'orchestrator_connection_tenant_queue_id',
        )->withTimestamps();
    }

    public function readBy()
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
    
    public function falsePositive(): Attribute
    {
        return Attribute::get(function ($original) {
            return $original === 1;
        });
    }
}
