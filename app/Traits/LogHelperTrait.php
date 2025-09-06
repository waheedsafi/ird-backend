<?php

namespace App\Traits;

use Jenssegers\Agent\Agent;
use App\Jobs\LogUserLoginJob;

trait LogHelperTrait
{
    public function storeUserLog($userable_id, $userable_type, $action)
    {
        $ipAddress = request()->ip();
        $agent = new Agent();
        $platform = $agent->platform();
        $browser = $agent->browser();
        LogUserLoginJob::dispatch(
            $userable_id,
            $userable_type,
            $action,
            $ipAddress,
            $browser,
            $platform,
        );
    }
}
