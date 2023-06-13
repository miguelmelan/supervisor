<?php

namespace App\Notifications;

use App\Http\Resources\OrchestratorConnectionTenantAlertResource;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use \Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Support\Facades\Log;

class NewOrchestratorConnectionTenantAlert extends Notification
{
    use Queueable;

    protected OrchestratorConnectionTenantAlertResource $alert;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(OrchestratorConnectionTenantAlertResource $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        $url = route('alerts.edit', ['alert' => $this->alert->id]);
        $data = json_decode($this->alert->data);
        $fields = [
            'Reason' => $this->alert->trigger_id ? $data->reason : $data->Message,
        ];
        return (new SlackMessage)
            ->content(__('A new alert has been created!'))
            ->attachment(function ($attachment) use ($url, $fields) {
                $attachment
                    ->title(__('Direct link to the alert'), $url)
                    ->fields($fields);
            });
    }
}
