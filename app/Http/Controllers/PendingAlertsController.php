<?php

namespace App\Http\Controllers;

use App\Http\Resources\AutomatedProcessResource;
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
use Illuminate\Http\Request as HttpRequest;
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
                'deep_link_relative_url' => $alert->deep_link_relative_url,
                'read_at' => $alert->read_at,
                'resolution_time_in_seconds' => $alert->resolution_time_in_seconds,
                'locked_at' => $alert->locked_at,
                'locked_by' => $alert->locked_by ? new UserResource(User::find($alert->locked_by)) : null,
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

    private function doRead(OrchestratorConnectionTenantAlert $alert, $loop = false)
    {
        if ($alert->read_at) {
            if (!$loop) {
                createToast(__('Alert already read by :username', [
                    'username' => User::find($alert->read_by)->name,
                ]), 'error');
                return false;
            }
        } else {
            $alert->read_at = Carbon::now();
            $alert->readBy()->associate(auth()->user());
            $alert->save();
        }
        return true;
    }

    private function doLock(OrchestratorConnectionTenantAlert $alert, $loop = false)
    {
        if ($alert->read_at) {
            if (!$loop) {
                createToast(__('Alert already read by :username', [
                    'username' => User::find($alert->read_by)->name,
                ]), 'error');
                return false;
            }
        } elseif ($alert->locked_at) {
            if (!$loop) {
                createToast(__('Alert already locked by :username', [
                    'username' => User::find($alert->locked_by)->name,
                ]), 'error');
            }
            return false;
        } else {
            $alert->locked_at = Carbon::now();
            $alert->lockedBy()->associate(auth()->user());
            $alert->save();
        }
        return true;
    }

    private function doUnlock(OrchestratorConnectionTenantAlert $alert, $loop = false)
    {
        if ($alert->read_at) {
            if (!$loop) {
                createToast(__('Alert already read by :username', [
                    'username' => User::find($alert->read_by)->name,
                ]), 'error');
                return false;
            }
        } elseif (!$alert->locked_at) {
                if (!$loop) {
                createToast(__('Alert cannot be unlocked as it is not locked.'), 'error');
            }
            return false;
        } elseif ($alert->locked_by !== auth()->user()->id) {
            if (!$loop) {
                createToast(__('Alert cannot be unlocked as it is locked by :username', [
                    'username' => User::find($alert->locked_by)->name,
                ]), 'error');
            }
            return false;
        } else {
            $alert->locked_at = null;
            $alert->lockedBy()->dissociate();
            $alert->save();
        }
        return true;
    }

    public function read(OrchestratorConnectionTenantAlert $alert)
    {
        $done = $this->doRead($alert);
        if ($done) {
            createToast(__('Alert successfully read!'), 'success');
        }
        return redirect()->route('pending-alerts.index');
    }

    public function lock(OrchestratorConnectionTenantAlert $alert)
    {
        $done = $this->doLock($alert);
        if ($done) {
            createToast(__('Alert successfully locked!'), 'success');
        }
        return redirect()->route('pending-alerts.index');
    }

    public function unlock(OrchestratorConnectionTenantAlert $alert)
    {
        $done = $this->doUnlock($alert);
        if ($done) {
            createToast(__('Alert successfully unlocked!'), 'success');
        }
        return redirect()->route('pending-alerts.index');
    }

    private function bulkAction(HttpRequest $request, $action, $verb)
    {
        $selected = $request->selected;
        $count = 0;
        foreach ($selected as $alert) {
            $done = false;
            switch($action) {
                case 'read':
                    $done = $this->doRead(OrchestratorConnectionTenantAlert::find($alert), true);
                    break;
                case 'lock':
                    $done = $this->doLock(OrchestratorConnectionTenantAlert::find($alert), true);
                    break;
                case 'unlock':
                    $done = $this->doUnlock(OrchestratorConnectionTenantAlert::find($alert), true);
                    break;
            }
            $done && $count++;
        }
        
        $message = ":count / :total alert successfully $verb!";
        $style = 'success';
        if ($count > 1) {
            $message = ":count / :total alerts successfully $verb!";
        }
        if ($count < count($selected)) {
            $style = 'warning';
        } elseif ($count === 0) {
            $style = 'error';
        }
        createToast(__($message, [
            'count' => $count,
            'total' => count($selected),
        ]), $style);

        return redirect()->route('pending-alerts.index');
    }

    public function bulkRead(HttpRequest $request)
    {
        $this->bulkAction($request, 'read', 'read');
    }

    public function bulkLock(HttpRequest $request)
    {
        $this->bulkAction($request, 'lock', 'locked');
    }

    public function bulkUnlock(HttpRequest $request)
    {
        $this->bulkAction($request, 'unlock', 'unlocked');
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
