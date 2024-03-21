<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\PusherPushNotifications\PusherChannel;
use NotificationChannels\PusherPushNotifications\PusherMessage;

class ReportSubmitted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Report $report)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            PusherChannel::class,
            'database',
        ];
    }

    public function toPushNotification($notifiable)
    {
        return PusherMessage::create()
            ->web()
            ->badge(1)
            ->sound('success')
            ->body("A report has been submitted by {$this->report->staff->name}.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Report Submitted',
            'body' => "A report has been submitted by {$this->report->staff->name}.",
            'link' => config('app.frontend_url').'/reports/'.$this->report->uuid,
            'icon' => 'circle-alert',
        ];
    }
}
