<?php

namespace App\Entities\Subscription;

use App\Events\SubscriptionMonthMemberUpdated;
use App\Models\Subscription\SubscriptionMember;
use App\Models\Subscription\SubscriptionMonthMember;
use App\Repositories\Subscription\SubscriptionRepository;
use App\Traits\ConvertsDateAttributes;
use Carbon\Carbon;

class Subscription
{
    use ConvertsDateAttributes;
    protected array $data;
    protected SubscriptionRepository $repository;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->repository = new SubscriptionRepository();
    }

    public function save()
    {
        $subscription = SubscriptionMember::create($this->data);
        $date = Carbon::now();
        $joinDate = Carbon::createFromFormat('Y-m-d', $this->data['join_date']);

        if ($joinDate->day > 15) {
            $joinDate = $joinDate->addMonth();
        }

        for (
            $currDate = $date;
            $joinDate->lessThanOrEqualTo($currDate);
            $joinDate->addMonth()
        ) {
            $status = 'pendente';
            $expiration_date = Carbon::create(
                $joinDate->year,
                $joinDate->month,
                15
            );

            if ($joinDate->month < $currDate->month || $date->day > 15 || $joinDate->year < $currDate->year) {
                $status = 'vencida';
            }
    
            SubscriptionMonthMember::create([
                'subscription_member_id' => $subscription->id,
                'expiration_date' => $expiration_date,
                'month' => $joinDate->month,
                'year' => $joinDate->year,
                'status' => $status
            ]);
        }

        event(new SubscriptionMonthMemberUpdated(
            $subscription->months()->latest('id')->first()
        ));
    }

    public function registerPayment(): SubscriptionMonthMember
    {
        return $this->repository->registerPayment([
            'id' => $this->data['id'],
            'idSubscription' => $this->data['idSubscription'],
            'value' => $this->data['value'],
            'paid_at' => $this->convertToDatabaseDate($this->data['paid_at']),
            'payment_method' => $this->data['payment_method'],
            'payment_proof_link' => $this->data['payment_proof_link'] ?? null,
            'status' => 'paga'
        ]);
    }

    public function exemptMonth(): SubscriptionMonthMember
    {
        return $this->repository->exemptMonth($this->data['id']);
    }
}