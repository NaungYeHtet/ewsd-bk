<?php

namespace App\Listeners;

use App\Events\IdeaSubmitted;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;

class SendIdeaSubmittedNotification
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
    public function handle(IdeaSubmitted $event): void
    {
        $department = $event->idea->staff->department;

        $coordinator = $department->staffs()->whereHas('roles', function (Builder $query) {
            $query->where('roles.name', 'QA Coordinator');
        })->first();

        if($coordinator){
            Notification::send($coordinator, new \App\Notifications\IdeaSubmitted($event->idea));
        }
        // $coordinator = Staff::where('email', 'naungyehtet717@gmail.com')->first();
    }
}
