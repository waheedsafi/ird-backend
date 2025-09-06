<?php

namespace App\Repositories\Notification;

use App\Jobs\SendNotificationJob;
use App\Jobs\StoreNotificationJob;
use App\Jobs\StoreUniqueNotificationJob;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function sendStoreNotification($notifier_id, $message, $action_url, $context, $permission, $permissionName)
    {
        $user = request()->user();

        $data = [
            'notifier_id' => $notifier_id,
            'message' => $message,
            'action_url' => $action_url,
            'context' => $context,
            'created_at' => now(),
            'permissionName' => $permissionName,
        ];
        $requestDetail = [
            'user_id' => $user?->id ?? 'N/K',
            'username' => $user?->username ?? 'N/K',
            'ip_address' => request()->ip() ?? 'N/K',
            'method' => request()->method() ?? 'N/K',
            'uri' => request()->fullUrl() ?? 'N/K',
        ];
        // 1. Store notification for authorized users
        StoreNotificationJob::dispatch(
            $data,
            $permission,
            $requestDetail
        );
        // 2. Send to express to give them notification
        $notificationUrl = env('NOTIFICATION_URL', 'http://localhost:8001/api/v1');

        SendNotificationJob::dispatch(
            "{$notificationUrl}/notification",
            $data,
            $requestDetail
        );
    }
    public function sendStoreUniqueNotification($notifier_id, $message, $action_url, $context, $permissionName, $user_id)
    {
        $data = [
            'notifier_id' => $notifier_id,
            'message' => $message,
            'action_url' => $action_url,
            'user_id' => $user_id,
            'context' => $context,
            'created_at' => now(),
            'permissionName' => $permissionName,
        ];
        $user = request()->user();
        $requestDetail = [
            'user_id' => $user?->id ?? 'N/K',
            'username' => $user?->username ?? 'N/K',
            'ip_address' => request()->ip() ?? 'N/K',
            'method' => request()->method() ?? 'N/K',
            'uri' => request()->fullUrl() ?? 'N/K',
        ];
        // 1. Store notification for authorized users
        StoreUniqueNotificationJob::dispatch(
            $data,
            $user_id,
            $requestDetail
        );
        // 2. Send to express to give them notification
        $notificationUrl = env('NOTIFICATION_URL', 'http://localhost:8001/api/v1');

        SendNotificationJob::dispatch(
            "{$notificationUrl}/notification/unique",
            $data,
            $requestDetail
        );
    }
}
