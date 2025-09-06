<?php

namespace App\Repositories\Redis;

use App\Enums\Permissions\RoleEnum;
use Illuminate\Support\Facades\Redis;

class RedisRepository implements RedisRepositoryInterface
{
    public function storeUserPermissions(array $groups, $user_id)
    {
        if (!is_array($groups)) {
            return false;
        }
        foreach ($groups as $key => $value) {
            if (!is_int($key) || !is_string($value)) {
                return false;
            }
        }
        $keyPrefix = env('REDIS_USER_PERMISSIONS', 'user_permissions:');
        $key = $keyPrefix . $user_id;

        // Store the array as JSON string
        Redis::set($key, json_encode($groups));

        return true;
    }
}
