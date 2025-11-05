<?php

namespace App\Events;

use App\Models\Subscription\SubscriptionMonthMember;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionMonthMemberUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(public SubscriptionMonthMember $subscriptionMonthMember) { }
}