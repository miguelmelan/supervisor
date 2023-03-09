<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AutomatedProcessOrchestratorConnection extends Pivot
{
    protected $casts = [
        'built_in_alerts' => 'boolean',
        'processes_supervision' => 'boolean',
        'machines_supervision' => 'boolean',
        'queues_supervision' => 'boolean',
        'kibana_built_in_alerts' => 'boolean',
    ];
}