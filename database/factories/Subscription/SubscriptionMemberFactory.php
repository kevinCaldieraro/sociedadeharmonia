<?php

namespace Database\Factories\Subscription;

use App\Models\Member\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription\SubscriptionMember>
 */
class SubscriptionMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $joinDate = $this->faker->dateTimeBetween('-3 years', 'now');
        $lastPaidAt = $this->faker->dateTimeBetween($joinDate, 'now');
        $status = $this->faker->randomElement(['regular', 'irregular', 'desativada']);

        return [
            'member_id' => Member::factory(),
            'join_date' => $joinDate->format('Y-m-d'),
            'last_paid_at' => $lastPaidAt->format('Y-m-d'),
            'status' => $status,
        ];
    }
}
