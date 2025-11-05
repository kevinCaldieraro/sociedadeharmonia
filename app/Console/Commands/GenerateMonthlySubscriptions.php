<?php

namespace App\Console\Commands;

use App\Models\Member\Member;
use App\Models\Subscription\SubscriptionMonthMember;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlySubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-monthly-subscriptions';

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
        $currDate = Carbon::now();
        $expiration_date = Carbon::createFromDate($currDate->year, $currDate->month, 15);

        $members = Member::with(['role', 'subscription'])
            ->whereHas('role', function ($query) {
                $query->where('status', 'ativo');
            })
            ->get();

        $count = 0;

        foreach ($members as $member) {
            try {
                $subscription = $member->subscription;

                if (!$subscription) continue;

                $exists = SubscriptionMonthMember::where('subscription_member_id', $subscription->id)
                    ->where('month', $currDate->month)
                    ->where('year', $currDate->year)
                    ->exists();

                if ($exists) continue;

                SubscriptionMonthMember::create([
                    'subscription_member_id' => $subscription->id,
                    'expiration_date' => $expiration_date,
                    'month' => $currDate->month,
                    'year' => $currDate->year,
                    'status' => 'pendente'
                ]);

                $count++;
            } catch (\Exception $e) {
                \Log::error("Erro ao gerar mensalidade para membro {$member->id}: {$e->getMessage()}");
            }
        }

        $this->info("Mensalidades geradas: $count");
    }
}
