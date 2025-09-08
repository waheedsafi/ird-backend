<?php

namespace App\Jobs;

use Exception;
use App\Models\Notification;
use App\Models\NotificationTrans;
use Illuminate\Support\Facades\Log;
use App\Enums\Languages\LanguageEnum;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreUniqueNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $data;
    public $user_id;
    public $requestDetail;

    public $tries = 5;       // More attempts
    public $timeout = 15;    // Increased timeout in seconds
    /**
     * Create a new job instance.
     */
    public function __construct($data, $user_id, $requestDetail)
    {
        $this->data = $data;
        $this->user_id = $user_id;
        $this->requestDetail = $requestDetail;
    }
    /**
     * Retry delay between attempts (exponential backoff).
     */
    public function backoff(): array
    {
        return [5, 15, 30, 60]; // Custom backoff per attempt
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $notification = Notification::create([
                'user_id' => $this->user_id,
                'sender_id' => $this->requestDetail['user_id'],
                'notifier_type_id' => $this->data['notifier_id'],
                'action_url' => $this->data['action_url'],
                'context' => $this->data['context'], // Assuming DB column is JSON type
            ]);
            foreach (LanguageEnum::LANGUAGES as $code => $name) {
                NotificationTrans::create([
                    'notification_id' => $notification->id,
                    'language_name' => $code,
                    'message' => $this->data['message'][$code],
                ]);
            }
        } catch (Exception $err) {
            // Exception caught and the variable $err is used here
            $logData = [
                'error_code' => $err->getCode(),
                'trace' => $err->getTraceAsString(),
                'exception_type' => get_class($err),
                'error_message' => $err->getMessage(),
                'user_id' => $this->requestDetail['user_id'], // If you have an authenticated user, you can add the user ID
                'username' =>  $this->requestDetail['username'], // If you have an authenticated user, you can add the user ID
                'ip_address' =>  $this->requestDetail['ip_address'],
                'method' =>  $this->requestDetail['method'],
                'uri' =>  $this->requestDetail['uri'],
            ];
            LogErrorJob::dispatch($logData);
            Log::info('StoreUniqueNotificationJob =>' . json_encode($logData));
        }
    }
}
