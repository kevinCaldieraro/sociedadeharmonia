<?php

namespace App\Listeners;

use App\Events\SubscriptionMonthMemberUpdated;
use Carbon\Carbon;

class UpdateMemberStatus
{
    public function handle(SubscriptionMonthMemberUpdated $event): void
    {
        $subscription = $event->subscriptionMonthMember->subscription;

        $hasExpiredMonth = $subscription->months()
            ->where('status', 'vencida')
            ->exists();

        $irregularSince = null;

        if ($hasExpiredMonth && $subscription->irregular_since == null) {
            $firstMonth = $subscription->months()
                ->where('status', 'vencida')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->first();
            $monthPadded = str_pad($firstMonth->month, 2, '0', STR_PAD_LEFT);
            $irregularSince = Carbon::createFromFormat('Y-m-d', "{$firstMonth->year}-{$monthPadded}-16")
                ->format('Y-m-d');
        }

        if (!$hasExpiredMonth) {
            $irregularSince = null;
        }

        $subscription->update([
            'status' => $hasExpiredMonth ? 'irregular' : 'regular',
            'irregular_since' => $irregularSince
        ]);
    }
}