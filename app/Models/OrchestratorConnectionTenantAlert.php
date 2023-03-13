<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrchestratorConnectionTenantAlert extends Model
{
    use HasFactory;

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
    ];

    public function tenant()
    {
        return $this->belongsTo(OrchestratorConnectionTenant::class);
    }

    public function automatedProcess()
    {
        return $this->belongsTo(AutomatedProcess::class);
    }

    public function release()
    {
        return $this->belongsTo(OrchestratorConnectionTenantRelease::class);
    }

    public function machine()
    {
        return $this->belongsTo(OrchestratorConnectionTenantMachine::class);
    }

    public function queue()
    {
        return $this->belongsTo(OrchestratorConnectionTenantQueue::class);
    }

    public function readBy()
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
}
