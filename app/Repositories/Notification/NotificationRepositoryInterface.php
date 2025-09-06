<?php

namespace App\Repositories\Notification;

interface NotificationRepositoryInterface
{
    /**
     * Stores and sends notification to all users.
     * 
     *
     * @param string $notifier_id
     * @param array $message
     * @param string $action_url
     * @param string $context
     * @param string $permission
     */
    public function sendStoreNotification($notifier_id, $message, $action_url, $context, $permission, $permissionName);

    /**
     * Stores and sends notification to specific user.
     * 
     *
     * @param string $notifier_id
     * @param array $message
     * @param string $action_url
     * @param string $context
     * @param string $user_id
     */
    public function sendStoreUniqueNotification($notifier_id, $message, $action_url, $context, $permissionName, $user_id);
}
