<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;

class AIBasedAlertTrigger extends Model
{
    use HasFactory, HasTags;

    protected $fillable = [
        'code',
        'name',
        'conditions',
        'recurrence',
        'crons',
        'verifications',
    ];

    protected $casts = [
        'crons' => 'array',
        'verifications' => AsArrayObject::class,
    ];

    public function code(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value),
            set: fn ($value) => strtolower($value)
        );
    }

    public function orchestratorConnections()
    {
        return $this->belongsToMany(
            OrchestratorConnection::class,
            'orchestrator_connection_a_i_based_alert_trigger',
            'a_i_based_alert_trigger_id',
            'orchestrator_connection_id',
        )->withTimestamps();
    }

    public function orchestratorConnectionTenants()
    {
        return $this->belongsToMany(
            OrchestratorConnectionTenant::class,
            'a_i_based_alert_trigger_orchestrator_connection_tenant',
            'a_i_based_alert_trigger_id',
            'tenant_id',
        )->withTimestamps();
    }

    public function releases()
    {
        return $this->belongsToMany(
            OrchestratorConnectionTenantRelease::class,
            'a_i_based_alert_trigger_orchestrator_connection_tenant_release',
            'a_i_based_alert_trigger_id',
            'release_id',
        )->withTimestamps();
    }

    public function machines()
    {
        return $this->belongsToMany(
            OrchestratorConnectionTenantMachine::class,
            'a_i_based_alert_trigger_orchestrator_connection_tenant_machine',
            'a_i_based_alert_trigger_id',
            'machine_id',
        )->withTimestamps();
    }

    public function queues()
    {
        return $this->belongsToMany(
            OrchestratorConnectionTenantQueue::class,
            'a_i_based_alert_trigger_orchestrator_connection_tenant_queue',
            'a_i_based_alert_trigger_id',
            'queue_id',
        )->withTimestamps();
    }

    public function conditions()
    {
        return $this->hasMany(AIBasedAlertTriggerCondition::class, 'a_i_based_alert_trigger_id');
    }

    public function alerts()
    {
        return $this->hasMany(OrchestratorConnectionTenantAlert::class, 'a_i_based_alert_trigger_id');
    }
}
