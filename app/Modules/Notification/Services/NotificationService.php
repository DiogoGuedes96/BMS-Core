<?php

namespace App\Modules\Notification\Services;

use App\Modules\Notification\Models\Notifications;
use Illuminate\Notifications\Notification;

class NotificationService
{
    public function addNotification($userId, $reason, $message, $notifiableType, $notifiableId)
    {
        $notification = new Notifications();

        $notification->user_id = $userId;
        $notification->reason = $reason;
        $notification->message = $message;
        $notification->notifiable_type = get_class($notifiableType);
        $notification->notifiable_id = $notifiableId;

        $notification->save();
    }

    public function markAsRead($notificationId, $read = true)
    {
        $notification = Notifications::find($notificationId);
        $notification->read = true;
        $notification->save();
    }

    public function markAllAsRead($userId)
    {
        Notifications::where('user_id', $userId)
            ->update(['read' => true]);
    }

    /**
     * Change the status of a notification and set read to true.
     *
     * @param int $notificationId The ID of the notification to change.
     * @param string $status The new status of the notification. Must be 'pending', 'accepted', or 'rejected'.
     * @throws \InvalidArgumentException If an invalid status is provided.
     */
    public function changeStatus($notificationId, string $status)
    {
        $notification = Notifications::find($notificationId);
        $notification->status = $status;
        $notification->read = true;
        $notification->save();
    }

    public function listNotifications($userId)
    {
        return Notifications::where('user_id', $userId)
                ->with('notifiable')
                ->with(['notifiable.client' => function ($query) {
                    $query->without('client');
                }])
                ->orderBy('created_at', 'desc')
                ->get();
    }
    
    public function getNotificationRequestedByUser($notificationId)
    {
        return Notifications::where('id', $notificationId)
                ->with('notifiable')
                ->with(['notifiable.client' => function ($query) {
                    $query->without('client');
                }])
                ->with(['notifiable.requestedByUser' => function ($query) {
                        $query->without('requestedByUser');
                }])
                ->first();
    }
}
