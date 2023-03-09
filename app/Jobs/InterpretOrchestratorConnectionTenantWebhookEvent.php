<?php

namespace App\Jobs;

use App\Events\OrchestratorConnectionTenantAlertCreated;
use App\Models\OrchestratorConnectionTenant;
use App\Services\UiPathOrchestratorService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InterpretOrchestratorConnectionTenantWebhookEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public OrchestratorConnectionTenant $tenant;
    public $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenant, $data)
    {
        $this->tenant = $tenant;
        $this->data = json_decode($data);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(UiPathOrchestratorService $service)
    {
        $type = $this->data->Type;
        switch($type) {
            case 'job.faulted':
                $this->handleJobFaultedEvent();
                break;
            /* case 'schedule.failed':
                $this->handleScheduleFailedEvent();
                break; */
            case 'queueItem.transactionFailed':
                $this->handleQueueItemTransactionFailed();
                break;
            default:
                Log::error("Type of webhook event not managed: $type");
        }
    }

    /**
     * Manages job faulted events.
     *
     * @return void
     */
    private function handleJobFaultedEvent()
    {
        Log::info('Managing job faulted event');
        $notificationName = 'Process.JobExecution.Faulted';
        $component = 'Jobs';
        $severity = 'Error';
        $creationTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u', $this->data->Timestamp);

        $alert = $this->tenant->alerts()->create([
            'notification_name' => $notificationName,
            'data' => json_encode($this->data),
            'component' => $component,
            'severity' => $severity,
            'creation_time' => $creationTime,
            'state' => 'Unread',
        ]);

        OrchestratorConnectionTenantAlertCreated::dispatch($alert);

        Log::info("New alert created: $alert->id");
    }

    /**
     * Manages schedule failed events
     * 
     * @return void
     */
    private function handleScheduleFailedEvent()
    {
        Log::info('Managing schedule failed event');
        $notificationName = '';
        $component = '';
        $severity = '';
        $creationTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u', $this->data->Timestamp);

        $alert = $this->tenant->alerts()->create([
            'notification_name' => $notificationName,
            'data' => json_encode($this->data),
            'component' => $component,
            'severity' => $severity,
            'creation_time' => $creationTime,
            'state' => 'Unread',
        ]);

        OrchestratorConnectionTenantAlertCreated::dispatch($alert);

        Log::info("New alert created: $alert->id");
    }

    /**
     * Manages queue item transaction failed
     * 
     * @return void
     */
    private function handleQueueItemTransactionFailed()
    {
        Log::info('Managing queue item transaction failed event');
        if ($this->data->QueueItem->ProcessingException->Type === 'ApplicationException') {
            $notificationName = 'Queue.TransactionStatus.FailedWithApplicationException';
            $severity = 'Error';
        } elseif ($this->data->QueueItem->ProcessingException->Type === 'BusinessException') {
            $notificationName = 'Queue.TransactionStatus.FailedWithBusinessException';
            $severity = 'Warn';
        }
        $component = 'Transactions';
        $creationTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u', $this->data->Timestamp);

        $alert = $this->tenant->alerts()->create([
            'notification_name' => $notificationName,
            'data' => json_encode($this->data),
            'component' => $component,
            'severity' => $severity,
            'creation_time' => $creationTime,
            'state' => 'Unread',
        ]);

        OrchestratorConnectionTenantAlertCreated::dispatch($alert);

        Log::info("New alert created: $alert->id");
    }
}
