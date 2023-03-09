<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Spatie\Tags\HasTags;

class OrchestratorConnection extends Model
{
    use HasFactory, HasTags;

    protected $fillable = [
        'code',
        'name',
        'hosting_type',
        'environment_type',
        'client_id',
        'client_secret',
        'url',
        'organization_name',
        'elasticsearch_enabled',
        'elasticsearch_index_configuration',
        'elasticsearch_url',
        'elasticsearch_anonymous_authentication',
        'elasticsearch_username',
        'elasticsearch_password',
        'kibana_enabled',
        'kibana_url',
        'verified',
    ];

    public function code(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value),
            set: fn ($value) => strtolower($value)
        );
    }

    public function clientSecret(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Crypt::encryptString($value)
        );
    }

    public function url(): Attribute
    {
        return Attribute::get(function ($original) {
            if ($this->hosting_type === 'cloud') {
                return sprintf(
                    '%s/%s',
                    config('constants.uipath.orchestrator.cloud.url_prefix'),
                    $this->organization_name
                );
            }
            return rtrim($original, '/');
        });
    }

    public function elasticsearchEnabled(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === 1,
            set: function ($value) {
                if (!$value) {
                    $this->elasticsearch_index_configuration = null;
                    $this->elasticsearch_url = null;
                    $this->elasticsearch_username = null;
                    $this->elasticsearch_password = null;
                } elseif ($this->elasticsearch_anonymous_authentication) {
                    $this->elasticsearch_username = null;
                    $this->elasticsearch_password = null;
                }
                return $value;
            }
        );
    }

    public function elasticSearchIndexConfiguration(): Attribute
    {
        return Attribute::get(function ($original) {
            return $original ?? config('constants.uipath.elasticsearch.default_index_configuration');
        });
    }

    public function elasticsearchAnonymousAuthentication(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === 1,
            set: function ($value) {
                if ($value) {
                    $this->elasticsearch_username = null;
                    $this->elasticsearch_password = null;
                }
                return $value;
            }
        );
    }

    public function elasticsearchPassword(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Crypt::encryptString(($value))
        );
    }

    public function kibanaEnabled(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === 1,
            set: function ($value) {
                if (!$value) {
                    $this->kibana_url = null;
                }
                return $value;
            }
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

    public function identityServerTokenEndpoint(): Attribute
    {
        return Attribute::make(function () {
            return getUiPathOrchestratorEndpoint($this->hosting_type, $this->url);
        });
    }

    public function tenants()
    {
        return $this->hasMany(OrchestratorConnectionTenant::class, 'orchestrator_connection_id');
    }

    public function automatedProcesses()
    {
        return $this->belongsToMany(
            AutomatedProcess::class,
            'orchestrator_connection_automated_process',
            'automated_process_id',
            'orchestrator_connection_id',
        )->withTimestamps();
    }

    public function properties()
    {
        return $this->belongsToMany(
            Property::class,
            'orchestrator_connection_property',
            'property_id',
            'orchestrator_connection_id',
        )->withTimestamps();
    }

    public function alerts()
    {
        return $this->hasManyThrough(
            OrchestratorConnectionTenantAlert::class,
            OrchestratorConnectionTenant::class,
            'orchestrator_connection_id',
            'tenant_id',
        );
    }
}
