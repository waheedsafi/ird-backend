<?php

namespace App\Repositories\Redis;

interface RedisRepositoryInterface
{
    /**
     * Stores user permissions name and id in redis.
     * 
     *
     * @param array  groups
     * @param mixed user_id
     * @return bool
     */
    public function storeUserPermissions(array $groups, $user_id);
}
