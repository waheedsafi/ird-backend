<?php

namespace App\Jobs;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $url;
    public $data;
    public $requestDetail;
    public $tries = 5;       // More attempts
    public $timeout = 15;    // Increased timeout in seconds
    /**
     * Create a new job instance.
     */
    public function __construct($url, $data, $requestDetail)
    {
        $this->url = $url;
        $this->data = $data;
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
        $logData = null;
        try {
            $response = Http::post($this->url, $this->data);

            if (!$response->ok()) {
                $logData = [
                    'error_code' => $response->status(),
                    'trace' => json_encode($this->data),
                    'exception_type' => 'N/K',
                    'error_message' => $response->body(),
                    'user_id' => $this->requestDetail['user_id'],
                    'username' =>  $this->requestDetail['username'],
                    'method' => 'POST',
                    'uri' => $this->url,
                ];
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
        }

        if ($logData) {
            LogErrorJob::dispatch($logData);
            Log::info('SendNotificationJob =>' . json_encode($logData));
        }
    }
}
