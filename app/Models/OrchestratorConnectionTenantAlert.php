<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use BeyondCode\Comments\Traits\HasComments;

class OrchestratorConnectionTenantAlert extends Model
{
    use HasFactory, HasComments;

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
    
    public function falsePositive(): Attribute
    {
        return Attribute::get(function ($original) {
            return $original === 1;
        });
    }
}
