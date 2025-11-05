<?php

namespace Database\Seeders;

use App\Models\Member\MemberRole;
use App\Models\Subscription\SubscriptionMember;
use App\Models\Subscription\SubscriptionMonthMember;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MemberRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MemberRole::factory()
            ->count(50)
            ->patrimonial()
            ->create()
            ->each(function ($patrimonial) {
                // Cônjuge
                MemberRole::factory()->spouse($patrimonial->member_id)->create();

                // Agregados
                MemberRole::factory()
                    ->count(rand(2, 5))
                    ->affiliated($patrimonial->member_id)
                    ->create()
                    ->each(function ($affiliated) {
                        if (!$affiliated->is_exempt) {
                            $subscription = SubscriptionMember::factory()
                                ->create(['member_id' => $affiliated->member_id]);

                            SubscriptionMonthMember::factory()
                                ->count(rand(1, 6))
                                ->create(['subscription_member_id' => $subscription->id]);
                        }
                    });

                // Assinatura do patrimonial
                $subscription = SubscriptionMember::factory()
                    ->create(['member_id' => $patrimonial->member_id]);

                SubscriptionMonthMember::factory()
                    ->count(rand(1, 6))
                    ->create(['subscription_member_id' => $subscription->id]);
            });
    }
}
