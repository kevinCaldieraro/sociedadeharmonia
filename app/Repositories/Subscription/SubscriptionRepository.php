<?php

namespace App\Repositories\Subscription;

use App\Models\Subscription\SubscriptionMember;
use App\Models\Subscription\SubscriptionMonthMember;

class SubscriptionRepository
{
    public function save()
    {

    }

    public function registerPayment(array $data): SubscriptionMonthMember
    {
        $subscriptionMember = SubscriptionMember::find($data['idSubscription']);
        $subscriptionMonth = SubscriptionMonthMember::find($data['id']);

        $subscriptionMonth->value = $data['value'];
        $subscriptionMonth->paid_at = $data['paid_at'];
        $subscriptionMonth->payment_method = $data['payment_method'];
        $subscriptionMonth->payment_proof_link = $data['payment_proof_link'];
        $subscriptionMonth->status = $data['status'];

        $subscriptionMonth->save();

        $subscriptionMember->last_paid_at = $data['paid_at'];
        $subscriptionMember->save();

        return $subscriptionMonth;
    }

    public function exemptMonth($id): SubscriptionMonthMember
    {
        $subscriptionMonth = SubscriptionMonthMember::find($id);
        $subscriptionMonth->status = 'isenta';
        $subscriptionMonth->save();

        return $subscriptionMonth;
    }
}