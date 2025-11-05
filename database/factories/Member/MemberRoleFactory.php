<?php

namespace Database\Factories\Member;

use App\Models\Member\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member\MemberRole>
 */
class MemberRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'patrimonial_member_id' => null,
            'type' => 'patrimonial',
            'relationship' => null,
            'is_exempt' => false,
            'patrimonial_purchase_date' => null,
        ];
    }

    public function patrimonial()
    {
        return $this->state(fn () => [
            'type' => 'patrimonial',
            'patrimonial_member_id' => null,
            'patrimonial_purchase_date' => $this->faker->date(),
            'patrimonial_value' => $this->faker->randomFloat($this->faker->numberBetween(0, 2), 2500, 5000), 
            'is_exempt' => false,
        ]);
    }

    public function spouse($patrimonialId)
    {
        return $this->state(fn () => [
            'type' => 'patrimonial_spouse',
            'patrimonial_member_id' => $patrimonialId,
            'patrimonial_purchase_date' => null,
            'is_exempt' => true,
        ]);
    }

    public function affiliated($patrimonialId)
    {
        return $this->state(fn () => [
            'type' => 'affiliated',
            'patrimonial_member_id' => $patrimonialId,
            'patrimonial_purchase_date' => null,
            'is_exempt' => $this->faker->boolean(),
        ]);
    }
}
