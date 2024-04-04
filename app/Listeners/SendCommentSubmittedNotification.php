<?php

namespace App\Listeners;

use App\Events\CommentSubmitted;
use App\Models\Staff;
use Illuminate\Support\Facades\Notification;

class SendCommentSubmittedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentSubmitted $event): void
    {
        $staff = $event->comment->commentable->staff;

        // $staff = Staff::where('email', 'naungyehtet717@gmail.com')->first();
        Notification::send($staff, new \App\Notifications\CommentSubmitted($event->comment));
    }
}
