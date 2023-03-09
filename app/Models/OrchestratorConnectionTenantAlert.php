<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'state',
        'deep_link_relative_url',
        'read_at',
        'resolution_time_in_seconds',
    ];

    /* public function readAt(): Attribute
    {
        return Attribute::set(function ($original) {
            $this->resolution_time_in_seconds = 55;
            //$this->resolution_time_in_seconds = Carbon::parse(Carbon::instance($original))->diffInSeconds(Carbon::now());
            return $original;
        });
    } */

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
}
