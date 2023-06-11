<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class OrchestratorConnectionTenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'client_id',
        'client_secret',
        'webhook_secret',
        'uuid',
        'verified',
    ];

     /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($tenant) {
            $tenant->uuid = Str::uuid();
            $tenant->webhook_secret = fake(app()->getLocale())->password(12);
        });
    }

    public function clientSecret(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Crypt::encryptString($value)
        );
    }

    public function webhookSecret(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Crypt::encryptString($value)
        );
    }

    public function verified(): Attribute
    {
        return Attribute::get(function ($original) {
            return $original === 1;
        });
    }

    public function verifiedAtForHumans(): Attribute
    {
        return Attribute::get(function ($original) {
            if ($this->verified_at) {
                return Carbon::parse($this->verified_at)->diffForHumans();
            }
            return null;
        });
    }

    public function orchestratorConnection()
    {
        return $this->belongsTo(OrchestratorConnection::class);
    }

    public function automatedProcesses()
    {
        return $this->belongsToMany(
            AutomatedProcess::class,
            'automated_process_orchestrator_connection_tenant',
            'tenant_id',
            'automated_process_id',
        )->withTimestamps();
    }

    public function releases()
    {
        return $this->hasMany(OrchestratorConnectionTenantRelease::class, 'tenant_id');
    }

    public function machines()
    {
        return $this->hasMany(OrchestratorConnectionTenantMachine::class, 'tenant_id');
    }

    public function queues()
    {
        return $this->hasMany(OrchestratorConnectionTenantQueue::class, 'tenant_id');
    }

    public function alerts()
    {
        return $this->hasMany(OrchestratorConnectionTenantAlert::class, 'tenant_id');
    }

    public function pendingAlerts()
    {
        return $this->alerts()->get()->whereNull('read_at');
    }
}
