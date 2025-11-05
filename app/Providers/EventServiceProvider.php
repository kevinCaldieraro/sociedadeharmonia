<?php

namespace App\Providers;

use App\Events\SubscriptionMonthMemberUpdated;
use App\Listeners\UpdateMemberStatus;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SubscriptionMonthMemberUpdated::class => [
            UpdateMemberStatus::class,
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
