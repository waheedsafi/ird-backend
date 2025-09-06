<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Role\RoleRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\Redis\RedisRepository;
use App\Repositories\Storage\StorageRepository;
use App\Repositories\Approval\ApprovalRepository;
use App\Repositories\Director\DirectorRepository;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Redis\RedisRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepository;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\Notification\NotificationRepository;
use App\Repositories\Organization\OrganizationRepository;
use App\Repositories\Approval\ApprovalRepositoryInterface;
use App\Repositories\Director\DirectorRepositoryInterface;
use App\Repositories\Representative\RepresentativeRepository;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;
use App\Repositories\Notification\NotificationRepositoryInterface;
use App\Repositories\Organization\OrganizationRepositoryInterface;
use App\Repositories\Representative\RepresentativeRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(ApprovalRepositoryInterface::class, ApprovalRepository::class);
        $this->app->bind(RedisRepositoryInterface::class, RedisRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(RepresentativeRepositoryInterface::class, RepresentativeRepository::class);
        $this->app->bind(DirectorRepositoryInterface::class, DirectorRepository::class);
        $this->app->bind(StorageRepositoryInterface::class, StorageRepository::class);
        $this->app->bind(OrganizationRepositoryInterface::class, OrganizationRepository::class);
        $this->app->bind(PendingTaskRepositoryInterface::class, PendingTaskRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
