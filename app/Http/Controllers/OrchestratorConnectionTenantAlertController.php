<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrchestratorConnectionTenantAlertRequest;
use App\Http\Resources\OrchestratorConnectionTenantAlertResource;
use App\Models\OrchestratorConnectionTenantAlert;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Carbon\Carbon;
use Inertia\Inertia;

class OrchestratorConnectionTenantAlertController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrchestratorConnectionTenantAlert  $alert
     * @return \Illuminate\Http\Response
     */
    public function edit(OrchestratorConnectionTenantAlert $alert)
    {
        $alert->load('comments');
        return Inertia::render('Dashboard/Alerts/Edit', [
            'alert' => new OrchestratorConnectionTenantAlertResource($alert),
        ]);
    }

    /**
     * Update the resolution details.
     *
     * @param  \App\Http\Requests\HttpRequest  $request
     * @param  \App\Models\OrchestratorConnectionTenantAlert  $alert
     * @return \Illuminate\Http\Response
     */
    public function updateResolutionDetails(HttpRequest $request, OrchestratorConnectionTenantAlert $alert)
    {
        $previousResolutionDetails = $alert->resolution_details;
        $previousFalsePositive = $alert->false_positive;

        $alert->resolution_details = $request->get('resolution_details');
        $alert->false_positive = $request->get('false_positive');
        $alert->save();
        
        if ($previousResolutionDetails !== $alert->resolution_details) {
            $comment = 'Resolution details updated. Made empty.';
            if ($alert->resolution_details) {
                $comment = 'Resolution details updated. New value: ' . $alert->resolution_details;
            }
            $alert->comment($comment);
        }

        if (!$previousFalsePositive || !$alert->false_positive) {
            $alert->comment(
                'False positive flag updated. New value: '
                . ($alert->false_positive ? 'enabled.' : 'disabled.')
            );
        }

        if ($request->get('mark_as_read')) {
            if ($this->doRead($alert)) {
                createToast(__('Alert successfully read!'), 'success');
            }
        }
        
        return redirect()->route('alerts.edit', [
            'alert' => $alert->id,
        ]);
    }

    public function comment(HttpRequest $request, OrchestratorConnectionTenantAlert $alert)
    {
        $alert->comment($request->get('comment'));
        createToast(__('Comment successfully added!'), 'success');

        return redirect()->route('alerts.edit', [
            'alert' => $alert->id,
        ]);
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
            $alert->comment('Alert read');
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
            $alert->comment('Alert locked');
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
            $alert->comment('Alert unlocked');
        }
        return true;
    }

    public function read(OrchestratorConnectionTenantAlert $alert)
    {
        $done = $this->doRead($alert);
        if ($done) {
            createToast(__('Alert successfully read!'), 'success');
        }
        return redirect()->back();
    }

    public function lock(OrchestratorConnectionTenantAlert $alert)
    {
        $done = $this->doLock($alert);
        if ($done) {
            createToast(__('Alert successfully locked!'), 'success');
        }
        return redirect()->back();
    }

    public function unlock(OrchestratorConnectionTenantAlert $alert)
    {
        $done = $this->doUnlock($alert);
        if ($done) {
            createToast(__('Alert successfully unlocked!'), 'success');
        }
        return redirect()->back();
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

        return redirect()->back();
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
}
