<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrchestratorConnectionResource;
use App\Http\Resources\OrchestratorConnectionTenantAlertResource;
use App\Http\Resources\OrchestratorConnectionTenantResource;
use App\Http\Resources\UserResource;
use App\Models\AutomatedProcess;
use App\Models\OrchestratorConnection;
use App\Models\OrchestratorConnectionTenant;
use App\Models\OrchestratorConnectionTenantAlert;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class PendingAlertsController extends Controller
{
    public function index($loadSavedSearch = false)
    {
        request()->validate([
            'sorting.direction' => ['in:asc,desc'],
            'sorting.field' => ['in:id'],
        ]);

        $data = request()->all();
        if ($loadSavedSearch) {
            $data = request()->session()->get('pending-alerts.filters');
            if ($data) {
                request()->merge($data);
            }
        }
        request()->session()->put('pending-alerts.filters', $data);

        $periods = 7;

        $baseQuery = DB::table('orchestrator_connection_tenant_alerts')
        ->join('orchestrator_connection_tenants', 'orchestrator_connection_tenant_alerts.tenant_id', '=', 'orchestrator_connection_tenants.id')
        ->join('orchestrator_connections', 'orchestrator_connection_tenants.orchestrator_connection_id', '=', 'orchestrator_connections.id')
        ->leftJoin('automated_process_orchestrator_connection_tenant_alert', 'automated_process_orchestrator_connection_tenant_alert.orchestrator_connection_tenant_alert_id', '=', 'orchestrator_connection_tenant_alerts.id')
        ->leftJoin('automated_processes', 'automated_processes.id', 'automated_process_orchestrator_connection_tenant_alert.automated_process_id')
        ->when(request('data.alert.creationDateRange'), function ($query, $range) {
            $query->where('creation_time', '>=', $range[0])
                ->where('creation_time', '<=', $range[1]);
        })
        ->when(request('data.alert.lockingDateRange'), function ($query, $range) {
            $query->where('locked_at', '>=', $range[0])
                ->where('locked_at', '<=', $range[1]);
        })
        ->when(request('data.alert.selectedLockingUsers'), function ($query, $selected) {
            $query->whereIn('locked_by', $selected);
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
        ->whereNull('read_at')
        ->select('orchestrator_connection_tenant_alerts.*');
        $alerts = (clone $baseQuery)
            ->when(request('sorting'), function ($query, $sorting) {
                $query->orderBy($sorting['field'], $sorting['direction']);
            })
            ->when(!request('sorting'), function ($query) {
                $query->orderBy('orchestrator_connection_tenant_alerts.id', 'desc');
            });

        $alertsCount = $alerts->count();

        $alertsByCategory = $this->getAlertsByCategory($baseQuery);

        $alerts = $alerts->paginate(config('constants.pagination.items_per_page'))
            ->withQueryString()
            ->through(function ($alert) {
                $alert = OrchestratorConnectionTenantAlert::find($alert->id);
                $alert->load('automatedProcesses');
                return new OrchestratorConnectionTenantAlertResource($alert);
            });

        $alertsCollection = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()->sortBy('read_at')->whereNull('read_at')
        );

        $alertsLockingUsers = UserResource::collection(
            User::all()->sortBy('name')->whereIn('id', $alertsCollection->unique('locked_by')->pluck('locked_by'))
        );
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
            OrchestratorConnectionTenantAlert::all()->sortBy('read_at')->whereNotNull('read_at')
        );
        $closedAlertsCount = $closedAlerts->count();

        $automatedProcessesCount = AutomatedProcess::all()->count();

        $alertsAverageResolutionTimeEveryday = $this->getAlertsAverageResolutionTimeEveryday($periods);

        return Inertia::render('Dashboard/PendingAlerts/Index', [
            'alerts' => $alerts,
            'alertsByCategory' => $alertsByCategory,
            'alertsCount' => $alertsCount,
            'closedAlertsCount' => $closedAlertsCount,
            'automatedProcessesCount' => $automatedProcessesCount,
            'alertsAverageResolutionTimeEveryday' => $alertsAverageResolutionTimeEveryday,
            'alertsAverageResolutionTime' => $this->getAlertsAverageResolutionTime(),
            'filters' => Request::only(['data', 'sorting']),
            'alertsProperties' => [
                'lockingUser' => $alertsLockingUsers,
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

    private function getAlertsAverageResolutionTimeEveryday($periods)
    {
        $format = 'd/m';

        $currentDate = Carbon::now();
        $lowerLimitDate = $currentDate->setHours(0)->setMinutes(0)->setSeconds(0)->subDays($periods);
        $collection = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()
                ->whereNotNull('read_at')
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
            ->whereNotNull('read_at')
            ->avg('resolution_time_in_seconds');

        if ($average) {
            return CarbonInterval::seconds($average)->cascade()->forHumans(['parts' => 2]);
        }

        return null;
    }

    private function getAlertsByCategory($query)
    {
        $severity = (clone $query)->select(DB::raw('severity, count(orchestrator_connection_tenant_alerts.id) as count'))->groupBy('severity')->get();
        $notificationName = (clone $query)->select(DB::raw('notification_name, count(orchestrator_connection_tenant_alerts.id) as count'))->groupBy('notification_name')->get();
        $component = (clone $query)->select(DB::raw('component, count(orchestrator_connection_tenant_alerts.id) as count'))->groupBy('component')->get();

        return [
            'severity' => $severity,
            'notificationName' => $notificationName,
            'component' => $component,
        ];
    }
}
