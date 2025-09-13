<?php

namespace App\Traits;

use App\Jobs\SendMailJob;

trait EmailTrait
{
    private function sendEmailToOrganization($email, $body, $subject)
    {
        $org = [
            ["email" => $email, "name" => "Organization"],
        ];
        $this->sendEmail($org, $body, $subject);
    }
    private function sendEmail(array $data, $body, $subject)
    {
        $delaySeconds = 2;
        $counter = 0;

        foreach ($data as $emailData) {
            SendMailJob::dispatch($emailData['email'], $emailData['name'], $body, $subject)
                ->delay(now()->addSeconds($counter));

            $counter += $delaySeconds;
        }
    }
}
