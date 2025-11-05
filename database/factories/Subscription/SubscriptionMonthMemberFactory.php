<?php

namespace Database\Factories\Subscription;

use App\Models\Subscription\SubscriptionMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription\SubscriptionMonthMember>
 */
class SubscriptionMonthMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $month = $this->faker->numberBetween(1, 12);
        $year = $this->faker->numberBetween(2020, 2025);
        $expirationDate = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $status = $this->faker->randomElement(['paga', 'pendente', 'vencida']);
        $paidAt = $status === 'paga' ? $this->faker->dateTimeBetween($expirationDate->copy()->subMonth(), $expirationDate) : null;

        return [
            'subscription_member_id' => SubscriptionMember::factory(),
            'status' => $status,
            'paid_at' => $paidAt,
            'expiration_date' => $expirationDate->format('Y-m-d'),
            'value' => $this->faker->randomFloat(2, 50, 200),
            'month' => $month,
            'year' => $year,
            'payment_method' => $status === 'paga' ? $this->faker->randomElement(['pix', 'boleto', 'cartão de crédito']) : null,
            'payment_proof_link' => $status === 'paga' ? $this->faker->url() : null,
        ];
    }
}
