<?php

namespace App\Jobs;

use App\Mail\SendMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public string $email;
    public string $name;
    public string $body;
    public string $subject;
    public $tries = 5;       // More attempts
    public $timeout = 15;    // Increased timeout in seconds

    public function __construct($email, $name, $body, $subject)
    {
        $this->email = $email;
        $this->name = $name;
        $this->body = $body;
        $this->subject = $subject;
    }
    /**
     * Retry delay between attempts (exponential backoff).
     */
    public function backoff(): array
    {
        return [5, 15, 30, 60]; // Custom backoff per attempt
    }

    public function handle()
    {
        Mail::to($this->email)
            ->send(new SendMail($this->body, $this->subject, $this->name));
    }
}
