<?php

namespace App\Http\Controllers;

use App\Data\NotificationData;
use App\Models\Staff;
use Illuminate\Http\Request;
use Spatie\LaravelData\PaginatedDataCollection;

class NotificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        $staff = Staff::find(auth()->id());

        return $this->responseSuccess([
            'unread_count' => $staff->unreadNotifications()->count(),
            'notifications' => NotificationData::collect($staff->notifications()->paginate(10), PaginatedDataCollection::class),
        ]);
    }

    public function read(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:notifications,id',
        ]);

        $notification = Staff::find(auth()->id())->notifications()->find($request->id);

        $notification->markAsRead();

        return $this->responseSuccess();
    }

    public function readAll(Request $request)
    {
        Staff::find(auth()->id())->unreadNotifications->markAsRead();

        return $this->responseSuccess();
    }
}
