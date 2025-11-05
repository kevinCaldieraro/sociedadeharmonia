<?php

namespace App\Console\Commands;

use App\Models\Member\Member;
use App\Models\Subscription\SubscriptionMember;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateExemptMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-exempt-members';

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
        $dateLimit = $currDate->copy()->subYears(25);
        $members = Member::with('role')
            ->whereHas('role', function ($query) {
                $query->where('type', 'affiliated');
                $query->where('status', 'ativo');
            })
            ->whereDate('birth_date', '<=', $dateLimit)->get();

        foreach ($members as $member) {
            $role = $member->role;

            if (!$role->is_exempt) continue;

            $role->update(['is_exempt' => false]);

            $exists = SubscriptionMember::where('member_id', $member->id)
                ->exists();

            if ($exists) continue;

            SubscriptionMember::create([
                'member_id' => $member->id,
                'price' => 79.9,
                'join_date' => $currDate->format('Y-m-d'),
                'status' => 'regular'
            ]);
        }

        $this->info('Membros com 25 anos ou mais atualizados com sucesso.');
    }
}
