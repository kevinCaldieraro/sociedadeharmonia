<?php

namespace App\Services\Subscription;

use App\Entities\Subscription\Subscription;
use App\Models\Subscription\SubscriptionMonthMember;
use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class SubscriptionService
{
    public function registerPayment(array $data): SubscriptionMonthMember
    {
        $subscriptionEntity = new Subscription($data);
        $subscriptionMonthMember = $subscriptionEntity->registerPayment();
        $subscriptionMonthMember = $subscriptionMonthMember->fresh([
            'subscription:id,member_id,last_paid_at',
            'subscription.member:id,name,cpf'
        ]);

        return $subscriptionMonthMember;
    }

    public function exemptMonth($id): SubscriptionMonthMember
    {
        $subscriptionEntity = new Subscription(['id' => $id]);
        $subscriptionMonthMember = $subscriptionEntity->exemptMonth();
        $subscriptionMonthMember = $subscriptionMonthMember->fresh([
            'subscription:id,member_id,last_paid_at',
            'subscription.member:id,name,cpf'
        ]);

        return $subscriptionMonthMember;
    }
}