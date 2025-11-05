<?php

namespace App\Observers;

use App\Events\SubscriptionMonthMemberUpdated;
use App\Models\Subscription\SubscriptionMonthMember;

class SubscriptionMonthMemberObserver
{
    public function updated(SubscriptionMonthMember $subscriptionMonthMember): void
    {
        event(new SubscriptionMonthMemberUpdated($subscriptionMonthMember));
    }
}
