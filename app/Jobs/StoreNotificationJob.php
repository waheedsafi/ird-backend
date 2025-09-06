<?php

namespace App\Jobs;

use Exception;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Enums\Languages\LanguageEnum;
use App\Models\NotificationTrans;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $data;
    public $permission;
    public $requestDetail;

    /**
     * Number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 10;
    /**
     * Create a new job instance.
     */
    public function __construct($data, $permission, $requestDetail)
    {
        $this->data = $data;
        $this->permission = $permission;
        $this->requestDetail = $requestDetail;
    }
    public function backoff(): int
    {
        return 5; // Retry after 5 seconds
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $users = DB::table('users as u')
                ->join('role_permissions as rp', function ($join) {
                    $join->on('u.role_id', '=', 'rp.role')
                        ->where('rp.permission', $this->permission);
                })
                ->select('u.id')
                ->get();

            foreach ($users as $user) {
                $notification = Notification::create([
                    'user_id' => $user->id,
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
            Log::info('Global Exception =>' . json_encode($logData));
        }
    }
}
