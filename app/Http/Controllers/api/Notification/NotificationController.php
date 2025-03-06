<?php

namespace App\Http\Controllers\api\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notification\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function notifications(): AnonymousResourceCollection
    {
        $data = Notification::query()
            ->where('notifiable_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return NotificationResource::collection($data);
    }
}
