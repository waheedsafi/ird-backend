<?php

namespace App\Traits;

use App\Models\AddressTran;
use Illuminate\Support\Facades\App;

trait CacheKeyTrait
{
    private function getKey($key)
    {
        $locale = App::getLocale();
        return $key . "_{$locale}";
    }
}
