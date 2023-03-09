<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrchestratorConnectionTenantAlertResource;
use App\Models\OrchestratorConnectionTenantAlert;
use Inertia\Inertia;

class ClosedAlertsController extends Controller
{
    public function index()
    {
        $closedAlerts = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()->sortBy('creation_time')->where('read_at', !null)
        );

        $pendingAlertsCount = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()->sortBy('read_at')->where('read_at', null)
        )->count();

        return Inertia::render('Dashboard/ClosedAlerts/Index', [
            'closedAlerts' => $closedAlerts,
            'pendingAlertsCount' => $pendingAlertsCount,
        ]);
    }
}
