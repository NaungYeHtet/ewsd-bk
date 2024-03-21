<?php

namespace App\Notifications;

use App\Models\Idea;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\PusherPushNotifications\PusherChannel;
use NotificationChannels\PusherPushNotifications\PusherMessage;

class IdeaSubmitted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Idea $idea)
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
            // PusherChannel::class,
            // 'mail',
            'database',
        ];
    }

    public function toPushNotification($notifiable)
    {
        return PusherMessage::create()
            ->web()
            ->badge(1)
            ->sound('success')
            ->title("A new idea has been submitted by {$this->idea->staff->name}.")
            ->body($this->idea->title);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Idea Submitted')
            ->greeting("Hello {$notifiable->name} | {$this->idea->department->name}")
            ->line("A new idea has been submitted by {$this->idea->staff->name}.")
            ->line($this->idea->title)
            ->line($this->idea->content)
            ->action('View Idea', config('app.frontend_url').'/ideas/'.$this->idea->slug);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Idea Submitted',
            'message' => "A new idea has been submitted by {$this->idea->staff->name}.",
            'link' => '/ideas/'.$this->idea->slug,
            'icon' => 'message-circle-question',
        ];
    }
}
