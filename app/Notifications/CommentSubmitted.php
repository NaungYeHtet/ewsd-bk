<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\PusherPushNotifications\PusherChannel;
use NotificationChannels\PusherPushNotifications\PusherMessage;

class CommentSubmitted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Comment $comment)
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
            'database'
        ];
    }

    public function toPushNotification($notifiable)
    {
        return PusherMessage::create()
            ->web()
            ->badge(1)
            ->sound('success')
            ->title("{$this->comment->staff->name} commented your idea.")
            ->body($this->comment->content);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Comment Submitted')
            ->greeting("Hello {$notifiable->name} | {$notifiable->department->name}")
            ->line("{$this->comment->staff->name} commented your idea.")
            ->action('View Comment', config('app.frontend_url').'/ideas/'.$this->comment->commentable->slug.'/comments#'.$this->comment->uuid)
            ->line($this->comment->content);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Comment Submitted',
            'body' => "{$this->comment->staff->name} commented your idea.",
            'link' => '/ideas/'.$this->comment->commentable->slug.'/comments#'.$this->comment->uuid,
            'icon' => 'message-circle-more',
        ];
    }
}
