<?php

namespace App\Http\Controllers;

use App\Http\Resources\AutomatedProcessResource;
use App\Http\Resources\OrchestratorConnectionResource;
use App\Http\Resources\OrchestratorConnectionTenantAlertResource;
use App\Http\Resources\OrchestratorConnectionTenantResource;
use App\Models\AutomatedProcess;
use App\Models\OrchestratorConnection;
use App\Models\OrchestratorConnectionTenant;
use App\Models\OrchestratorConnectionTenantAlert;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class PendingAlertsController extends Controller
{
    public function index()
    {
        request()->validate([
            'sorting.direction' => ['in:asc,desc'],
            'sorting.field' => ['in:id'],
        ]);

        $periods = 7;

        $alerts = DB::table('orchestrator_connection_tenant_alerts')
            ->join('orchestrator_connection_tenants', 'orchestrator_connection_tenant_alerts.tenant_id', '=', 'orchestrator_connection_tenants.id')
            ->join('orchestrator_connections', 'orchestrator_connection_tenants.orchestrator_connection_id', '=', 'orchestrator_connections.id')
            ->when(request('sorting'), function ($query, $sorting) {
                $query->orderBy($sorting['field'], $sorting['direction']);
            })
            ->when(!request('sorting'), function ($query) {
                $query->orderBy('orchestrator_connection_tenant_alerts.id', 'desc');
            })
            ->when(request('data.alert.creationDateRange'), function ($query, $range) {
                $query->where('creation_time', '>=', $range[0])
                    ->where('creation_time', '<=', $range[1]);
            })
            ->when(request('data.alert.selectedSeverities'), function ($query, $selected) {
                $query->whereIn('severity', $selected);
            })
            ->when(request('data.alert.selectedNotificationNames'), function ($query, $selected) {
                $query->whereIn('notification_name', $selected);
            })
            ->when(request('data.alert.selectedComponents'), function ($query, $selected) {
                $query->whereIn('component', $selected);
            })
            ->when(request('data.orchestratorConnection.selected'), function ($query, $selected) {
                $query->whereIn('orchestrator_connections.id', $selected);
            })
            ->when(request('data.orchestratorConnection.selectedTenants'), function ($query, $selected) {
                $query->whereIn('orchestrator_connection_tenants.id', $selected);
            })
            ->when(request('data.orchestratorConnection.selectedHostingTypes'), function ($query, $selected) {
                $query->whereIn('orchestrator_connections.hosting_type', $selected);
            })
            ->when(request('data.orchestratorConnection.selectedEnvironmentTypes'), function ($query, $selected) {
                $query->whereIn('orchestrator_connections.environment_type', $selected);
            })
            ->where('read_at', null)
            ->select('orchestrator_connection_tenant_alerts.*')
            ->paginate(config('constants.pagination.items_per_page'))
            ->withQueryString()
            ->through(fn ($alert) => [
                'id' => $alert->id,
                'id_padded' => str_pad($alert->id, 4, '0', STR_PAD_LEFT),
                'tenant' => new OrchestratorConnectionTenantResource(
                    OrchestratorConnectionTenant::with('orchestratorConnection')->find($alert->tenant_id)
                ),
                'automated_process' => $alert->automated_process_id ? new AutomatedProcessResource(
                    AutomatedProcess::find($alert->automated_process_id)
                ) : null,
                'external_id' => $alert->external_id,
                'notification_name' => $alert->notification_name,
                '_data' => $alert->data,
                'component' => $alert->component,
                'severity' => $alert->severity,
                'creation_time' => $alert->creation_time,
                'creation_time_for_humans' => Carbon::parse($alert->creation_time)->diffForHumans(Carbon::now()),
                'state' => $alert->state,
                'deep_link_relative_url' => $alert->deep_link_relative_url,
                'read_at' => $alert->read_at,
                'resolution_time_in_seconds' => $alert->resolution_time_in_seconds,
            ]);

        $alertsCollection = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()->sortBy('read_at')->where('read_at', null)
        );
        $alertsCount = $alertsCollection->count();
        $alertsSeverities = $alertsCollection->unique('severity')->pluck('severity');
        $alertsNotificationNames = $alertsCollection->unique('notification_name')->pluck('notification_name');
        $alertsComponents = $alertsCollection->unique('component')->pluck('component');
        $orchestratorConnectionTenants = OrchestratorConnectionTenantResource::collection(
            OrchestratorConnectionTenant::with('orchestratorConnection')->get()->sortBy('code')->whereIn('id', $alertsCollection->pluck('tenant.id')->unique())
        );
        $orchestratorConnections = OrchestratorConnectionResource::collection(
            OrchestratorConnection::all()->sortBy('code')->whereIn('id', $orchestratorConnectionTenants->pluck('orchestratorConnection.id')->unique())
        );
        $orchestratorConnectionsHostingTypes = $orchestratorConnections->pluck('hosting_type')->unique();
        $orchestratorConnectionsEnvironmentTypes = $orchestratorConnections->pluck('environment_type')->unique();

        $closedAlerts = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()->sortBy('read_at')->where('read_at', !null)
        );
        $closedAlertsCount = $closedAlerts->count();

        $automatedProcessesCount = AutomatedProcess::all()->count();

        $alertsAverageResolutionTimeEveryday = $this->getAlertsAverageResolutionTimeEveryday($periods);

        return Inertia::render('Dashboard/PendingAlerts/Index', [
            'alerts' => $alerts,
            'alertsCount' => $alertsCount,
            'closedAlertsCount' => $closedAlertsCount,
            'automatedProcessesCount' => $automatedProcessesCount,
            'alertsAverageResolutionTimeEveryday' => $alertsAverageResolutionTimeEveryday,
            'alertsAverageResolutionTime' => $this->getAlertsAverageResolutionTime(),
            'filters' => Request::only(['data', 'sorting']),
            'alertsProperties' => [
                'severity' => $alertsSeverities,
                'notificationName' => $alertsNotificationNames,
                'component' => $alertsComponents,
            ],
            'orchestratorConnectionsProperties' => [
                'self' => $orchestratorConnections,
                'tenants' => $orchestratorConnectionTenants,
                'hostingType' => $orchestratorConnectionsHostingTypes,
                'environmentType' => $orchestratorConnectionsEnvironmentTypes,
            ],
        ]);
    }

    public function edit(OrchestratorConnectionTenantAlert $alert)
    {
        return redirect()->route('pending-alerts.index');
    }

    public function read(OrchestratorConnectionTenantAlert $alert)
    {
        return redirect()->route('pending-alerts.index');
    }

    public function lock(OrchestratorConnectionTenantAlert $alert)
    {
        return redirect()->route('pending-alerts.index');
    }

    public function unlock(OrchestratorConnectionTenantAlert $alert)
    {
        return redirect()->route('pending-alerts.index');
    }

    public function bulkRead()
    {
        return redirect()->route('pending-alerts.index');
    }

    public function bulkLock()
    {
        return redirect()->route('pending-alerts.index');
    }

    public function bulkUnlock()
    {
        return redirect()->route('pending-alerts.index');
    }

    private function getAlertsAverageResolutionTimeEveryday($periods)
    {
        $format = 'd/m';

        $currentDate = Carbon::now();
        $lowerLimitDate = $currentDate->setHours(0)->setMinutes(0)->setSeconds(0)->subDays($periods);
        $collection = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()
                ->where('read_at', !null)
                ->where('creation_time', '>=', $lowerLimitDate)
        )->collection->sortBy('creation_time');
        $data = $collection
            ->groupBy(function ($alert) use ($format) {
                return Carbon::parse($alert->creation_time)
                    ->format($format);
            })->map(function ($group) {
                return [
                    'original' => '',
                    'forHumans' => CarbonInterval::seconds($group->avg('resolution_time_in_seconds'))->cascade()->forHumans(['parts' => 2]),
                ];
            });
        $categories = array_map(function ($index) use ($format, $lowerLimitDate) {
            return $lowerLimitDate->copy()->addDays($index)->format($format);
        }, range(0, $periods));

        return [
            'data' => $data,
            'categories' => $categories,
        ];
    }

    private function getAlertsAverageResolutionTime()
    {
        $average = OrchestratorConnectionTenantAlert::all()
            ->where('read_at', !null)
            ->avg('resolution_time_in_seconds');

        if ($average) {
            return CarbonInterval::seconds($average)->cascade()->forHumans(['parts' => 2]);
        }

        return null;
    }
}
