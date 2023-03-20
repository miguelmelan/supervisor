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
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $orchestratorConnections = OrchestratorConnectionResource::collection(
            OrchestratorConnection::withCount('alerts')->get()->sortBy('code')
        );

        $orchestratorConnectionTenants = OrchestratorConnectionTenantResource::collection(
            OrchestratorConnectionTenant::withCount('alerts')->with('orchestratorConnection')->get()->sortBy('code')
        );

        $automatedProcesses = AutomatedProcessResource::collection(
            AutomatedProcess::withCount('alerts')->get()->sortBy('code')
        );

        $pendingAlertsCount = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()->sortBy('creation_time')->where('read_at', null)
        )->count();

        $closedAlertsCount = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()->sortBy('read_at')->where('read_at', !null)
        )->count();

        $mostAlertingAutomatedProcesses = AutomatedProcessResource::collection(
            AutomatedProcess::withCount('alerts')->orderBy('alerts_count', 'desc')->get()
        );

        $alertsOverTime = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()->where('creation_time', '>=', Carbon::now()->subDays(9))
        )->collection->sortBy('creation_time')->groupBy([
            function ($alert) {
                return Carbon::parse($alert->creation_time)->format('d/m/Y');
            },
            function ($alert) {
                return Carbon::parse($alert->creation_time)->format('H:00');
            },
        ]);

        $periods = 7;

        $alertsEveryFifteenMinutes = $this->getAlertsEveryFifteenMinutes($periods);
        $alertsEveryHour = $this->getAlertsEveryHour($periods);
        $alertsEveryFourHours = $this->getAlertsEveryFourHours($periods);
        $alertsEveryday = $this->getAlertsEveryday($periods);
        $alertsEveryWeek = $this->getAlertsEveryWeek($periods);
        $alertsEveryMonth = $this->getAlertsEveryMonth($periods);

        return Inertia::render('Dashboard/Overview/Index', [
            'orchestratorConnections' => $orchestratorConnections,
            'orchestratorConnectionTenants' => $orchestratorConnectionTenants,
            'automatedProcesses' => $automatedProcesses,
            'pendingAlertsCount' => $pendingAlertsCount,
            'closedAlertsCount' => $closedAlertsCount,
            'mostAlertingAutomatedProcesses' => $mostAlertingAutomatedProcesses,
            'alerts' => [
                'overTime' => $alertsOverTime,
                'everyFifteenMinutes' => [
                    'data' => $alertsEveryFifteenMinutes['data'],
                    'categories' => $alertsEveryFifteenMinutes['categories'],
                ],
                'everyHour' => [
                    'data' => $alertsEveryHour['data'],
                    'categories' => $alertsEveryHour['categories'],
                ],
                'everyFourHours' => [
                    'data' => $alertsEveryFourHours['data'],
                    'categories' => $alertsEveryFourHours['categories'],
                ],
                'everyday' => [
                    'data' => $alertsEveryday['data'],
                    'categories' => $alertsEveryday['categories'],
                ],
                'everyWeek' => [
                    'data' => $alertsEveryWeek['data'],
                    'categories' => $alertsEveryWeek['categories'],
                ],
                'everyMonth' => [
                    'data' => $alertsEveryMonth['data'],
                    'categories' => $alertsEveryMonth['categories'],
                ],
            ],
        ]);
    }

    private function getAlertsEveryFifteenMinutes($periods)
    {
        $interval = 15;
        $format = 'H:i';

        $currentDateRounded = roundToNearestMinuteInterval(Carbon::now());
        $lowerLimitDate = $currentDateRounded->subMinutes($interval * $periods);
        $collection = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()->where('creation_time', '>=', $lowerLimitDate)
        )->collection->sortBy('creation_time');
        $data = $collection->groupBy(function ($alert) use ($format) {
            return roundToNearestMinuteInterval(Carbon::parse($alert->creation_time))
                ->format($format);
        });
        $categories = array_map(function ($index) use ($interval, $format, $lowerLimitDate) {
            return $lowerLimitDate->copy()->addMinutes($interval * $index)->format($format);
        }, range(0, $periods));

        return [
            'data' => $data,
            'categories' => $categories,
        ];
    }

    private function getAlertsEveryHour($periods)
    {
        $format = 'H:00';

        $currentDateRounded = roundToNearestMinuteInterval(Carbon::now(), 60);
        $lowerLimitDate = $currentDateRounded->subHours($periods);
        $collection = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()->where('creation_time', '>=', $lowerLimitDate)
        )->collection->sortBy('creation_time');
        $data = $collection->groupBy(function ($alert) use ($format) {
            return Carbon::parse($alert->creation_time)
                ->format($format);
        });
        $categories = array_map(function ($index) use ($format, $lowerLimitDate) {
            return $lowerLimitDate->copy()->addHours($index)->format($format);
        }, range(0, $periods));

        return [
            'data' => $data,
            'categories' => $categories,
        ];
    }

    private function getAlertsEveryFourHours($periods)
    {
        $interval = 4;
        $format = 'd/m H:00';

        $currentDateRounded = roundToNearestMinuteInterval(Carbon::now(), 60);
        $lowerLimitDate = $currentDateRounded->subHours($interval * $periods);
        $collection = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()->where('creation_time', '>=', $lowerLimitDate)
        )->collection->sortBy('creation_time');
        $data = $collection->groupBy(function ($alert) use ($format) {
            return roundToNearestMinuteInterval(Carbon::parse($alert->creation_time), 60)
                ->format($format);
        });
        $categories = array_map(function ($index) use ($interval, $format, $lowerLimitDate) {
            return $lowerLimitDate->copy()->addHours($interval * $index)->format($format);
        }, range(0, $periods));

        return [
            'data' => $data,
            'categories' => $categories,
        ];
    }

    private function getAlertsEveryday($periods)
    {
        $format = 'd/m';

        $currentDate = Carbon::now();
        $lowerLimitDate = $currentDate->setHours(0)->setMinutes(0)->setSeconds(0)->subDays($periods);
        $collection = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()->where('creation_time', '>=', $lowerLimitDate)
        )->collection->sortBy('creation_time');
        $data = $collection->groupBy(function ($alert) use ($format) {
            return Carbon::parse($alert->creation_time)
                ->format($format);
        });
        $categories = array_map(function ($index) use ($format, $lowerLimitDate) {
            return $lowerLimitDate->copy()->addDays($index)->format($format);
        }, range(0, $periods));

        return [
            'data' => $data,
            'categories' => $categories,
        ];
    }

    private function getAlertsEveryWeek($periods)
    {
        $format = 'd/m - W';

        $currentDate = Carbon::now()->startOfWeek();
        $lowerLimitDate = $currentDate->subWeeks($periods);
        $collection = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()->where('creation_time', '>=', $lowerLimitDate)
        )->collection->sortBy('creation_time');
        $data = $collection->groupBy(function ($alert) use ($format) {
            return Carbon::parse($alert->creation_time)
                ->format($format);
        });
        $categories = array_map(function ($index) use ($format, $lowerLimitDate) {
            return $lowerLimitDate->copy()->addWeeks($index)->format($format);
        }, range(0, $periods));

        return [
            'data' => $data,
            'categories' => $categories,
        ];
    }

    private function getAlertsEveryMonth($periods)
    {
        $format = 'MMM';

        $currentDate = Carbon::now()->startOfMonth();
        $lowerLimitDate = $currentDate->subMonths($periods);
        $collection = OrchestratorConnectionTenantAlertResource::collection(
            OrchestratorConnectionTenantAlert::all()->where('creation_time', '>=', $lowerLimitDate)
        )->collection->sortBy('creation_time');
        $data = $collection->groupBy(function ($alert) use ($format) {
            return ucfirst(Carbon::parse($alert->creation_time)
                ->isoFormat($format));
        });
        $categories = array_map(function ($index) use ($format, $lowerLimitDate) {
            return ucfirst($lowerLimitDate->copy()->addMonths($index)->isoFormat($format));
        }, range(0, $periods));

        return [
            'data' => $data,
            'categories' => $categories,
        ];
    }
}
