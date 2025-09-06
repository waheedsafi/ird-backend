<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use App\Enums\Types\ApplicationEnum;

trait ApplicationTrait
{
    // filter function
    protected function approvable()
    {
        $approvable = DB::table('applications as a')
            ->where('a.id', '=', ApplicationEnum::user_approval->value)
            ->select('a.value')
            ->first();
        return $approvable->value == 'true';
    }
}
