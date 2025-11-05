<?php

namespace App\Providers;

use App\Models\Subscription\SubscriptionMonthMember;
use App\Observers\SubscriptionMonthMemberObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        SubscriptionMonthMember::observe(SubscriptionMonthMemberObserver::class);
    }
}
