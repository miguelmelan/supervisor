<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    public function key()
    {
        return $this->belongsTo(PropertyKey::class);
    }

    public function value()
    {
        return $this->belongsTo(PropertyValue::class);
    }

    public function orchestratorConnections()
    {
        return $this->belongsToMany(
            OrchestratorConnection::class,
            'orchestrator_connection_property',
            'orchestrator_connection_id',
            'property_id',
        )->withTimestamps();
    }

    public function automatedProcesses()
    {
        return $this->belongsToMany(
            AutomatedProcess::class,
            'automated_process_property',
            'automated_process_id',
            'property_id',
        )->withTimestamps();
    }
}
