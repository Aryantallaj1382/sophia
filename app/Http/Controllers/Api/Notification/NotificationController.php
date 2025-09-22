<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = UserNotification::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);
        $notifications->getCollection()->transform(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->message,
                'date' => $notification->created_at->format('d F'),
                'time' => $notification->created_at->format('H:i'),
            ];
        });

        return api_response($notifications);
    }
}
