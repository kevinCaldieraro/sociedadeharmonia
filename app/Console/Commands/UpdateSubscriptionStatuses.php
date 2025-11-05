<?php

namespace App\Console\Commands;

use App\Models\Subscription\SubscriptionMember;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateSubscriptionStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-subscription-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando atualização do status das assinaturas...');
        $subscriptions = SubscriptionMember::all();

        foreach ($subscriptions as $subscription) {
            $hasExpiredMonth = $subscription->months()
                ->where('status', 'vencida')
                ->exists();

            $irregularSince = $subscription->irregular_since;

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

        $this->info('Concluído.');
    }
}
